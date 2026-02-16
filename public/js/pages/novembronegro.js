// ============ BOOTSTRAP DA PÁGINA NOVEMBRO NEGRO ============
document.addEventListener('DOMContentLoaded', () => {
  // 1) Login SUAP (usa helper global, se existir)
  if (window.SuapAuth) {
    SuapAuth.initLoginLink('loginLink');
  }

  // 2) Transição de entrada/saída
  const backButton = document.getElementById('backButton');
  const logo = document.getElementById('logo');

  function playExitTransitionAndGo(url) {
    const overlay = document.getElementById('pageTransitionOverlay');
    if (overlay) overlay.classList.add('is-active');
    document.body.classList.add('saindo-transicao');

    const container = document.querySelector('.container');
    if (container) {
      container.style.opacity = '0';
      container.style.transform = 'scale(0.97)';
    }

    setTimeout(() => {
      window.location.href = url;
    }, 600);
  }

  if (backButton) {
    backButton.onclick = e => {
      e.preventDefault();
      playExitTransitionAndGo('/index.html');
    };
  }

  if (logo) {
    logo.onclick = () => playExitTransitionAndGo('/index.html');
  }

  setTimeout(() => {
    document.body.classList.add('page-enter-active');
  }, 50);

  // 3) Verificar professor (botão admin)
  if (window.suap) {
    if (document.readyState === 'complete') {
      verificarProfessor();
    } else {
      $(document).ready(verificarProfessor);
    }
  }

  // 4) Tooltip de aluno nos comentários (common)
  window.addEventListener('load', () => {
    setTimeout(() => {
      if (typeof initAlunoTooltip === 'function') {
        initAlunoTooltip();
      }
    }, 1500);
  });

  // 5) Carregar vídeos + comentários
  carregarVideosCarousel();
});


// ============ SUAP / PROFESSOR ============

// Usa o mesmo objeto suap global (settings.js/client.js)
var suap = new SuapClient(SUAP_URL, CLIENT_ID, REDIRECT_URI, SCOPE);
suap.init();

function verificarProfessor() {
  if (!suap.isAuthenticated()) return;

  const scope = suap.getToken().getScope();
  suap.getResource(scope, function (resp) {
    const matricula = resp.identificacao;

    fetch('/api/auth/me')
      .then(r => r.json())
      .then(data => {
        if (data.status === 'ok' && data.data.type === 'professor') {
          const adminContainer = document.getElementById('headerAdminContainer');
          if (adminContainer) {
            adminContainer.style.display = 'flex';
          }
        }
      })
      .catch(err => console.error('Erro ao verificar professor:', err));
  });
}


// ============ CARROSSEL DE VÍDEOS ============

let currentSlide = 0;
let videoTitleByPostId = {};
let carouselPostIds = [];

// helpers usados pelo JS de comentários comum
window.getNovembroCurrentPostIds = () => carouselPostIds;
window.getNovembroVideoTitles = () => videoTitleByPostId;
window.getNovembroCurrentSlidePostId = () => {
  const slides = Array.from(document.querySelectorAll('.video-slide'));
  const activeSlide = slides[currentSlide];
  return activeSlide ? activeSlide.dataset.postId : null;
};

async function carregarVideosCarousel() {
  const videoCarouselInner = document.getElementById('videoCarouselInner');
  if (!videoCarouselInner) return;

  const temaPagina = 'educacao_antirracista';
  const tipoDesejado = 'video';
  const pagina = 1;

  try {
    const resp = await fetch(
      `/api/posts/tema/${encodeURIComponent(temaPagina)}?tipopostagem=${encodeURIComponent(tipoDesejado)}&pagina=${pagina}`
    );
    const json = await resp.json();

    if (json.status !== 'ok' || !Array.isArray(json.data)) {
      console.error('Erro ao carregar vídeos:', json);
      return;
    }

    const dados = json.data;

    carouselPostIds = [];
    videoTitleByPostId = {};
    videoCarouselInner.innerHTML = '';

    dados.forEach((video, index) => {
      if (video.tipo_postagem !== 'video') return;

      const slide = document.createElement('div');
      slide.className = 'video-slide';
      slide.dataset.index = index;
      slide.dataset.detalhe = video.descricao || 'Sem detalhamento para este vídeo.';
      slide.dataset.postId = video.id_postagem;

      videoTitleByPostId[video.id_postagem] = video.titulo;
      carouselPostIds.push(video.id_postagem);

      slide.innerHTML = `
        <div class="video-frame">
          <iframe
            src="${video.link_midia}"
            title="${video.titulo}"
            frameborder="0"
            allowfullscreen
            loading="lazy">
          </iframe>
        </div>
      `;

      videoCarouselInner.appendChild(slide);
    });

    initVideoCarousel();
    updateCommentContext();

    // inicializa comentários usando o JS comum (se existir)
    if (typeof initCommentsSectionNovembro === 'function') {
      initCommentsSectionNovembro();
    }
  } catch (e) {
    console.error('Erro ao buscar vídeos do Novembro Negro:', e);
  }
}

function initVideoCarousel() {
  const videoCarouselInner = document.getElementById('videoCarouselInner');
  const slides = Array.from(document.querySelectorAll('.video-slide'));
  const prevVideoBtn = document.getElementById('prevVideoBtn');
  const nextVideoBtn = document.getElementById('nextVideoBtn');
  const videoDescription = document.getElementById('videoDescription');
  const detailCard = document.getElementById('detailCard');
  const videoDetailTrigger = document.getElementById('videoDetailTrigger');

  if (!videoCarouselInner || !slides.length) return;

  let detailHoverTimeout;

  function updateCarouselPosition() {
    const offset = -currentSlide * 100;
    videoCarouselInner.style.transform = 'translateX(' + offset + '%)';
  }

  function resetDetailCard() {
    if (detailCard && detailCard.classList.contains('is-visible')) {
      detailCard.classList.remove('is-visible');
    }
  }

  if (prevVideoBtn && nextVideoBtn) {
    prevVideoBtn.addEventListener('click', () => {
      currentSlide = (currentSlide - 1 + slides.length) % slides.length;
      updateCarouselPosition();
      resetDetailCard();
      updateCommentContext();
    });

    nextVideoBtn.addEventListener('click', () => {
      currentSlide = (currentSlide + 1) % slides.length;
      updateCarouselPosition();
      resetDetailCard();
      updateCommentContext();
    });
  }

  if (videoDetailTrigger) {
    videoDetailTrigger.addEventListener('mouseenter', () => {
      clearTimeout(detailHoverTimeout);
      detailHoverTimeout = setTimeout(() => {
        videoDetailTrigger.classList.add('show-alt-text', 'active-detail-hover');
      }, 2500);
    });

    videoDetailTrigger.addEventListener('mouseleave', () => {
      clearTimeout(detailHoverTimeout);
      videoDetailTrigger.classList.remove('show-alt-text', 'active-detail-hover');
    });

    videoDetailTrigger.addEventListener('click', () => {
      const activeSlide = slides[currentSlide];
      const detalhe =
        activeSlide.getAttribute('data-detalhe') ||
        'Sem detalhamento para este vídeo.';
      if (videoDescription) {
        videoDescription.textContent = detalhe;
      }
      if (detailCard) {
        detailCard.classList.toggle('is-visible');
      }
    });
  }

  if (slides.length > 0) updateCarouselPosition();
}

function updateCommentContext() {
  const titleSpan = document.getElementById('commentVideoTitle1');
  const videoTitleCurrent = document.getElementById('videoTitleCurrent');

  const slides = Array.from(document.querySelectorAll('.video-slide'));
  const activeSlide = slides[currentSlide];

  let titulo = '';
  if (activeSlide) {
    const iframe = activeSlide.querySelector('iframe');
    titulo = iframe ? iframe.title : '';
  }

  if (titleSpan) titleSpan.textContent = titulo;
  if (videoTitleCurrent) videoTitleCurrent.textContent = titulo;
}
