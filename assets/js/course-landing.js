/**
 * Course landing page: curriculum "Expand all" and the mobile purchase bar.
 *
 * @package CodesBlock
 */
(function () {
	'use strict';

	var toggle = document.querySelector('[data-cbl-toggle-all]');
	if (toggle) {
		toggle.addEventListener('click', function () {
			var modules = document.querySelectorAll('.cbl-modules details');
			var expand = toggle.getAttribute('aria-pressed') !== 'true';
			modules.forEach(function (module) {
				module.open = expand;
			});
			toggle.setAttribute('aria-pressed', expand ? 'true' : 'false');
			toggle.textContent = expand ? toggle.dataset.labelCollapse || 'Collapse all' : toggle.dataset.labelExpand || 'Expand all';
		});
	}

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
