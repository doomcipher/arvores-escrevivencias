// Depende de: settings.js e client.js (SuapClient)

window.SuapAuth = (function () {
  const suap = new SuapClient(SUAP_URL, CLIENT_ID, REDIRECT_URI, SCOPE);
  suap.init();
  window.suap = suap;

  function initLoginLink(loginLinkId = 'loginLink') {
    const loginLink = document.getElementById(loginLinkId);
    if (!loginLink) return;

    let hoverTimeout;

    loginLink.addEventListener('mouseenter', () => {
      clearTimeout(hoverTimeout);
      hoverTimeout = setTimeout(() => {
        loginLink.classList.add('show-hover-text', 'active-hover');
      }, 1000);
    });

    loginLink.addEventListener('mouseleave', () => {
      clearTimeout(hoverTimeout);
      loginLink.classList.remove('show-hover-text', 'active-hover');
    });

    loginLink.onclick = (e) => {
      e.preventDefault();
      window.location.href = suap.getLoginURL();
    };

    loginLink.setAttribute('href', suap.getLoginURL());
  }

  return { initLoginLink };
})();

SuapAuth.initLoginLink('loginLink');
SuapAuth.initLoginLink('loginComments1');
