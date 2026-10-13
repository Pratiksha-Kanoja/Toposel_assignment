(() => {
	document.querySelectorAll('.tpa-app').forEach((app) => {
		app.querySelectorAll('[data-tpa-toggle-group]').forEach((group) => {
			group.addEventListener('click', (event) => {
				const btn = event.target.closest('button');
				if (!btn || !group.contains(btn)) return;
				group.querySelectorAll('button').forEach((b) => b.classList.toggle('is-active', b === btn));
			});
		});

		app.addEventListener('click', (event) => {
			const heart = event.target.closest('.tpa-heart');
			if (!heart) return;
			const active = heart.classList.toggle('is-active');
			heart.setAttribute('aria-pressed', String(active));
		});

		const menuBtn = app.querySelector('[data-tpa-menu]');
		const overlay = app.querySelector('[data-tpa-close]');
		if (!menuBtn || !overlay) return;

		const setMenuOpen = (open) => {
			app.classList.toggle('is-menu-open', open);
			overlay.hidden = !open;
			menuBtn.setAttribute('aria-expanded', String(open));
		};

		menuBtn.addEventListener('click', () => setMenuOpen(!app.classList.contains('is-menu-open')));
		overlay.addEventListener('click', () => setMenuOpen(false));
		document.addEventListener('keydown', (event) => {
			if (event.key === 'Escape') setMenuOpen(false);
		});
	});
})();
