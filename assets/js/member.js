(function () {
	'use strict';

	var config = window.cbMemberData || {};
	var authOverlay = document.getElementById('member-overlay');
	var purchaseOverlay = document.getElementById('paywall-overlay');
	var activeOverlay = null;
	var lastFocused = null;

	function setView(view, focusField) {
		if (!authOverlay) return;
		view = view === 'signin' ? 'signin' : 'register';

		authOverlay.querySelectorAll('[data-member-view]').forEach(function (tab) {
			var active = tab.getAttribute('data-member-view') === view;
			tab.classList.toggle('is-active', active);
			tab.setAttribute('aria-selected', active ? 'true' : 'false');
			tab.setAttribute('tabindex', active ? '0' : '-1');
		});

		authOverlay.querySelectorAll('[data-member-panel]').forEach(function (panel) {
			var active = panel.getAttribute('data-member-panel') === view;
			panel.classList.toggle('is-active', active);
			panel.hidden = !active;
		});

		if (focusField) {
			var field = authOverlay.querySelector('[data-member-panel="' + view + '"] input:not([type="hidden"]):not(.cb-honeypot)');
			if (field) field.focus();
		}
	}

	function hideOverlay(overlay) {
		if (!overlay) return;
		overlay.hidden = true;
		overlay.setAttribute('aria-hidden', 'true');
	}

	function openOverlay(overlay, view) {
		if (!overlay) return false;
		var modal = overlay.querySelector('.cb-member-modal');
		if (!modal) return false;

		if (!activeOverlay) {
			lastFocused = document.activeElement;
		} else if (activeOverlay !== overlay) {
			hideOverlay(activeOverlay);
		}

		activeOverlay = overlay;
		overlay.hidden = false;
		overlay.setAttribute('aria-hidden', 'false');
		document.body.classList.add('cb-modal-open');
		if (overlay === authOverlay) setView(view || 'register', false);
		modal.scrollTop = 0;
		window.requestAnimationFrame(function () {
			var initialFocus = overlay === authOverlay
				? modal.querySelector('[role="tab"][aria-selected="true"]')
				: modal.querySelector('.cb-member-close');
			(initialFocus || modal).focus();
		});
		return true;
	}

	function closeOverlay(overlay) {
		if (!overlay) return;
		hideOverlay(overlay);
		if (activeOverlay === overlay) activeOverlay = null;

		if ((!authOverlay || authOverlay.hidden) && (!purchaseOverlay || purchaseOverlay.hidden)) {
			document.body.classList.remove('cb-modal-open');
		}

		var focusTarget = lastFocused;
		lastFocused = null;
		if (focusTarget && document.contains(focusTarget) && typeof focusTarget.focus === 'function') {
			focusTarget.focus();
		}
	}

	function bindTriggers(selector, overlay, defaultView) {
		document.querySelectorAll(selector).forEach(function (trigger) {
			if (overlay) {
				trigger.setAttribute('aria-haspopup', 'dialog');
				trigger.setAttribute('aria-controls', overlay.id);
				if (trigger.tagName === 'A' && (trigger.getAttribute('href') || '').charAt(0) === '#') {
					trigger.setAttribute('href', '#' + overlay.id);
				}
			}

			trigger.addEventListener('click', function (event) {
				if (!overlay) return;
				event.preventDefault();
				openOverlay(overlay, trigger.getAttribute('data-member-view') || defaultView);
			});
		});
	}

	function bindOverlay(overlay) {
		if (!overlay) return;
		var modal = overlay.querySelector('.cb-member-modal');

		overlay.querySelectorAll('[data-member-close]').forEach(function (button) {
			button.addEventListener('click', function () {
				closeOverlay(overlay);
			});
		});

		if (overlay === authOverlay) {
			var tabs = Array.prototype.slice.call(overlay.querySelectorAll('[data-member-view]'));
			tabs.forEach(function (tab, index) {
				tab.addEventListener('click', function () {
					setView(tab.getAttribute('data-member-view'), true);
				});
				tab.addEventListener('keydown', function (event) {
					var nextIndex = null;
					if (event.key === 'ArrowRight') nextIndex = (index + 1) % tabs.length;
					if (event.key === 'ArrowLeft') nextIndex = (index - 1 + tabs.length) % tabs.length;
					if (event.key === 'Home') nextIndex = 0;
					if (event.key === 'End') nextIndex = tabs.length - 1;
					if (nextIndex === null) return;

					event.preventDefault();
					setView(tabs[nextIndex].getAttribute('data-member-view'), false);
					tabs[nextIndex].focus();
				});
			});

			overlay.querySelectorAll('[data-focus-register]').forEach(function (button) {
				button.addEventListener('click', function () { setView('register', true); });
			});
		}

		overlay.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') {
				closeOverlay(overlay);
				return;
			}

			if (event.key !== 'Tab' || !modal) return;
			var focusable = Array.prototype.slice.call(modal.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), [tabindex]:not([tabindex="-1"])'))
				.filter(function (element) {
					return !element.closest('[hidden]') && element.getAttribute('tabindex') !== '-1';
				});
			if (!focusable.length) return;
			var first = focusable[0];
			var last = focusable[focusable.length - 1];
			if (event.shiftKey && (document.activeElement === first || document.activeElement === modal)) {
				event.preventDefault();
				last.focus();
			} else if (!event.shiftKey && document.activeElement === modal) {
				event.preventDefault();
				first.focus();
			} else if (!event.shiftKey && document.activeElement === last) {
				event.preventDefault();
				first.focus();
			}
		});
	}

	bindTriggers('.js-open-member', authOverlay, 'register');
	bindTriggers('.js-open-paywall', purchaseOverlay, '');
	bindOverlay(authOverlay);
	bindOverlay(purchaseOverlay);

	/* Keep access CTAs reliable when a course template or later script adds them after this file runs. */
	document.addEventListener('click', function (event) {
		var target = event.target;
		var trigger = target && target.closest ? target.closest('.js-open-member, .js-open-paywall') : null;
		if (!trigger) return;

		var overlay = trigger.classList.contains('js-open-member') ? authOverlay : purchaseOverlay;
		if (!overlay || !overlay.hidden) return;

		event.preventDefault();
		openOverlay(overlay, trigger.getAttribute('data-member-view') || (overlay === authOverlay ? 'register' : ''));
	});

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

	function redirectSameOrigin(url) {
		if (!url) return;
		try {
			var target = new URL(url, window.location.origin);
			if (target.origin === window.location.origin) {
				window.location.assign(target.href);
			}
		} catch (error) {
			// Ignore malformed redirect values and leave the success message visible.
		}
	}

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
					if (payload.view && authOverlay) setView(payload.view, false);
					var targetPanel = payload.view && authOverlay ? authOverlay.querySelector('[data-member-panel="' + payload.view + '"]') : form;
					var targetFeedback = targetPanel ? targetPanel.querySelector('.cb-form-feedback') : feedback;
					if (targetFeedback) targetFeedback.textContent = payload.message || 'Please check your details and try again.';
					return;
				}

				if (feedback) {
					feedback.textContent = payload.message || 'Done.';
					feedback.classList.add('is-success');
				}
				if (action === 'cbcommerce_newsletter') form.reset();
				if (payload.redirect) window.setTimeout(function () { redirectSameOrigin(payload.redirect); }, 550);
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

	if (window.location.hash === '#join' || window.location.hash === '#member-overlay') {
		openOverlay(authOverlay, 'register');
	} else if (window.location.hash === '#signin') {
		openOverlay(authOverlay, 'signin');
	} else if (window.location.hash === '#paywall-overlay') {
		openOverlay(purchaseOverlay, '');
	}
}());
