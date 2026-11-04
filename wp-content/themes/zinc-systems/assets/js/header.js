/*** -- Site header: mega menu, region dropdown and mobile menu -- ***/
(function () {
	const header = document.querySelector('[data-site-header]');
	if (!header) {
		return;
	}

	const desktop = window.matchMedia('(min-width: 1100px)');
	const hoverCapable = window.matchMedia('(hover: hover)');
	const dropdowns = Array.from(header.querySelectorAll('[data-dropdown]'));
	const menuToggle = header.querySelector('[data-menu-toggle]');
	const menuToggleLabel = header.querySelector('[data-menu-toggle-label]');
	const CLOSE_DELAY = 150;

	function setOpen(dropdown, open) {
		const toggle = dropdown.querySelector('[data-dropdown-toggle]');
		dropdown.classList.toggle('is-open', open);
		if (toggle) {
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		}
		header.classList.toggle('has-mega-open', !!header.querySelector('.has-mega-menu.is-open'));
	}

	function closeAll(except) {
		dropdowns.forEach(function (dropdown) {
			if (dropdown !== except && dropdown.classList.contains('is-open')) {
				setOpen(dropdown, false);
			}
		});
	}

	dropdowns.forEach(function (dropdown) {
		const toggle = dropdown.querySelector('[data-dropdown-toggle]');
		let closeTimer;

		// Desktop: open on hover with a short grace period so the pointer can travel into the panel
		dropdown.addEventListener('mouseenter', function () {
			if (!desktop.matches || !hoverCapable.matches) {
				return;
			}
			clearTimeout(closeTimer);
			closeAll(dropdown);
			setOpen(dropdown, true);
		});

		dropdown.addEventListener('mouseleave', function () {
			if (!desktop.matches || !hoverCapable.matches) {
				return;
			}
			closeTimer = setTimeout(function () {
				setOpen(dropdown, false);
			}, CLOSE_DELAY);
		});

		// Click / keyboard / touch
		if (toggle) {
			toggle.addEventListener('click', function (e) {
				// A mouse click on a dropdown already opened by hover shouldn't immediately close it
				if (desktop.matches && hoverCapable.matches && e.detail > 0 && dropdown.classList.contains('is-open')) {
					return;
				}
				const open = !dropdown.classList.contains('is-open');
				if (desktop.matches) {
					closeAll(dropdown);
				}
				setOpen(dropdown, open);
			});
		}

		// Close when keyboard focus leaves the item on desktop
		dropdown.addEventListener('focusout', function (e) {
			if (desktop.matches && e.relatedTarget && !dropdown.contains(e.relatedTarget)) {
				setOpen(dropdown, false);
			}
		});
	});

	// Mobile menu
	function setMenuOpen(open) {
		header.classList.toggle('is-menu-open', open);
		document.documentElement.classList.toggle('has-menu-open', open);
		if (menuToggle) {
			menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		}
		if (menuToggleLabel) {
			menuToggleLabel.textContent = open ? 'Close menu' : 'Open menu';
		}
	}

	if (menuToggle) {
		menuToggle.addEventListener('click', function () {
			setMenuOpen(!header.classList.contains('is-menu-open'));
		});
	}

	// Escape closes the open dropdown (returning focus to its toggle), then the mobile menu
	document.addEventListener('keydown', function (e) {
		if (e.key !== 'Escape') {
			return;
		}
		const open = header.querySelector('[data-dropdown].is-open');
		if (open) {
			setOpen(open, false);
			const toggle = open.querySelector('[data-dropdown-toggle]');
			if (toggle) {
				toggle.focus();
			}
		} else if (header.classList.contains('is-menu-open')) {
			setMenuOpen(false);
			menuToggle.focus();
		}
	});

	// Clicking outside the header closes any open desktop dropdown
	document.addEventListener('click', function (e) {
		if (desktop.matches && !header.contains(e.target)) {
			closeAll();
		}
	});

	// Language switcher (GTranslate handles the translation itself via its a[data-gt-lang] links)
	const languageSwitcher = header.querySelector('[data-language-switcher]');
	if (languageSwitcher) {
		const label = languageSwitcher.querySelector('[data-language-label]');
		const defaultLang = languageSwitcher.getAttribute('data-default-lang');

		function setLanguageLabel(lang) {
			label.textContent = !lang || lang === defaultLang ? label.getAttribute('data-default-label') : lang.split('-')[0].toUpperCase();
		}

		// GTranslate remembers the chosen language in localStorage
		try {
			const stored = JSON.parse(localStorage.getItem('__GT_TRANSLATE_LANGS'));
			if (stored && typeof stored.tgtLang === 'string') {
				setLanguageLabel(stored.tgtLang);
			}
		} catch (e) {}

		languageSwitcher.querySelectorAll('a[data-gt-lang]').forEach(function (link) {
			link.addEventListener('click', function () {
				setLanguageLabel(link.getAttribute('data-gt-lang'));
				setOpen(languageSwitcher, false);
				languageSwitcher.querySelector('[data-dropdown-toggle]').focus();
			});
		});
	}

	// Expose the header height so the mobile panel can sit beneath it
	function setHeaderHeight() {
		document.documentElement.style.setProperty('--site-header-height', header.offsetHeight + 'px');
	}
	setHeaderHeight();
	window.addEventListener('resize', setHeaderHeight);

	// Reset state when switching between mobile and desktop layouts
	desktop.addEventListener('change', function () {
		closeAll();
		setMenuOpen(false);
	});
})();
/*** -- END Site header -- ***/
