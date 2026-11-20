/**
 * Course landing page: curriculum explorer tabs, the AI tutor demo and the
 * mobile purchase bar.
 *
 * @package CodesBlock
 */
(function () {
	'use strict';

	/* ── Curriculum explorer: list of modules beside one panel ── */
	var explorer = document.querySelector('[data-cbl-explorer]');
	if (explorer) {
		var nav = explorer.querySelector('.cbl-explorer-nav');
		var tabs = Array.prototype.slice.call(explorer.querySelectorAll('[role="tab"]'));
		var select = function (tab, focus) {
			tabs.forEach(function (other) {
				var on = other === tab;
				var panel = document.getElementById(other.getAttribute('aria-controls'));
				other.setAttribute('aria-selected', on ? 'true' : 'false');
				other.tabIndex = on ? 0 : -1;
				if (panel) panel.hidden = !on;
			});
			if (focus) tab.focus();
			if (nav && nav.scrollWidth > nav.clientWidth) {
				nav.scrollTo({ left: tab.offsetLeft - 8, behavior: 'smooth' });
			}
		};

		if (nav && tabs.length) {
			nav.hidden = false;
			explorer.classList.add('is-ready');
			select(tabs.filter(function (t) { return t.getAttribute('aria-selected') === 'true'; })[0] || tabs[0], false);

			tabs.forEach(function (tab, i) {
				tab.addEventListener('click', function () { select(tab, false); });
				tab.addEventListener('keydown', function (event) {
					var next = null;
					if (event.key === 'ArrowDown' || event.key === 'ArrowRight') next = tabs[(i + 1) % tabs.length];
					if (event.key === 'ArrowUp' || event.key === 'ArrowLeft') next = tabs[(i - 1 + tabs.length) % tabs.length];
					if (event.key === 'Home') next = tabs[0];
					if (event.key === 'End') next = tabs[tabs.length - 1];
					if (next) {
						event.preventDefault();
						select(next, true);
					}
				});
			});
		}
	}

	/* ── AI tutor demo: text / voice switch, plays once in view ── */
	var demo = document.querySelector('[data-cbl-ai-demo]');
	if (demo) {
		var modeButtons = demo.querySelectorAll('[data-cbl-ai-mode]');
		var panes = demo.querySelectorAll('[data-cbl-ai-pane]');
		var replay = function () {
			demo.classList.remove('is-live');
			void demo.offsetWidth; // Restart the CSS entrance animation.
			demo.classList.add('is-live');
		};

		modeButtons.forEach(function (button) {
			button.addEventListener('click', function () {
				var mode = button.getAttribute('data-cbl-ai-mode');
				demo.setAttribute('data-mode', mode);
				modeButtons.forEach(function (other) {
					other.setAttribute('aria-pressed', other === button ? 'true' : 'false');
				});
				panes.forEach(function (pane) {
					pane.hidden = pane.getAttribute('data-cbl-ai-pane') !== mode;
				});
				replay();
			});
		});

		if ('IntersectionObserver' in window) {
			var seen = new IntersectionObserver(function (entries) {
				if (entries[0].isIntersecting) {
					replay();
					seen.disconnect();
				}
			}, { threshold: 0.35 });
			seen.observe(demo);
		}
	}

	/* ── Mobile purchase bar ── */
	var bar = document.querySelector('[data-cbl-mobile-bar]');
	var card = document.getElementById('cbl-enrol');
	var hero = document.querySelector('.cbl-hero');
	if (!bar || !card || !('IntersectionObserver' in window)) {
		return;
	}

	var visible = { card: true, hero: true };
	var update = function () {
		bar.classList.toggle('is-visible', !visible.card && !visible.hero);
	};
	var observer = new IntersectionObserver(function (entries) {
		entries.forEach(function (entry) {
			visible[entry.target === card ? 'card' : 'hero'] = entry.isIntersecting;
		});
		update();
	});
	observer.observe(card);
	if (hero) {
		observer.observe(hero);
	}
}());
