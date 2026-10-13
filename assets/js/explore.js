/**
 * BuyMore Explore page interactions:
 * drawer navigation, category/search/sort filtering, favourites carousel, likes.
 */
(function () {
	'use strict';

	var LIKES_KEY = 'tpaLikes';

	function readLikes() {
		try {
			return JSON.parse(window.localStorage.getItem(LIKES_KEY)) || [];
		} catch (e) {
			return [];
		}
	}

	function writeLikes(list) {
		try {
			window.localStorage.setItem(LIKES_KEY, JSON.stringify(list));
		} catch (e) {
			/* Storage unavailable (private mode); likes stay in memory. */
		}
	}

	function initNav(root) {
		var openBtn = root.querySelector('[data-tpa-nav-open]');
		var sidebar = root.querySelector('.tpa-sidebar');
		var overlay = root.querySelector('.tpa-overlay');
		if (!openBtn || !sidebar) {
			return;
		}

		function setOpen(open) {
			root.classList.toggle('is-nav-open', open);
			document.documentElement.classList.toggle('tpa-lock', open);
			openBtn.setAttribute('aria-expanded', String(open));
			if (overlay) {
				overlay.hidden = !open;
			}
			if (open) {
				var first = sidebar.querySelector('a, button');
				if (first) {
					first.focus();
				}
			} else {
				openBtn.focus();
			}
		}

		openBtn.addEventListener('click', function () {
			setOpen(true);
		});
		root.querySelectorAll('[data-tpa-nav-close]').forEach(function (el) {
			el.addEventListener('click', function () {
				setOpen(false);
			});
		});
		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && root.classList.contains('is-nav-open')) {
				setOpen(false);
			}
		});
	}

	function initCatalog(root) {
		var grid = root.querySelector('[data-tpa-products]');
		var track = root.querySelector('[data-tpa-favs-track]');
		var gridItems = grid ? Array.prototype.slice.call(grid.querySelectorAll('[data-tpa-product]')) : [];
		var favItems = track ? Array.prototype.slice.call(track.querySelectorAll('[data-tpa-product]')) : [];
		var limit = grid ? parseInt(grid.getAttribute('data-limit'), 10) || 2 : 0;
		var state = { category: 'all', query: '', sort: 'featured' };

		function price(el) {
			return parseFloat(el.getAttribute('data-price')) || 0;
		}

		function matches(el) {
			var categories = (el.getAttribute('data-cats') || '').split(' ');
			var inCategory = state.category === 'all' || categories.indexOf(state.category) !== -1;
			var inSearch = !state.query || (el.getAttribute('data-title') || '').indexOf(state.query) !== -1;
			return inCategory && inSearch;
		}

		function sorted(items) {
			return items.slice().sort(function (a, b) {
				if (state.sort === 'price-asc') {
					return price(a) - price(b);
				}
				if (state.sort === 'price-desc') {
					return price(b) - price(a);
				}
				return a.getAttribute('data-order') - b.getAttribute('data-order');
			});
		}

		function render(container, items, max) {
			var shown = 0;
			sorted(items).forEach(function (el) {
				var visible = matches(el) && shown < max;
				if (visible) {
					shown++;
				}
				el.hidden = !visible;
				container.appendChild(el);
			});
			var scope = container.closest('[data-tpa-favs]') || container;
			var empty = scope.querySelector('[data-tpa-empty]');
			if (empty) {
				empty.hidden = shown > 0;
			}
		}

		function apply() {
			if (grid) {
				render(grid, gridItems, limit);
			}
			if (track) {
				render(track, favItems, Infinity);
				track.scrollLeft = 0;
				root.dispatchEvent(new CustomEvent('tpa:favourites-change'));
			}
		}

		root.querySelectorAll('[data-tpa-filter]').forEach(function (btn, index, all) {
			btn.addEventListener('click', function () {
				all.forEach(function (other) {
					var active = other === btn;
					other.classList.toggle('is-active', active);
					other.setAttribute('aria-pressed', String(active));
				});
				state.category = btn.getAttribute('data-tpa-filter');
				apply();
			});
		});

		var search = root.querySelector('[data-tpa-search]');
		var searchToggle = root.querySelector('[data-tpa-search-toggle]');
		if (search && searchToggle) {
			search.addEventListener('input', function () {
				state.query = search.value.trim().toLowerCase();
				apply();
			});
			searchToggle.addEventListener('click', function () {
				var open = !root.classList.contains('is-search-open');
				root.classList.toggle('is-search-open', open);
				searchToggle.setAttribute('aria-expanded', String(open));
				if (open) {
					search.focus();
				} else if (search.value) {
					search.value = '';
					state.query = '';
					apply();
				}
			});
		}

		var sortToggle = root.querySelector('[data-tpa-sort-toggle]');
		var sortMenu = root.querySelector('[data-tpa-sort-menu]');
		if (sortToggle && sortMenu) {
			var setMenu = function (open) {
				sortMenu.hidden = !open;
				sortToggle.setAttribute('aria-expanded', String(open));
			};
			sortToggle.addEventListener('click', function () {
				setMenu(sortMenu.hidden);
			});
			sortMenu.querySelectorAll('[data-tpa-sort]').forEach(function (btn, index, all) {
				btn.addEventListener('click', function () {
					all.forEach(function (other) {
						other.setAttribute('aria-pressed', String(other === btn));
					});
					state.sort = btn.getAttribute('data-tpa-sort');
					setMenu(false);
					apply();
				});
			});
			document.addEventListener('click', function (event) {
				if (!sortMenu.hidden && !event.target.closest('.tpa-sort')) {
					setMenu(false);
				}
			});
			document.addEventListener('keydown', function (event) {
				if (event.key === 'Escape' && !sortMenu.hidden) {
					setMenu(false);
					sortToggle.focus();
				}
			});
		}
	}

	function initFavourites(root) {
		var wrap = root.querySelector('[data-tpa-favs]');
		if (!wrap) {
			return;
		}
		var track = wrap.querySelector('[data-tpa-favs-track]');
		var prev = wrap.querySelector('[data-tpa-favs-prev]');
		var next = wrap.querySelector('[data-tpa-favs-next]');

		function step() {
			var item = track.querySelector('[data-tpa-product]:not([hidden])');
			var gap = parseFloat(window.getComputedStyle(track).columnGap) || 0;
			return item ? item.getBoundingClientRect().width + gap : track.clientWidth;
		}

		function update() {
			var max = track.scrollWidth - track.clientWidth - 1;
			prev.disabled = track.scrollLeft <= 0;
			next.disabled = track.scrollLeft >= max;
		}

		prev.addEventListener('click', function () {
			track.scrollBy({ left: -step() });
		});
		next.addEventListener('click', function () {
			track.scrollBy({ left: step() });
		});
		track.addEventListener('scroll', update, { passive: true });
		window.addEventListener('resize', update);
		root.addEventListener('tpa:favourites-change', update);
		update();
	}

	function initLikes(root) {
		var likes = readLikes();
		root.querySelectorAll('[data-tpa-like]').forEach(function (btn) {
			var id = btn.getAttribute('data-tpa-like');
			btn.setAttribute('aria-pressed', String(likes.indexOf(id) !== -1));
			btn.addEventListener('click', function () {
				var index = likes.indexOf(id);
				if (index === -1) {
					likes.push(id);
				} else {
					likes.splice(index, 1);
				}
				btn.setAttribute('aria-pressed', String(index === -1));
				writeLikes(likes);
			});
		});
	}

	document.querySelectorAll('[data-tpa-explore]').forEach(function (root) {
		initNav(root);
		initCatalog(root);
		initFavourites(root);
		initLikes(root);
	});
})();
