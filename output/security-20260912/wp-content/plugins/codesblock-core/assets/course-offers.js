(() => {
  'use strict';

  const offer = document.querySelector('.cb-course-offer[data-offer-id]');
  if (!offer) return;

  // On phones and tablets the reading surface is more valuable than a
  // promotional flyout. Course links remain available in the page itself.
  if (window.matchMedia('(max-width: 980px)').matches) {
    offer.remove();
    return;
  }

  const closeButton = offer.querySelector('.cb-course-offer__close');
  let delayFinished = false;
  let engaged = false;
  let shown = false;
  let retryTimer = 0;

  const anotherDialogIsOpen = () =>
    document.body.classList.contains('cb-modal-open') ||
    Boolean(document.querySelector('.cb-member-overlay:not([hidden])'));

  const removeEngagementListeners = () => {
    window.removeEventListener('scroll', noteEngagement);
    document.removeEventListener('pointerdown', noteEngagement);
  };

  const show = () => {
    if (shown || !delayFinished || !engaged || document.visibilityState !== 'visible' || anotherDialogIsOpen()) return;
    shown = true;
    window.clearInterval(retryTimer);
    removeEngagementListeners();
    offer.hidden = false;
    window.requestAnimationFrame(() => offer.classList.add('is-visible'));
  };

  function noteEngagement(event) {
    if (event.type === 'keydown' && event.key === 'Escape' && shown) {
      dismiss();
      return;
    }
    if (event.type === 'scroll') {
      const pageHeight = Math.max(document.documentElement.scrollHeight - window.innerHeight, 1);
      if (window.scrollY / pageHeight < 0.08 && window.scrollY < 160) return;
    }
    engaged = true;
    show();
  }

  const dismiss = () => {
    window.clearInterval(retryTimer);
    removeEngagementListeners();
    document.removeEventListener('keydown', noteEngagement);
    offer.classList.remove('is-visible');
    offer.classList.add('is-leaving');
    window.setTimeout(() => offer.remove(), 360);
  };

  closeButton?.addEventListener('click', dismiss);
  window.addEventListener('scroll', noteEngagement, { passive: true });
  document.addEventListener('pointerdown', noteEngagement, { passive: true });
  document.addEventListener('keydown', noteEngagement);
  document.addEventListener('visibilitychange', show);
  document.addEventListener('click', () => window.setTimeout(show, 0));

  window.setTimeout(() => {
    delayFinished = true;
    show();
  }, 2200);

  // Readers who do not scroll still receive one non-blocking impression.
  window.setTimeout(() => {
    engaged = true;
    show();
  }, 5200);

  // If account or purchase UI was open, retry only after it is closed.
  retryTimer = window.setInterval(show, 1000);
})();
