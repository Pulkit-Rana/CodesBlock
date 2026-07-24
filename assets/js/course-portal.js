(function () {
	'use strict';

	/* ─────────────────────────────────────────────────────────
	   INTERACTIVE COURSE GUIDE — fixed slide-in panel
	   ───────────────────────────────────────────────────────── */

	var tutor = {
		panel:       document.getElementById('ai-tutor-panel'),
		toggleBtn:   document.getElementById('ai-tutor-toggle'),
		inlineBtn:   document.getElementById('open-ai-tutor-inline'),
		closeBtn:    document.getElementById('ai-tutor-close'),
		tabs:        document.querySelectorAll('.ai-tutor-tab'),
		tabContents: document.querySelectorAll('.ai-tutor-tab-content'),
		messagesEl:  document.getElementById('ai-chat-messages'),
		inputEl:     document.getElementById('ai-chat-input'),
		sendBtn:     document.getElementById('ai-chat-send'),
		chips:       document.querySelectorAll('.ai-suggestion-chip'),
	};

	/* --- Course guide knowledge base (generated from page data) --- */
	var courseData = window.cbPortalData || {};
	var summary    = courseData.summary  || 'This course is designed to help you build practical skills step by step. Review the syllabus and learning outcomes on this page for the full outline.';
	var faqs       = courseData.faqs     || [];
	var courseTitle = courseData.title   || 'this course';

	/* Keyword-based page guidance. */
	var knowledgeBase = [
		{ keys: ['price', 'cost', 'how much', 'free', 'enroll', 'subscribe', 'buy'],
		  reply: 'Use the Enroll button on this page to review the currently available membership choices and final checkout price.' },
		{ keys: ['syllabus', 'curriculum', 'module', 'chapter', 'lesson'],
		  reply: 'Scroll down to the Course Syllabus section on this page to see the full breakdown of modules and lessons. Select a module header to expand it.' },
		{ keys: ['prerequisite', 'need to know', 'level', 'beginner', 'experience', 'requirement'],
		  reply: 'Check the Level badge in the course header. Beginner courses start from scratch, Intermediate assumes some experience, and Advanced targets senior engineers.' },
		{ keys: ['duration', 'how long', 'hours', 'weeks', 'time'],
		  reply: 'The estimated course duration is listed in the course header. Your account keeps saved progress so you can continue later.' },
		{ keys: ['guide', 'help', 'how does this work'],
		  reply: 'This interactive guide uses the published course summary, syllabus, and instructor-provided FAQ answers to help you navigate the page.' },
		{ keys: ['language', 'python', 'javascript', 'java', 'typescript'],
		  reply: 'The course examples use languages relevant to the topic. All conceptual patterns are language-agnostic — you can apply them in Python, JavaScript, Java, or whatever your interviewer expects.' },
		{ keys: ['summary', 'overview', 'about', 'what is'],
		  reply: summary },
	];

	/* FAQ answers are rendered as text, never as markup. */
	faqs.forEach(function (faq) {
		if (!faq || typeof faq.q !== 'string' || typeof faq.a !== 'string') { return; }
		knowledgeBase.push({
			keys: faq.q.toLowerCase().split(/\W+/).filter(function (w) { return w.length > 3; }),
			reply: faq.a,
		});
	});

	var fallbacks = [
		'Great question! Check the Syllabus and What You’ll Learn sections below. Is there a specific topic you want me to explain?',
		'I’m here to help you navigate ' + courseTitle + '. Try asking about the course outline, pricing, prerequisites, or duration.',
		'That needs a little more context. Try asking “What will I learn?”, “How long is this course?”, or “What’s included?”',
	];

	function getBotReply(msg) {
		var lower = msg.toLowerCase();
		var best  = null;
		var bestScore = 0;

		knowledgeBase.forEach(function (entry) {
			var score = 0;
			entry.keys.forEach(function (k) {
				if (lower.indexOf(k) !== -1) { score++; }
			});
			if (score > bestScore) { bestScore = score; best = entry; }
		});

		if (best && bestScore > 0) { return best.reply; }
		return fallbacks[Math.floor(Math.random() * fallbacks.length)];
	}

	/* --- Panel open / close --- */
	var lastTutorFocus = null;
	var closeTimer = null;

	function openPanel() {
		if (!tutor.panel) return;
		if (closeTimer) {
			window.clearTimeout(closeTimer);
			closeTimer = null;
		}
		lastTutorFocus = document.activeElement;
		tutor.panel.hidden = false;
		tutor.panel.removeAttribute('inert');
		tutor.panel.setAttribute('aria-hidden', 'false');
		window.requestAnimationFrame(function () {
			tutor.panel.classList.add('is-open');
			if (tutor.closeBtn) { tutor.closeBtn.focus(); }
		});
		if (tutor.toggleBtn) { tutor.toggleBtn.setAttribute('aria-expanded', 'true'); }
		document.body.style.overflow = 'hidden';
	}

	function closePanel() {
		if (!tutor.panel) return;
		tutor.panel.classList.remove('is-open');
		tutor.panel.setAttribute('aria-hidden', 'true');
		tutor.panel.setAttribute('inert', '');
		if (tutor.toggleBtn) { tutor.toggleBtn.setAttribute('aria-expanded', 'false'); }
		document.body.style.overflow = '';
		closeTimer = window.setTimeout(function () {
			tutor.panel.hidden = true;
			closeTimer = null;
		}, 260);
		if (lastTutorFocus && typeof lastTutorFocus.focus === 'function') {
			lastTutorFocus.focus();
		}
	}

	if (tutor.toggleBtn) {
		tutor.toggleBtn.addEventListener('click', function () {
			if (tutor.panel && tutor.panel.classList.contains('is-open')) {
				closePanel();
			} else {
				openPanel();
			}
		});
	}

	if (tutor.inlineBtn) {
		tutor.inlineBtn.addEventListener('click', openPanel);
	}

	if (tutor.closeBtn) { tutor.closeBtn.addEventListener('click', closePanel); }

	/* Close on overlay click (outside panel) */
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && tutor.panel && !tutor.panel.hidden) { closePanel(); }
	});

	/* --- Tabs --- */
	function setTutorTab(target) {
		tutor.tabs.forEach(function (tab) {
			var active = tab.dataset.tab === target;
			tab.classList.toggle('is-active', active);
			tab.setAttribute('aria-selected', active ? 'true' : 'false');
			tab.setAttribute('tabindex', active ? '0' : '-1');
		});
		tutor.tabContents.forEach(function (content) {
			var active = content.id === 'ai-tab-' + target;
			content.classList.toggle('is-active', active);
			content.hidden = !active;
		});
	}

	tutor.tabs.forEach(function (tab) {
		tab.addEventListener('click', function () {
			setTutorTab(tab.dataset.tab);
		});
	});

	/* --- Chat messaging --- */
	function appendMessage(message, role) {
		if (!tutor.messagesEl) return;
		var isAi   = role === 'ai';
		var wrap   = document.createElement('div');
		wrap.className = 'chat-msg chat-msg-' + role;

		var avatar = document.createElement('div');
		avatar.className = 'chat-avatar';
		avatar.textContent = isAi ? 'AI' : 'Me';

		var bubble = document.createElement('div');
		bubble.className = 'chat-bubble';
		bubble.textContent = message;

		if (isAi) {
			wrap.appendChild(avatar);
			wrap.appendChild(bubble);
		} else {
			wrap.appendChild(bubble);
			wrap.appendChild(avatar);
		}

		tutor.messagesEl.appendChild(wrap);
		tutor.messagesEl.scrollTop = tutor.messagesEl.scrollHeight;
	}

	function showTyping() {
		if (!tutor.messagesEl) return null;
		var wrap   = document.createElement('div');
		wrap.className = 'chat-msg chat-msg-ai chat-typing-wrap';

		var avatar = document.createElement('div');
		avatar.className = 'chat-avatar';
		avatar.textContent = 'AI';

		var typing = document.createElement('div');
		typing.className = 'chat-bubble';
		var typingDots = document.createElement('div');
		typingDots.className = 'chat-typing';
		for (var i = 0; i < 3; i++) {
			typingDots.appendChild(document.createElement('span'));
		}
		typing.appendChild(typingDots);

		wrap.appendChild(avatar);
		wrap.appendChild(typing);
		tutor.messagesEl.appendChild(wrap);
		tutor.messagesEl.scrollTop = tutor.messagesEl.scrollHeight;
		return wrap;
	}

	function sendMessage(msg) {
		if (!msg.trim()) return;
		appendMessage(msg, 'user');
		if (tutor.inputEl) { tutor.inputEl.value = ''; }

		var typingEl = showTyping();
		var delay = 600 + Math.random() * 800;

		setTimeout(function () {
			if (typingEl && typingEl.parentNode) {
				typingEl.parentNode.removeChild(typingEl);
			}
			appendMessage(getBotReply(msg), 'ai');

			/* Switch to chat tab if on summary */
			var chatTab = document.querySelector('[data-tab="chat"]');
			if (chatTab) { chatTab.click(); }
		}, delay);
	}

	if (tutor.sendBtn) {
		tutor.sendBtn.addEventListener('click', function () {
			sendMessage(tutor.inputEl ? tutor.inputEl.value : '');
		});
	}

	if (tutor.inputEl) {
		tutor.inputEl.addEventListener('keydown', function (e) {
			if (e.key === 'Enter' && !e.shiftKey) {
				e.preventDefault();
				sendMessage(tutor.inputEl.value);
			}
		});
	}

	/* Suggestion chips */
	tutor.chips.forEach(function (chip) {
		chip.addEventListener('click', function () {
			sendMessage(chip.textContent.trim());
			/* Switch to chat tab */
			var chatTab = document.querySelector('[data-tab="chat"]');
			if (chatTab) { chatTab.click(); }
			openPanel();
		});
	});

	/* ─────────────────────────────────────────────────────────
	   SYLLABUS ACCORDION
	   ───────────────────────────────────────────────────────── */

	document.querySelectorAll('.syllabus-module-header').forEach(function (header) {
		header.addEventListener('click', function () {
			var mod = header.closest('.syllabus-module');
			if (!mod) return;
			var isOpen = mod.classList.toggle('is-open');
			header.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
		});
	});

	/* Keep server-rendered default state synchronized. */
	var firstModule = document.querySelector('.syllabus-module');
	if (firstModule) {
		firstModule.classList.add('is-open');
		var firstHeader = firstModule.querySelector('.syllabus-module-header');
		if (firstHeader) { firstHeader.setAttribute('aria-expanded', 'true'); }
	}

	/* ─────────────────────────────────────────────────────────
	   COURSE FILTER PILLS (archive page)
	   ───────────────────────────────────────────────────────── */

	var filterPills = document.querySelectorAll('.filter-pill[data-filter]');
	var courseCards = document.querySelectorAll('.course-card-v2[data-level]');

	function applyCourseFilter(filter, updateUrl) {
		var matched = 0;

		filterPills.forEach(function (pill) {
			var active = pill.dataset.filter === filter;
			pill.classList.toggle('is-active', active);
			pill.setAttribute('aria-pressed', active ? 'true' : 'false');
		});

		courseCards.forEach(function (card) {
			var level = (card.dataset.level || '').toLowerCase();
			var show = filter === 'all' || level === filter;
			card.hidden = !show;
			if (show) { matched++; }
		});

		if (updateUrl && window.history && window.URL) {
			var url = new URL(window.location.href);
			if (filter === 'all') {
				url.searchParams.delete('type');
			} else {
				url.searchParams.set('type', filter);
			}
			window.history.replaceState({}, '', url.toString());
		}

		var grid = document.getElementById('course-grid');
		if (grid) {
			grid.setAttribute('data-visible-courses', String(matched));
		}
		var status = document.getElementById('course-filter-status');
		if (status) {
			status.textContent = matched === 1 ? '1 course shown.' : matched + ' courses shown.';
		}
	}

	filterPills.forEach(function (pill) {
		pill.addEventListener('click', function () {
			applyCourseFilter(pill.dataset.filter, true);
		});
	});

	if (filterPills.length && courseCards.length) {
		var requestedFilter = new URLSearchParams(window.location.search).get('type');
		var validFilter = Array.prototype.some.call(filterPills, function (pill) {
			return pill.dataset.filter === requestedFilter;
		});
		applyCourseFilter(validFilter ? requestedFilter : 'all', false);
	}

}());
