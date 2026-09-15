(function () {
	'use strict';

	if (!window.cbContentEngagement || !window.fetch) return;

	var config = window.cbContentEngagement;
	var summary = document.querySelector('[data-engagement-summary]');
	var feedback = document.querySelector('[data-engagement-feedback]');
	var followButton = document.querySelector('[data-engagement-follow]');

	function request(endpoint, body) {
		return fetch(config.restUrl + endpoint, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': config.nonce
			},
			body: body ? JSON.stringify(body) : '{}'
		}).then(function (response) {
			return response.json().then(function (payload) {
				if (!response.ok) throw new Error(payload.message || 'Unable to update this right now.');
				return payload;
			});
		});
	}

	function updateSummary(metrics) {
		if (!summary || !metrics) return;
		var views = summary.querySelector('[data-engagement-views]');
		var average = summary.querySelector('[data-engagement-average]');
		var count = summary.querySelector('[data-engagement-rating-count]');
		var subscribers = summary.querySelector('[data-engagement-subscribers]');
		if (views) views.textContent = Number(metrics.views || 0).toLocaleString();
		if (average) average.textContent = metrics.rating_count ? Number(metrics.rating_average).toFixed(1) : 'New';
		if (count) count.textContent = metrics.rating_count ? '(' + Number(metrics.rating_count).toLocaleString() + (Number(metrics.rating_count) === 1 ? ' rating)' : ' ratings)') : '(no ratings yet)';
		if (subscribers) subscribers.textContent = Number(metrics.subscribers || 0).toLocaleString();
	}

	request('view').then(updateSummary).catch(function () {
		/* The server already counts uncached page renders; do not distract readers if this background sync fails. */
	});

	document.addEventListener('click', function (event) {
		var ratingButton = event.target.closest('[data-engagement-rating]');
		if (ratingButton) {
			var rating = Number(ratingButton.getAttribute('data-engagement-rating'));
			if (feedback) feedback.textContent = 'Saving your rating…';
			request('rating', { rating: rating }).then(function (metrics) {
				updateSummary(metrics);
				if (feedback) feedback.textContent = 'Thanks — your rating was saved.';
			}).catch(function (error) {
				if (feedback) feedback.textContent = error.message;
			});
		}

		if (event.target.closest('[data-engagement-follow]')) {
			if (!config.loggedIn) {
				window.location.assign(config.loginUrl);
				return;
			}
			if (feedback) feedback.textContent = 'Updating your follow…';
			request('subscription').then(function (payload) {
				updateSummary(payload.metrics);
				if (followButton) followButton.textContent = payload.subscribed ? 'Following article' : 'Follow article';
				if (feedback) feedback.textContent = payload.subscribed ? 'This article is now in your learning list.' : 'You are no longer following this article.';
			}).catch(function (error) {
				if (feedback) feedback.textContent = error.message;
			});
		}
	});
}());
