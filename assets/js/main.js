(function () {

	/* Top announcement: campaign-aware dismissal and privacy-safe events. */
	var promoBar = document.querySelector('[data-promo-campaign]');
	if (promoBar) {
		var promoCampaign = promoBar.getAttribute('data-promo-campaign') || 'announcement';
		var promoStorageKey = 'codesblock_promo_' + promoCampaign;
		var promoDismissedUntil = 0;

		try {
			promoDismissedUntil = parseInt(window.localStorage.getItem(promoStorageKey), 10) || 0;
		} catch (storageError) {
			promoDismissedUntil = 0;
		}

		function trackPromo(eventName) {
			var eventData = {
				event: 'codesblock_promo_' + eventName,
				campaign: promoCampaign
			};
			window.dispatchEvent(new CustomEvent('codesblock:promo', { detail: eventData }));
			if (Array.isArray(window.dataLayer)) window.dataLayer.push(eventData);
			if (typeof window.clarity === 'function') window.clarity('event', 'promo_' + eventName + '_' + promoCampaign);
		}

		if (promoDismissedUntil > Date.now()) {
			promoBar.hidden = true;
		} else {
			trackPromo('impression');
		}

		var promoCta = promoBar.querySelector('[data-promo-action="click"]');
		if (promoCta) {
			promoCta.addEventListener('click', function () { trackPromo('click'); });
		}

		var promoDismiss = promoBar.querySelector('[data-promo-dismiss]');
		if (promoDismiss) {
			promoDismiss.addEventListener('click', function () {
				var sevenDays = 7 * 24 * 60 * 60 * 1000;
				try {
					window.localStorage.setItem(promoStorageKey, String(Date.now() + sevenDays));
				} catch (storageError) {
					// The bar still dismisses for this page when storage is unavailable.
				}
				promoBar.hidden = true;
				trackPromo('dismiss');
			});
		}
	}

	/* ── Mobile nav toggle ── */
	var toggle = document.querySelector('[data-nav-toggle]');
	var nav    = document.querySelector('[data-primary-nav]');

	if (toggle && nav) {
		toggle.addEventListener('click', function () {
			var isOpen = nav.classList.toggle('is-open');
			toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
		});

		nav.addEventListener('click', function (event) {
			if (event.target.tagName === 'A') {
				nav.classList.remove('is-open');
				toggle.setAttribute('aria-expanded', 'false');
			}
		});
	}

	/* ── Liquid-glass scrolled header ── */
	var siteHeader  = document.querySelector('.site-header');
	var scrollThreshold = 80;
	var ticking = false;

	function onScroll() {
		if (!ticking) {
			requestAnimationFrame(function () {
				if (!siteHeader) { ticking = false; return; }
				if (window.scrollY > scrollThreshold) {
					siteHeader.classList.add('is-scrolled');
				} else {
					siteHeader.classList.remove('is-scrolled');
				}
				ticking = false;
			});
			ticking = true;
		}
	}

	window.addEventListener('scroll', onScroll, { passive: true });
	onScroll(); // run once on load

	/* ── Learning-strip liquid ripple on hover ── */
	var stripItems = document.querySelectorAll('.strip-item');

	stripItems.forEach(function (item) {
		item.addEventListener('mousemove', function (e) {
			var rect = item.getBoundingClientRect();
			var x = ((e.clientX - rect.left) / rect.width * 100).toFixed(1);
			var y = ((e.clientY - rect.top)  / rect.height * 100).toFixed(1);
			item.style.setProperty('--ripple-x', x + '%');
			item.style.setProperty('--ripple-y', y + '%');
		});

		item.addEventListener('mouseleave', function () {
			item.style.removeProperty('--ripple-x');
			item.style.removeProperty('--ripple-y');
		});
	});

}());
