window.UI = (function () {
  let transitionInProgress = false;
  let pageEnterTimeout = null;

  function smoothTransition(url) {
    transitionInProgress = true;

    if (pageEnterTimeout) {
      clearTimeout(pageEnterTimeout);
    }

    document.body.classList.add('saindo-transicao');
    const container = document.querySelector('.container');
    if (!container) {
      window.location.href = url;
      return;
    }

    setTimeout(() => {
      container.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
      container.style.opacity = '0';
      container.style.transform = 'scale(0.96)';
    }, 200);

    setTimeout(() => {
      window.location.href = url;
    }, 900);
  }

  function initPageEnter() {
    if (transitionInProgress) return;

    document.body.classList.remove('saindo-transicao');

    const container = document.querySelector('.container');
    if (!container) return;

    container.style.transition = 'none';
    container.style.opacity = '0';
    container.style.transform = 'scale(0.95)';

    void container.offsetHeight;

    setTimeout(() => {
      container.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
      container.style.opacity = '1';
      container.style.transform = 'scale(1)';
    }, 50);
  }

  function initPageTransitions() {
    window.addEventListener('load', () => {
      initPageEnter();
      transitionInProgress = false;
    });

    window.addEventListener('pageshow', event => {
      if (event.persisted) {
        transitionInProgress = false;
        initPageEnter();
      }
    });
  }

  return {
    smoothTransition,
    initPageEnter,
    initPageTransitions
  };
})();
