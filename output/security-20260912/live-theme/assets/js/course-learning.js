(function () {
	'use strict';

	var maps = document.querySelectorAll('[data-course-map]');
	if (!maps.length) return;

	function storageKey(map) {
		return 'codesblock_course_map_' + (map.getAttribute('data-course-id') || 'course');
	}

	function prefersDesktopLayout() {
		return window.matchMedia('(min-width: 1100px)').matches;
	}

	maps.forEach(function (map) {
		var panel = map.querySelector('.cb-course-map__panel');
		var toggle = map.querySelector('[data-course-map-toggle]');
		var closeControls = map.querySelectorAll('[data-course-map-close]');
		var activeLesson = map.querySelector('.cb-course-map__lesson.is-current');
		if (!panel || !toggle) return;

		function updatePanelTop() {
			if (!prefersDesktopLayout()) {
				map.style.removeProperty('--cb-map-top');
				return;
			}

			var header = document.querySelector('.site-header');
			var top = header ? Math.max(12, Math.ceil(header.getBoundingClientRect().bottom + 10)) : 88;
			map.style.setProperty('--cb-map-top', top + 'px');
		}

		function readSavedState() {
			try {
				return window.localStorage.getItem(storageKey(map));
			} catch (storageError) {
				return null;
			}
		}

		function saveState(isOpen) {
			try {
				window.localStorage.setItem(storageKey(map), isOpen ? 'open' : 'closed');
			} catch (storageError) {
				// The map remains fully usable when storage is unavailable.
			}
		}

		function setOpen(isOpen, persist, moveFocus) {
			map.classList.toggle('is-open', isOpen);
			document.body.classList.toggle('cb-course-map-open', isOpen);
			toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
			panel.setAttribute('aria-hidden', isOpen ? 'false' : 'true');

			if (isOpen) {
				panel.removeAttribute('inert');
				if (activeLesson) {
					window.requestAnimationFrame(function () {
						activeLesson.scrollIntoView({ block: 'center' });
					});
				}
			} else {
				panel.setAttribute('inert', '');
				if (moveFocus) toggle.focus();
			}

			if (persist) saveState(isOpen);
		}

		toggle.addEventListener('click', function () {
			setOpen(!map.classList.contains('is-open'), true, false);
		});

		closeControls.forEach(function (control) {
			control.addEventListener('click', function () {
				setOpen(false, true, control.classList.contains('cb-course-map__close'));
			});
		});

		map.querySelectorAll('[data-course-map-module-toggle]').forEach(function (moduleToggle) {
			moduleToggle.addEventListener('click', function () {
				var panelId = moduleToggle.getAttribute('aria-controls');
				var modulePanel = panelId ? document.getElementById(panelId) : null;
				if (!modulePanel) return;

				var willOpen = moduleToggle.getAttribute('aria-expanded') !== 'true';
				moduleToggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
				modulePanel.hidden = !willOpen;
			});
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && map.classList.contains('is-open')) {
				setOpen(false, true, true);
			}
		});

		var savedState = readSavedState();
		var defaultOpen = map.getAttribute('data-default-open') === 'true' && prefersDesktopLayout();
		var panelTopTicking = false;
		updatePanelTop();
		window.addEventListener('resize', updatePanelTop, { passive: true });
		window.addEventListener('scroll', function () {
			if (panelTopTicking) return;
			panelTopTicking = true;
			window.requestAnimationFrame(function () {
				updatePanelTop();
				panelTopTicking = false;
			});
		}, { passive: true });
		setOpen(prefersDesktopLayout() && (savedState ? savedState === 'open' : defaultOpen), false, false);
	});
}());
