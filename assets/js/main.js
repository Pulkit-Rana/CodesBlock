(function () {

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

	/* ── Role pills in Interview Guides ── */
	var roleDescriptions = {
		senior:  'Architecture, tradeoffs, ownership, debugging judgment, and system-level thinking.',
		ai:      'Prompting, agents, LLM evaluation, product thinking, and coding confidently with AI tools.',
		manager: 'People leadership, delivery stories, conflict resolution, and decision-making quality.'
	};

	var rolePills = document.querySelectorAll('.role-pill');
	var roleDesc  = document.getElementById('role-desc');

	rolePills.forEach(function (pill) {
		pill.addEventListener('click', function () {
			rolePills.forEach(function (p) { p.classList.remove('active'); });
			pill.classList.add('active');

			if (roleDesc) {
				roleDesc.style.opacity = '0';
				setTimeout(function () {
					roleDesc.textContent = roleDescriptions[pill.dataset.role] || '';
					roleDesc.style.opacity = '1';
				}, 180);
			}
		});
	});

}());
