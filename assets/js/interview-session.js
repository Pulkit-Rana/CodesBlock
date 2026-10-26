(function () {
	'use strict';

	var data = window.cbInterviewData || {};
	var form = document.querySelector('[data-interview-setup]');
	var error = document.querySelector('[data-interview-error]');

	function showError(message) {
		if (!error) return;
		error.textContent = message;
		error.hidden = false;
	}

	if (form) {
		form.querySelectorAll('input[name="track"]').forEach(function (input) {
			input.addEventListener('change', function () {
				form.querySelectorAll('.interview-track-option').forEach(function (option) {
					option.classList.toggle('is-selected', option.contains(input) && input.checked);
				});
			});
		});

		form.addEventListener('submit', function (event) {
			event.preventDefault();
			if (!data.loggedIn) {
				window.location.assign(data.loginUrl || '/wp-login.php');
				return;
			}

			var submit = form.querySelector('[type="submit"]');
			var selectedTrack = form.querySelector('input[name="track"]:checked');
			if (!selectedTrack) {
				showError('Choose an interview track to continue.');
				return;
			}

			if (error) error.hidden = true;
			submit.disabled = true;
			submit.querySelector('span:first-child').textContent = 'Preparing your workspace…';
			window.fetch(data.apiUrl, {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': data.nonce },
				body: JSON.stringify({
					track: selectedTrack.value,
					difficulty: form.querySelector('[name="difficulty"]').value
				})
			}).then(function (response) {
				return response.json().then(function (payload) {
					if (!response.ok) throw new Error(payload.message || 'We could not start your session. Please try again.');
					return payload;
				});
			}).then(function (payload) {
				window.location.assign(payload.url);
			}).catch(function (failure) {
				showError(failure.message || 'We could not start your session. Please try again.');
				submit.disabled = false;
				submit.querySelector('span:first-child').textContent = 'Start my free session';
			});
		});
	}

	var workspace = document.querySelector('[data-interview-session]');
	var draft = document.querySelector('[data-interview-draft]');
	if (!workspace || !draft) return;

	var sessionId = workspace.getAttribute('data-interview-session');
	var storageKey = 'codesblock-interview-draft-' + sessionId;
	var status = document.querySelector('[data-draft-status]');
	var count = document.querySelector('[data-draft-count]');

	function updateWordCount() {
		var words = draft.value.trim() ? draft.value.trim().split(/\s+/).length : 0;
		if (count) count.textContent = words + (words === 1 ? ' word' : ' words');
	}

	try {
		draft.value = window.localStorage.getItem(storageKey) || '';
	} catch (errorReadingDraft) {
		if (status) status.textContent = 'Draft storage unavailable';
	}
	updateWordCount();

	draft.addEventListener('input', function () {
		updateWordCount();
		if (status) status.textContent = 'Saving…';
		try {
			window.localStorage.setItem(storageKey, draft.value);
			if (status) status.textContent = 'Saved on this device';
		} catch (errorSavingDraft) {
			if (status) status.textContent = 'Could not save draft';
		}
	});
})();
