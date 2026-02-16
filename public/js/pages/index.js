// Código específico da home (index.html)

document.addEventListener('DOMContentLoaded', () => {
  // 1) Login SUAP (hover + redirecionamento)
  if (window.SuapAuth) {
    SuapAuth.initLoginLink('loginLink');
  }

  // 2) Transições de entrada/saída
  if (window.UI) {
    UI.initPageTransitions();
  }

  // 3) Carousel
  if (window.Carousel) {
    Carousel.setup();
  }

  // 4) Botões do carrossel usam as funções globais (para o onclick do HTML)
  window.moveSlide = direction => {
    Carousel.moveSlide(direction);
  };

  window.currentSlide = index => {
    Carousel.currentSlide(index);
  };

  // 5) Cards clicáveis (navegação suave)
  const cards = document.querySelectorAll('.card');
  cards.forEach(card => {
    card.style.cursor = 'pointer';

    card.addEventListener('click', () => {
      const targetPage = card.getAttribute('data-page');
      if (targetPage && window.UI) {
        UI.smoothTransition(targetPage);
      } else if (targetPage) {
        window.location.href = targetPage;
      }
    });

    card.addEventListener('keydown', e => {
      if (e.key === 'Enter' || e.code === 'Space') {
        e.preventDefault();
        const targetPage = card.getAttribute('data-page');
        if (targetPage && window.UI) {
          UI.smoothTransition(targetPage);
        } else if (targetPage) {
          window.location.href = targetPage;
        }
      }
    });
  });

  // 6) Logo volta ao topo (na home mantém o comportamento antigo)
  const logo = document.querySelector('.logo');
  if (logo) {
    logo.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // 7) Botão "Sobre Nós" com transição suave
  const aboutButton = document.querySelector('.about-button-link');
  if (aboutButton) {
    aboutButton.addEventListener('click', e => {
      e.preventDefault();
      const url = '/Paginas/sobrenos.html';
      if (window.UI) {
        UI.smoothTransition(url);
      } else {
        window.location.href = url;
      }
    });
  }
});
