(function () {

	/* Top announcement: campaign-aware dismissal and privacy-safe events. */
	var promoBar = document.querySelector('[data-promo-campaign]');
	if (promoBar) {
		var promoCampaign = promoBar.getAttribute('data-promo-campaign') || 'announcement';
		var promoStorageKey = 'codesblock_promo_' + promoCampaign;
		var promoDismissal = promoBar.getAttribute('data-promo-dismissal') || '168';
		var promoExpires = parseInt(promoBar.getAttribute('data-promo-expires'), 10) || 0;
		var promoPreview = promoBar.getAttribute('data-promo-preview') === '1';
		var promoCountdownTimer = null;

		function trackPromo(eventName) {
			if (promoPreview) return;
			var eventData = {
				event: 'codesblock_promo_' + eventName,
				campaign: promoCampaign
			};
			window.dispatchEvent(new CustomEvent('codesblock:promo', { detail: eventData }));
			if (Array.isArray(window.dataLayer)) window.dataLayer.push(eventData);
			if (typeof window.clarity === 'function') window.clarity('event', 'promo_' + eventName + '_' + promoCampaign);
		}

		function hidePromo(animate) {
			if (promoCountdownTimer) window.clearInterval(promoCountdownTimer);
			if (!animate) {
				promoBar.hidden = true;
				return;
			}
			promoBar.classList.add('is-leaving');
			window.setTimeout(function () { promoBar.hidden = true; }, 200);
		}

		function isPromoDismissed() {
			if (promoPreview) return false;
			try {
				if (promoDismissal === 'session') {
					return window.sessionStorage.getItem(promoStorageKey) === 'dismissed';
				}
				var dismissedUntil = parseInt(window.localStorage.getItem(promoStorageKey), 10) || 0;
				if (dismissedUntil > Date.now()) return true;
				if (dismissedUntil) window.localStorage.removeItem(promoStorageKey);
			} catch (storageError) {
				return false;
			}
			return false;
		}

		function updatePromoCountdown() {
			var countdown = promoBar.querySelector('[data-promo-countdown]');
			if (!countdown || !promoExpires) return;
			var remaining = Math.max(0, Math.floor((promoExpires - Date.now()) / 1000));
			if (remaining <= 0) {
				hidePromo(true);
				trackPromo('expired');
				return;
			}
			var days = Math.floor(remaining / 86400);
			var hours = Math.floor((remaining % 86400) / 3600);
			var minutes = Math.floor((remaining % 3600) / 60);
			var seconds = remaining % 60;
			var pad = function (value) { return String(value).padStart(2, '0'); };
			countdown.textContent = days > 0
				? days + 'd ' + pad(hours) + 'h ' + pad(minutes) + 'm'
				: pad(hours) + 'h ' + pad(minutes) + 'm ' + pad(seconds) + 's';
		}

		var hiddenOnThisDevice = promoBar.classList.contains('promo-hide-mobile') && window.matchMedia('(max-width: 640px)').matches;
		if ((!promoPreview && promoExpires && promoExpires <= Date.now()) || isPromoDismissed() || hiddenOnThisDevice) {
			hidePromo(false);
		} else {
			updatePromoCountdown();
			if (promoBar.querySelector('[data-promo-countdown]')) {
				promoCountdownTimer = window.setInterval(updatePromoCountdown, 1000);
			}
			trackPromo('impression');
		}

		var promoCta = promoBar.querySelector('[data-promo-action="click"]');
		if (promoCta) {
			promoCta.addEventListener('click', function () { trackPromo('click'); });
		}

		var promoDismiss = promoBar.querySelector('[data-promo-dismiss]');
		if (promoDismiss) {
			promoDismiss.addEventListener('click', function () {
				if (!promoPreview) {
					try {
						if (promoDismissal === 'session') {
							window.sessionStorage.setItem(promoStorageKey, 'dismissed');
						} else {
							var dismissalHours = parseInt(promoDismissal, 10) || 168;
							window.localStorage.setItem(promoStorageKey, String(Date.now() + (dismissalHours * 60 * 60 * 1000)));
						}
					} catch (storageError) {
						// The bar still dismisses for this page when storage is unavailable.
					}
				}
				hidePromo(true);
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
			toggle.setAttribute('aria-label', isOpen ? 'Close navigation' : 'Open navigation');
		});

		nav.addEventListener('click', function (event) {
			if (event.target.tagName === 'A') {
				nav.classList.remove('is-open');
				toggle.setAttribute('aria-expanded', 'false');
				toggle.setAttribute('aria-label', 'Open navigation');
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && nav.classList.contains('is-open')) {
				nav.classList.remove('is-open');
				toggle.setAttribute('aria-expanded', 'false');
				toggle.setAttribute('aria-label', 'Open navigation');
				toggle.focus();
			}
		});

		document.addEventListener('click', function (event) {
			if (!nav.classList.contains('is-open') || nav.contains(event.target) || toggle.contains(event.target)) return;
			nav.classList.remove('is-open');
			toggle.setAttribute('aria-expanded', 'false');
			toggle.setAttribute('aria-label', 'Open navigation');
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

	/* Subtle long-page wayfinding: a thin progress line and one-time section reveals. */
	var scrollProgress = document.createElement('div');
	scrollProgress.className = 'cb-scroll-progress';
	scrollProgress.setAttribute('aria-hidden', 'true');
	document.body.appendChild(scrollProgress);

	var progressTicking = false;
	function updateScrollProgress() {
		var scrollRange = Math.max(1, document.documentElement.scrollHeight - window.innerHeight);
		var progress = Math.min(1, Math.max(0, window.scrollY / scrollRange));
		scrollProgress.style.transform = 'scaleX(' + progress + ')';
		progressTicking = false;
	}

	window.addEventListener('scroll', function () {
		if (progressTicking) return;
		progressTicking = true;
		window.requestAnimationFrame(updateScrollProgress);
	}, { passive: true });
	updateScrollProgress();

	var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var revealItems = document.querySelectorAll('body.home main > .section > .container, body.home .learning-strip .strip-item, body.single-course .course-section');
	if (!prefersReducedMotion && revealItems.length && 'IntersectionObserver' in window) {
		document.documentElement.classList.add('cb-motion-ready');
		var revealObserver = new IntersectionObserver(function (entries, observer) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) return;
				entry.target.classList.add('is-visible');
				observer.unobserve(entry.target);
			});
		}, { rootMargin: '0px 0px -8% 0px', threshold: .08 });

		revealItems.forEach(function (item) {
			item.classList.add('cb-reveal-item');
			revealObserver.observe(item);
		});
	}

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
