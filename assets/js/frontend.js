(() => {
	const CART = 'tpa-cart';
	const FAVS = 'tpa-favs';

	const read = (key) => {
		try {
			const value = JSON.parse(sessionStorage.getItem(key) || '[]');
			return Array.isArray(value) ? value : [];
		} catch (error) {
			return [];
		}
	};

	const write = (key, value) => {
		try {
			sessionStorage.setItem(key, JSON.stringify(value));
		} catch (error) {
			/* Ignore private-mode storage failures. */
		}
	};

	const esc = (value) => String(value).replace(/[&<>"']/g, (ch) => ({
		'&': '&amp;',
		'<': '&lt;',
		'>': '&gt;',
		'"': '&quot;',
		"'": '&#39;',
	}[ch]));

	const money = (price) => {
		const amount = Number(String(price).replace(/[^0-9.]/g, ''));
		return Number.isFinite(amount) ? amount : NaN;
	};

	document.querySelectorAll('.tpa-app').forEach((app) => {
		const overlay = app.querySelector('[data-tpa-close]');
		const menuBtn = app.querySelector('[data-tpa-menu]');
		const cart = app.querySelector('[data-tpa-cart]');
		const cartBtn = app.querySelector('[data-tpa-cart-open]');
		const dialog = app.querySelector('[data-tpa-dialog]');
		const dialogForm = app.querySelector('[data-tpa-dialog-form]');
		const toast = app.querySelector('[data-tpa-toast]');
		const filterWrap = app.querySelector('.tpa-filter');
		const filterPanel = app.querySelector('[data-tpa-filter-panel]');
		const filterBtn = app.querySelector('[data-tpa-filters]');
		const searchInput = app.querySelector('[data-tpa-search-input]');
		const searchBtn = app.querySelector('[data-tpa-search]');
		let toastTimer = 0;
		let onDialogSubmit = null;
		let favStep = 0;

		function showToast(message) {
			if (!toast) return;
			toast.textContent = message;
			toast.hidden = false;
			window.clearTimeout(toastTimer);
			toastTimer = window.setTimeout(() => {
				toast.hidden = true;
			}, 2400);
		}

		function syncOverlay() {
			if (!overlay) return;
			overlay.hidden = !(app.classList.contains('is-menu-open') || app.classList.contains('is-cart-open'));
		}

		function setMenuOpen(open) {
			app.classList.toggle('is-menu-open', open);
			if (menuBtn) menuBtn.setAttribute('aria-expanded', String(open));
			if (open) setCartOpen(false);
			syncOverlay();
		}

		function setCartOpen(open) {
			if (!cart) return;
			cart.hidden = !open;
			app.classList.toggle('is-cart-open', open);
			if (cartBtn) cartBtn.setAttribute('aria-expanded', String(open));
			if (open) {
				app.classList.remove('is-menu-open');
				if (menuBtn) menuBtn.setAttribute('aria-expanded', 'false');
			}
			syncOverlay();
		}

		function setFiltersOpen(open) {
			if (!filterPanel || !filterBtn) return;
			filterPanel.hidden = !open;
			filterBtn.setAttribute('aria-expanded', String(open));
		}

		function setSearchOpen(open) {
			if (!searchInput || !searchBtn) return;
			searchInput.hidden = !open;
			searchBtn.setAttribute('aria-expanded', String(open));
			searchBtn.setAttribute('aria-label', open ? 'Close search' : 'Search');
			if (open) {
				searchInput.focus();
				return;
			}
			if (searchInput.value !== '') {
				searchInput.value = '';
				applyVisibility();
			}
		}

		function applyVisibility() {
			const category = app.dataset.category || 'all';
			const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
			app.classList.toggle('is-searching', query !== '');
			let visible = 0;
			app.querySelectorAll('.tpa-grid > .tpa-card').forEach((card) => {
				const cardCategory = card.dataset.category || 'all';
				const categoryOk = category === 'all' || cardCategory === 'all' || cardCategory === category;
				const textOk = query === '' || card.textContent.toLowerCase().includes(query);
				const show = categoryOk && textOk;
				card.hidden = !show;
				if (show) visible += 1;
			});
			const empty = app.querySelector('[data-tpa-empty]');
			if (empty) empty.hidden = visible !== 0;
		}

		function setCategory(category) {
			app.dataset.category = category;
			app.querySelectorAll('[data-tpa-category]').forEach((btn) => {
				const on = btn.dataset.tpaCategory === category;
				btn.classList.toggle('is-active', on);
				if (btn.hasAttribute('aria-pressed')) btn.setAttribute('aria-pressed', String(on));
			});
			applyVisibility();
		}

		function renderCart() {
			const items = read(CART).filter((item) => item && item.title);
			const list = app.querySelector('[data-tpa-cart-list]');
			const empty = app.querySelector('[data-tpa-cart-empty]');
			const foot = app.querySelector('[data-tpa-cart-foot]');
			const total = app.querySelector('[data-tpa-cart-total]');
			const badge = app.querySelector('[data-tpa-cart-count]');
			const count = items.reduce((sum, item) => sum + Number(item.qty || 0), 0);
			if (badge) {
				badge.textContent = String(count);
				badge.hidden = count === 0;
			}
			if (list) {
				list.innerHTML = items.map((item) => (
					'<li class="tpa-cart__row">'
					+ '<span><strong>' + esc(item.title) + '</strong><span class="tpa-cart__meta">'
					+ esc(item.price) + ' × ' + Number(item.qty) + '</span></span>'
					+ '<button type="button" class="tpa-cart__remove" data-tpa-cart-remove data-title="'
					+ esc(item.title) + '">Remove</button></li>'
				)).join('');
			}
			if (empty) empty.hidden = items.length > 0;
			const amounts = items.map((item) => money(item.price) * Number(item.qty || 0));
			const canTotal = items.length > 0 && amounts.every((amount) => Number.isFinite(amount));
			if (foot) foot.hidden = !canTotal;
			if (total && canTotal) {
				const sum = amounts.reduce((a, b) => a + b, 0);
				const symbol = String(items[0].price).trim().charAt(0) === '$' ? '$' : '';
				total.textContent = symbol + String(Math.round(sum * 100) / 100).replace(/\.0$/, '');
			}
		}

		function addToCart(itemTitle, price) {
			const items = read(CART).filter((item) => item && item.title);
			const found = items.find((item) => item.title === itemTitle);
			if (found) found.qty = Number(found.qty || 0) + 1;
			else items.push({ title: itemTitle, price, qty: 1 });
			write(CART, items);
			renderCart();
			setCartOpen(true);
			showToast(itemTitle + ' added to cart');
		}

		function openDialog({ title: dialogTitle, body, submitLabel, onSubmit }) {
			if (!dialog || !dialogForm) return;
			dialog.querySelector('[data-tpa-dialog-title]').textContent = dialogTitle;
			dialog.querySelector('[data-tpa-dialog-body]').innerHTML = body;
			const submit = dialog.querySelector('[data-tpa-dialog-submit]');
			const showSubmit = Boolean(submitLabel);
			if (submit) {
				submit.hidden = !showSubmit;
				submit.disabled = !showSubmit;
				if (showSubmit) submit.textContent = submitLabel;
			}
			onDialogSubmit = onSubmit || null;
			if (typeof dialog.showModal === 'function') dialog.showModal();
			else dialog.setAttribute('open', '');
			const field = dialogForm.querySelector('input, textarea');
			if (field) field.focus();
		}

		function swapFavs(direction) {
			const items = app.querySelector('.tpa-favs__items');
			if (!items) return;
			favStep = (favStep + direction + 2) % 2;
			items.classList.toggle('is-swapped', favStep === 1);
		}

		function restoreFavs() {
			const saved = new Set(read(FAVS).map(String));
			app.querySelectorAll('.tpa-heart').forEach((heart) => {
				const on = saved.has(heart.dataset.title || '');
				heart.classList.toggle('is-active', on);
				heart.setAttribute('aria-pressed', String(on));
			});
		}

		app.querySelectorAll('[data-tpa-toggle-group]').forEach((group) => {
			group.addEventListener('click', (event) => {
				const btn = event.target.closest('button');
				if (!btn || !group.contains(btn)) return;
				group.querySelectorAll('button').forEach((item) => item.classList.toggle('is-active', item === btn));
				if (btn.dataset.tpaView) {
					app.dataset.view = btn.dataset.tpaView;
					showToast(btn.textContent.trim());
				}
			});
		});

		if (searchInput) searchInput.addEventListener('input', applyVisibility);

		if (dialogForm) {
			dialogForm.addEventListener('submit', (event) => {
				event.preventDefault();
				if (onDialogSubmit && onDialogSubmit() === false) return;
				if (dialog && typeof dialog.close === 'function') dialog.close();
			});
		}

		const dialogClose = app.querySelector('[data-tpa-dialog-close]');
		if (dialogClose && dialog) {
			dialogClose.addEventListener('click', () => {
				if (typeof dialog.close === 'function') dialog.close();
				else dialog.removeAttribute('open');
			});
		}

		app.addEventListener('click', (event) => {
			const heart = event.target.closest('.tpa-heart');
			if (heart && app.contains(heart)) {
				const active = heart.classList.toggle('is-active');
				const itemTitle = heart.dataset.title || 'Item';
				const favs = read(FAVS).map(String).filter((item) => item !== itemTitle);
				if (active) favs.push(itemTitle);
				write(FAVS, favs);
				heart.setAttribute('aria-pressed', String(active));
				heart.setAttribute('aria-label', active ? 'Remove from favourites' : 'Add to favourites');
				showToast(active ? itemTitle + ' saved to favourites' : itemTitle + ' removed from favourites');
				return;
			}

			if (event.target.closest('[data-tpa-menu]')) {
				setMenuOpen(!app.classList.contains('is-menu-open'));
				return;
			}

			if (event.target.closest('[data-tpa-close]')) {
				setMenuOpen(false);
				setCartOpen(false);
				return;
			}

			if (event.target.closest('[data-tpa-cart-open]')) {
				setCartOpen(Boolean(cart && cart.hidden));
				return;
			}

			if (event.target.closest('[data-tpa-cart-close]')) {
				setCartOpen(false);
				return;
			}

			const remove = event.target.closest('[data-tpa-cart-remove]');
			if (remove) {
				const itemTitle = remove.dataset.title || '';
				const items = read(CART).flatMap((item) => {
					if (!item || item.title !== itemTitle) return [item];
					if (Number(item.qty) > 1) return [{ ...item, qty: Number(item.qty) - 1 }];
					return [];
				});
				write(CART, items);
				renderCart();
				showToast(itemTitle + ' removed from cart');
				return;
			}

			const add = event.target.closest('[data-tpa-add]');
			if (add) {
				addToCart(add.dataset.title || 'Item', add.dataset.price || '');
				return;
			}

			const nav = event.target.closest('[data-tpa-nav]');
			if (nav) {
				app.querySelectorAll('.tpa-nav__link').forEach((el) => {
					el.classList.remove('is-active');
					el.removeAttribute('aria-current');
				});
				nav.classList.add('is-active');
				nav.setAttribute('aria-current', 'page');
				const label = nav.querySelector('span') ? nav.querySelector('span').textContent.trim() : nav.textContent.trim();
				setMenuOpen(false);
				showToast(label);
				return;
			}

			const quick = event.target.closest('[data-tpa-quick]');
			if (quick) {
				const label = quick.dataset.title || quick.textContent.trim();
				if (quick.dataset.tpaQuick === 'member') {
					openDialog({
						title: label,
						submitLabel: 'Send invite',
						body: '<label class="tpa-field">Email<input type="email" name="email" required placeholder="name@email.com" autocomplete="email"></label>',
						onSubmit: () => {
							if (!dialogForm.reportValidity()) return false;
							showToast('Invite sent to ' + dialogForm.querySelector('[name="email"]').value);
						},
					});
				} else {
					openDialog({
						title: label,
						submitLabel: 'Send request',
						body: '<label class="tpa-field">What do you need?<textarea name="note" required placeholder="Describe the product"></textarea></label>',
						onSubmit: () => {
							if (!dialogForm.reportValidity()) return false;
							showToast('Request sent');
						},
					});
				}
				setMenuOpen(false);
				return;
			}

			const order = event.target.closest('[data-tpa-order]');
			if (order) {
				const itemTitle = order.dataset.title || 'Order';
				openDialog({
					title: itemTitle,
					body: '<p>' + esc(itemTitle) + ' is confirmed and being prepared.</p>',
				});
				return;
			}

			if (event.target.closest('[data-tpa-orders]')) {
				const orders = [...app.querySelectorAll('.tpa-order__title')]
					.map((el) => el.textContent.trim())
					.filter(Boolean);
				openDialog({
					title: 'Last orders',
					body: orders.length
						? '<ul>' + orders.map((item) => '<li>' + esc(item) + '</li>').join('') + '</ul>'
						: '<p>No orders yet.</p>',
				});
				return;
			}

			if (event.target.closest('[data-tpa-filters]')) {
				setFiltersOpen(Boolean(filterPanel && filterPanel.hidden));
				return;
			}

			const categoryBtn = event.target.closest('[data-tpa-category]');
			if (categoryBtn) {
				const category = categoryBtn.dataset.tpaCategory || 'all';
				setCategory(category);
				if (categoryBtn.closest('[data-tpa-filter-panel]')) setFiltersOpen(false);
				const name = categoryBtn.textContent.trim();
				showToast(category === 'all' ? 'Showing all products' : 'Showing ' + name);
				return;
			}

			if (event.target.closest('[data-tpa-search]')) {
				setSearchOpen(Boolean(searchInput && searchInput.hidden));
				return;
			}

			if (event.target.closest('[data-tpa-discount]')) {
				openDialog({
					title: 'Get Discount',
					submitLabel: 'Copy code',
					body: '<p>Use code <strong>WEEKEND50</strong> at checkout for 50% off.</p>',
					onSubmit: () => {
						const done = () => showToast('Code WEEKEND50 copied');
						if (navigator.clipboard && navigator.clipboard.writeText) {
							navigator.clipboard.writeText('WEEKEND50').then(done).catch(done);
						} else {
							done();
						}
					},
				});
				return;
			}

			const detail = event.target.closest('[data-tpa-detail]');
			if (detail) {
				openDialog({
					title: detail.dataset.title || 'Details',
					body: '<p>' + esc(detail.dataset.text || '') + '</p>',
				});
				return;
			}

			if (event.target.closest('[data-tpa-offer]')) {
				openDialog({
					title: 'Avail Offers',
					body: '<ul><li>Free shipping on orders over $50</li><li>Extra 10% off outerwear</li><li>Weekend code WEEKEND50</li></ul>',
				});
				return;
			}

			if (event.target.closest('[data-tpa-fav-prev]')) {
				swapFavs(-1);
				return;
			}

			if (event.target.closest('[data-tpa-fav-next]')) {
				swapFavs(1);
				return;
			}

			if (event.target.closest('[data-tpa-favs-all]')) {
				const saved = read(FAVS).map(String);
				openDialog({
					title: 'Favourites',
					body: saved.length
						? '<ul>' + saved.map((item) => '<li>' + esc(item) + '</li>').join('') + '</ul>'
						: '<p>No favourites yet. Tap a heart on a product to save it.</p>',
				});
				return;
			}

			if (filterPanel && !filterPanel.hidden && filterWrap && !filterWrap.contains(event.target)) {
				setFiltersOpen(false);
			}
		});

		document.addEventListener('keydown', (event) => {
			if (event.key !== 'Escape') return;
			setMenuOpen(false);
			setCartOpen(false);
			setFiltersOpen(false);
			if (searchInput && !searchInput.hidden && searchInput.value === '') setSearchOpen(false);
		});

		restoreFavs();
		renderCart();
		applyVisibility();
	});
})();
