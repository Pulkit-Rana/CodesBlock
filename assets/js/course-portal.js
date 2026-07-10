(function () {
	'use strict';

	/* ─────────────────────────────────────────────────────────
	   AI TUTOR PANEL — dummy responses, fixed slide-in panel
	   ───────────────────────────────────────────────────────── */

	var tutor = {
		panel:       document.getElementById('ai-tutor-panel'),
		toggleBtn:   document.getElementById('ai-tutor-toggle'),
		closeBtn:    document.getElementById('ai-tutor-close'),
		tabs:        document.querySelectorAll('.ai-tutor-tab'),
		tabContents: document.querySelectorAll('.ai-tutor-tab-content'),
		messagesEl:  document.getElementById('ai-chat-messages'),
		inputEl:     document.getElementById('ai-chat-input'),
		sendBtn:     document.getElementById('ai-chat-send'),
		chips:       document.querySelectorAll('.ai-suggestion-chip'),
	};

	/* ─── Helper: escape HTML special chars ──────────────── */
	function escHtml(str) {
		return String(str)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#039;');
	}

	/* --- Dummy knowledge base (generated from page data) --- */
	var courseData = window.cbPortalData || {};
	var summary    = courseData.summary  || 'This is a comprehensive, hands-on course designed to help you build real skills step by step, with an AI tutor available throughout your learning journey.';
	var faqs       = courseData.faqs     || [];
	var courseTitle = courseData.title   || 'this course';

	/* Keyword-based dummy responses */
	var knowledgeBase = [
		{ keys: ['price', 'cost', 'how much', 'free', 'enroll', 'subscribe', 'buy'],
		  reply: 'Click the <strong>Enroll Now</strong> button on the right to see all plans. We have a Free tier, a Pro plan at $9/month (unlimited courses + AI Tutor), and a one-time Lifetime deal at $199.' },
		{ keys: ['syllabus', 'curriculum', 'module', 'chapter', 'lesson'],
		  reply: 'Scroll down to the <strong>Course Syllabus</strong> section on this page to see the full breakdown of modules and lessons — just click a module header to expand it.' },
		{ keys: ['certificate', 'certification', 'badge'],
		  reply: '✅ Yes! You get a <strong>Certificate of Completion</strong> after finishing all modules and the final assessment. You can download it as a PDF and share it directly on LinkedIn.' },
		{ keys: ['refund', 'money back', 'guarantee'],
		  reply: 'We offer a <strong>30-day money-back guarantee</strong>, no questions asked. If you are not satisfied, just contact us within 30 days for a full refund.' },
		{ keys: ['prerequisite', 'need to know', 'level', 'beginner', 'experience', 'requirement'],
		  reply: 'Check the <strong>Level</strong> badge in the course header. Beginner courses start from scratch, Intermediate assumes some experience, and Advanced targets senior engineers.' },
		{ keys: ['duration', 'how long', 'hours', 'weeks', 'time'],
		  reply: 'Course duration is listed in the course header. You have <strong>lifetime access</strong>, so you can go at your own pace — binge it in a week or spread it over months.' },
		{ keys: ['ai tutor', 'ai assistant', 'how does the tutor'],
		  reply: 'I am the AI Tutor 🤖 — I am baked into every course on CodesBlock. You can ask me anything about this course: concepts, doubts, prerequisites, or "explain it simply". On Pro and Lifetime plans I can also review your code.' },
		{ keys: ['language', 'python', 'javascript', 'java', 'typescript'],
		  reply: 'The course examples use languages relevant to the topic. All conceptual patterns are language-agnostic — you can apply them in Python, JavaScript, Java, or whatever your interviewer expects.' },
		{ keys: ['summary', 'overview', 'about', 'what is'],
		  reply: summary },
	];

	/* Also inject FAQ answers into knowledge base — sanitize text content */
	faqs.forEach(function (faq) {
		if (!faq || typeof faq.q !== 'string' || typeof faq.a !== 'string') { return; }
		knowledgeBase.push({
			keys: faq.q.toLowerCase().split(/\W+/).filter(function (w) { return w.length > 3; }),
			/* Escape the FAQ answer so it renders as plain text, not markup */
			reply: escHtml(faq.a),
		});
	});

	var fallbacks = [
		'Great question! I&rsquo;d suggest checking the <strong>Syllabus</strong> and <strong>What You\'ll Learn</strong> sections below. Is there a specific topic you want me to explain?',
		'I\'m here to help! Could you rephrase your question or ask something more specific about ' + escHtml(courseTitle) + '? I know the course content, pricing, prerequisites, and more.',
		'That\'s something I\'ll need more context to answer. Try asking: <em>"What will I learn?"</em>, <em>"How long is this course?"</em>, or <em>"What\'s included?"</em>',
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
	function openPanel() {
		if (!tutor.panel) return;
		tutor.panel.classList.add('is-open');
		if (tutor.toggleBtn) { tutor.toggleBtn.setAttribute('aria-expanded', 'true'); }
		document.body.style.overflow = 'hidden';
	}

	function closePanel() {
		if (!tutor.panel) return;
		tutor.panel.classList.remove('is-open');
		if (tutor.toggleBtn) { tutor.toggleBtn.setAttribute('aria-expanded', 'false'); }
		document.body.style.overflow = '';
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

	if (tutor.closeBtn) { tutor.closeBtn.addEventListener('click', closePanel); }

	/* Close on overlay click (outside panel) */
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') { closePanel(); closePaywall(); }
	});

	/* --- Tabs --- */
	tutor.tabs.forEach(function (tab) {
		tab.addEventListener('click', function () {
			var target = tab.dataset.tab;
			tutor.tabs.forEach(function (t) { t.classList.remove('is-active'); });
			tutor.tabContents.forEach(function (c) { c.classList.remove('is-active'); });
			tab.classList.add('is-active');
			var content = document.getElementById('ai-tab-' + target);
			if (content) { content.classList.add('is-active'); }
		});
	});

	/* --- Chat messaging --- */
	function appendMessage(html, role) {
		if (!tutor.messagesEl) return;
		var isAi   = role === 'ai';
		var wrap   = document.createElement('div');
		wrap.className = 'chat-msg chat-msg-' + role;

		var avatar = document.createElement('div');
		avatar.className = 'chat-avatar';
		avatar.textContent = isAi ? 'AI' : 'Me';

		var bubble = document.createElement('div');
		bubble.className = 'chat-bubble';
		bubble.innerHTML = html;

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
		typing.innerHTML = '<div class="chat-typing"><span></span><span></span><span></span></div>';

		wrap.appendChild(avatar);
		wrap.appendChild(typing);
		tutor.messagesEl.appendChild(wrap);
		tutor.messagesEl.scrollTop = tutor.messagesEl.scrollHeight;
		return wrap;
	}

	function sendMessage(msg) {
		if (!msg.trim()) return;
		appendMessage(escHtml(msg), 'user');
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
	   PAYWALL MODAL
	   ───────────────────────────────────────────────────────── */

	var paywallOverlay = document.getElementById('paywall-overlay');
	var paywallCloseBtn = document.getElementById('paywall-close');

	function openPaywall() {
		if (!paywallOverlay) return;
		paywallOverlay.classList.add('is-open');
		document.body.style.overflow = 'hidden';
	}

	function closePaywall() {
		if (!paywallOverlay) return;
		paywallOverlay.classList.remove('is-open');
		document.body.style.overflow = '';
	}

	/* All "Enroll Now" & "btn-enroll" triggers */
	document.querySelectorAll('.btn-enroll, .js-open-paywall').forEach(function (btn) {
		btn.addEventListener('click', function (e) {
			e.preventDefault();
			openPaywall();
		});
	});

	if (paywallCloseBtn) { paywallCloseBtn.addEventListener('click', closePaywall); }

	/* Close overlay on backdrop click */
	if (paywallOverlay) {
		paywallOverlay.addEventListener('click', function (e) {
			if (e.target === paywallOverlay) { closePaywall(); }
		});
	}

	/* ─────────────────────────────────────────────────────────
	   SYLLABUS ACCORDION
	   ───────────────────────────────────────────────────────── */

	document.querySelectorAll('.syllabus-module-header').forEach(function (header) {
		header.addEventListener('click', function () {
			var mod = header.closest('.syllabus-module');
			if (!mod) return;
			mod.classList.toggle('is-open');
		});
	});

	/* Open first module by default */
	var firstModule = document.querySelector('.syllabus-module');
	if (firstModule) { firstModule.classList.add('is-open'); }

	/* ─────────────────────────────────────────────────────────
	   COURSE FILTER PILLS (archive page)
	   ───────────────────────────────────────────────────────── */

	var filterPills = document.querySelectorAll('.filter-pill[data-filter]');
	var courseCards = document.querySelectorAll('.course-card-v2[data-level]');

	filterPills.forEach(function (pill) {
		pill.addEventListener('click', function () {
			filterPills.forEach(function (p) { p.classList.remove('is-active'); });
			pill.classList.add('is-active');

			var filter = pill.dataset.filter;

			courseCards.forEach(function (card) {
				if (filter === 'all') {
					card.style.display = '';
				} else {
					var level = (card.dataset.level || '').toLowerCase();
					card.style.display = level === filter ? '' : 'none';
				}
			});
		});
	});

	/* ─────────────────────────────────────────────────────────
	   NEWSLETTER FORM — simple feedback (no real submit)
	   ───────────────────────────────────────────────────────── */

	var nlForms = document.querySelectorAll('.newsletter-form-js');

	nlForms.forEach(function (form) {
		form.addEventListener('submit', function (e) {
			/* Only intercept if no real action is set */
			var action = form.getAttribute('action') || '';
			if (action && action !== '#') return; /* let real plugins handle it */

			e.preventDefault();
			var emailInput = form.querySelector('input[type="email"]');
			var btn        = form.querySelector('button[type="submit"]');

			if (emailInput && emailInput.value) {
				if (btn) { btn.textContent = '🎉 You\'re in!'; btn.disabled = true; }
				emailInput.value = '';

				setTimeout(function () {
					if (btn) { btn.textContent = 'Subscribe'; btn.disabled = false; }
				}, 4000);
			}
		});
	});

}());
