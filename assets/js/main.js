(function () {
	var toggle = document.querySelector('[data-nav-toggle]');
	var nav = document.querySelector('[data-primary-nav]');

	if (!toggle || !nav) {
		return;
	}

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
}());
