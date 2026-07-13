(function () {
	'use strict';

	var config = window.cbMemberData || {};
	var overlay = document.getElementById('paywall-overlay');
	var modal = overlay ? overlay.querySelector('.cb-member-modal') : null;
	var lastFocused = null;

	function setView(view, focusField) {
		if (!overlay) return;
		view = view === 'signin' ? 'signin' : 'register';

		overlay.querySelectorAll('[data-member-view]').forEach(function (tab) {
			var active = tab.getAttribute('data-member-view') === view;
			tab.classList.toggle('is-active', active);
			tab.setAttribute('aria-selected', active ? 'true' : 'false');
			tab.setAttribute('tabindex', active ? '0' : '-1');
		});

		overlay.querySelectorAll('[data-member-panel]').forEach(function (panel) {
			var active = panel.getAttribute('data-member-panel') === view;
			panel.classList.toggle('is-active', active);
			panel.hidden = !active;
		});

		if (focusField) {
			var field = overlay.querySelector('[data-member-panel="' + view + '"] input:not([type="hidden"]):not(.cb-honeypot)');
			if (field) field.focus();
		}
	}

	function openMember(view) {
		if (!overlay || !modal) return;
		lastFocused = document.activeElement;
		overlay.hidden = false;
		document.body.classList.add('cb-modal-open');
		setView(view || 'register', false);
		modal.scrollTop = 0;
		window.requestAnimationFrame(function () {
			modal.focus();
		});
	}

	function closeMember() {
		if (!overlay) return;
		overlay.hidden = true;
		document.body.classList.remove('cb-modal-open');
		if (lastFocused && typeof lastFocused.focus === 'function') lastFocused.focus();
	}

	document.querySelectorAll('.js-open-paywall, .js-open-member').forEach(function (trigger) {
		trigger.addEventListener('click', function (event) {
			event.preventDefault();
			openMember(trigger.getAttribute('data-member-view') || 'register');
		});
	});

	if (overlay) {
		overlay.querySelectorAll('[data-member-close]').forEach(function (button) {
			button.addEventListener('click', closeMember);
		});

		overlay.querySelectorAll('[data-member-view]').forEach(function (tab) {
			tab.addEventListener('click', function () {
				setView(tab.getAttribute('data-member-view'), true);
			});
		});

		overlay.querySelectorAll('[data-focus-register]').forEach(function (button) {
			button.addEventListener('click', function () { setView('register', true); });
		});

		overlay.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') {
				closeMember();
				return;
			}

			if (event.key !== 'Tab' || !modal) return;
			var focusable = Array.prototype.slice.call(modal.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), [tabindex]:not([tabindex="-1"])'))
				.filter(function (element) { return !element.closest('[hidden]'); });
			if (!focusable.length) return;
			var first = focusable[0];
			var last = focusable[focusable.length - 1];
			if (event.shiftKey && document.activeElement === first) {
				event.preventDefault();
				last.focus();
			} else if (!event.shiftKey && document.activeElement === last) {
				event.preventDefault();
				first.focus();
			}
		});
	}

	document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
		button.addEventListener('click', function () {
			var input = button.parentNode.querySelector('input');
			if (!input) return;
			var show = input.type === 'password';
			input.type = show ? 'text' : 'password';
			button.textContent = show ? 'Hide' : 'Show';
			button.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
		});
	});

	function submitForm(form, action) {
		var feedback = form.querySelector('.cb-form-feedback');
		var submit = form.querySelector('button[type="submit"]');
		var originalText = submit ? submit.textContent : '';

		if (!form.checkValidity()) {
			form.reportValidity();
			return;
		}

		if (!config.ajaxUrl || !config.nonce) {
			if (feedback) feedback.textContent = 'This form is temporarily unavailable. Please try again later.';
			return;
		}

		var data = new FormData(form);
		data.append('action', action);
		data.append('nonce', config.nonce);
		if (submit) {
			submit.disabled = true;
			submit.textContent = action === 'cbcommerce_login' ? 'Signing in...' : 'Saving...';
		}
		if (feedback) {
			feedback.textContent = '';
			feedback.classList.remove('is-success');
		}

		fetch(config.ajaxUrl, { method: 'POST', body: data, credentials: 'same-origin' })
			.then(function (response) { return response.json(); })
			.then(function (result) {
				var payload = result && result.data ? result.data : {};
				if (!result || !result.success) {
					if (payload.view && overlay) setView(payload.view, false);
					var targetPanel = payload.view && overlay ? overlay.querySelector('[data-member-panel="' + payload.view + '"]') : form;
					var targetFeedback = targetPanel ? targetPanel.querySelector('.cb-form-feedback') : feedback;
					if (targetFeedback) targetFeedback.textContent = payload.message || 'Please check your details and try again.';
					return;
				}

				if (feedback) {
					feedback.textContent = payload.message || 'Done.';
					feedback.classList.add('is-success');
				}
				if (action === 'cbcommerce_newsletter') form.reset();
				if (payload.redirect) window.setTimeout(function () { window.location.assign(payload.redirect); }, 550);
			})
			.catch(function () {
				if (feedback) feedback.textContent = 'We could not reach the server. Please try again.';
			})
			.finally(function () {
				if (submit) {
					submit.disabled = false;
					submit.textContent = originalText;
				}
			});
	}

	document.querySelectorAll('.cb-member-form[data-cb-action]').forEach(function (form) {
		form.addEventListener('submit', function (event) {
			event.preventDefault();
			var action = form.getAttribute('data-cb-action') === 'login' ? 'cbcommerce_login' : 'cbcommerce_register';
			submitForm(form, action);
		});
	});

	document.querySelectorAll('.cb-newsletter-form').forEach(function (form) {
		form.addEventListener('submit', function (event) {
			event.preventDefault();
			submitForm(form, 'cbcommerce_newsletter');
		});
	});

	document.querySelectorAll('[data-course-progress-form]').forEach(function (form) {
		form.addEventListener('submit', function (event) {
			event.preventDefault();
			var feedback = form.querySelector('.cb-form-feedback');
			var submit = form.querySelector('button[type="submit"]');
			var section = form.closest('.cb-course-progress');
			var data = new FormData(form);

			if (!config.ajaxUrl || !config.nonce) {
				if (feedback) feedback.textContent = 'Progress saving is temporarily unavailable.';
				return;
			}

			data.append('action', 'cbcommerce_update_course_progress');
			data.append('nonce', config.nonce);
			if (submit) submit.disabled = true;
			if (feedback) {
				feedback.textContent = '';
				feedback.classList.remove('is-success');
			}

			fetch(config.ajaxUrl, { method: 'POST', body: data, credentials: 'same-origin' })
				.then(function (response) { return response.json(); })
				.then(function (result) {
					var payload = result && result.data ? result.data : {};
					if (!result || !result.success) {
						if (feedback) feedback.textContent = payload.message || 'Progress could not be saved.';
						return;
					}

					var percent = Math.max(0, Math.min(100, parseInt(payload.percent, 10) || 0));
					var fill = section ? section.querySelector('[data-course-progress-fill]') : null;
					var value = section ? section.querySelector('[data-course-progress-value]') : null;
					var bar = section ? section.querySelector('[role="progressbar"]') : null;
					if (fill) fill.style.width = percent + '%';
					if (value) value.textContent = percent + '%';
					if (bar) bar.setAttribute('aria-valuenow', String(percent));
					if (feedback) {
						feedback.textContent = payload.message || 'Learning progress saved.';
						feedback.classList.add('is-success');
					}
				})
				.catch(function () {
					if (feedback) feedback.textContent = 'We could not reach the server. Please try again.';
				})
				.finally(function () {
					if (submit) submit.disabled = false;
				});
		});
	});

	if (window.location.hash === '#join' || window.location.hash === '#paywall-overlay') openMember('register');
}());
