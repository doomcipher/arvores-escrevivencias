// ===== CONFIG TEMAS =====
const GALERIA_TEMAS = [
    { id: 'educacao_antirracista', label: 'Educação Antirracista' },
    { id: 'esportes_representatividade', label: 'Esportes e Representatividade' },
    { id: 'artes_cenicas_visuais', label: 'Artes Cênicas e Visuais' },
    { id: 'musica_ritmos_afro_brasileiros', label: 'Música e Ritmos Afro-Brasileiros' }
];

let galeriaSelectedTheme = 'educacao_antirracista';
let galeriaCurrentPostId = null;
const galleryTitleByPostId = {};

// exposto para /js/common/comentarios.js
window.getGaleriaCurrentPostId = () => galeriaCurrentPostId;
window.getGaleriaTitleByPostId = () => galleryTitleByPostId;

// ===== THEME NAV =====
function renderThemeNav() {
    const themeNav = document.getElementById('themeNav');
    if (!themeNav) return;

    themeNav.innerHTML = GALERIA_TEMAS.map(tema => `
    <button
      class="theme-btn ${tema.id === galeriaSelectedTheme ? 'active' : ''}"
      data-galeria-theme="${tema.id}"
      type="button"
    >
      ${tema.label}
    </button>
  `).join('');
}

function bindThemeNavEvents() {
    const themeNav = document.getElementById('themeNav');
    if (!themeNav) return;

    themeNav.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-galeria-theme]');
        if (!btn) return;

        galeriaSelectedTheme = btn.dataset.galeriaTheme;
        renderThemeNav();
        loadGalleries();
    });
}

// ===== GALERIAS / POSTS =====
async function loadGalleries() {
    const container = document.getElementById('galleriesContainer');
    if (!container) return;

    container.innerHTML = '<div class="loading">Carregando galerias...</div>';

    try {
        const resp = await fetch(
            `/api/posts/tema/${encodeURIComponent(galeriaSelectedTheme)}?tipo_postagem=imagem&pagina=1`
        );
        const json = await resp.json();

        if (json.status !== 'ok' || !Array.isArray(json.data) || !json.data.length) {
            container.innerHTML = `
        <div class="empty-state">
          <h3>Nenhuma galeria disponível</h3>
          <p>Não encontramos imagens para este tema.</p>
        </div>
      `;
            galeriaCurrentPostId = null;
            return;
        }

        const posts = (json.data || []).filter(p => p.tipo_postagem === 'imagem');

        if (!posts.length) {
            container.innerHTML = `
        <div class="empty-state">
          <h3>Nenhuma galeria disponível</h3>
          <p>Não encontramos imagens para este tema.</p>
        </div>
      `;
            galeriaCurrentPostId = null;
            return;
        }

        posts.forEach(post => {
            galleryTitleByPostId[post.id_postagem] = post.titulo || 'Post sem título';
        });


        container.innerHTML = posts.map(post => {
            const titulo = post.titulo || 'Post sem título';
            const descricao = post.descricao || '';
            const autor = post.nome_professor || post.nome_autor || 'Autor desconhecido';
            const data = post.data ? new Date(post.data).toLocaleDateString('pt-BR') : '';

            return `
        <article class="gallery-card"
                 data-post-id="${post.id_postagem}"
                 data-post-title="${titulo}">
          <header class="gallery-header">
            <h3>${titulo}</h3>
            <p class="gallery-description">${descricao}</p>
            <p class="gallery-meta">
              Por ${autor} • ${data}
            </p>
            <button
              type="button"
              class="gallery-comment-btn"
              data-galeria-comment="${post.id_postagem}">
              <span class="dot"></span> Comentar este post
            </button>
          </header>

          <div class="images-grid" id="gallery-${post.id_postagem}">
            <div class="loading">Carregando imagens...</div>
          </div>
        </article>
      `;
        }).join('');

        // bind do botão de comentar
        container.querySelectorAll('[data-galeria-comment]').forEach(btn => {
            btn.addEventListener('click', () => {
                const postId = Number(btn.dataset.galeriaComment);
                const title = btn.closest('article')?.dataset.postTitle || '';
                selectGaleriaPost(postId, title);
            });
        });

        // carregar imagens de cada post (usando link_midia do próprio post)
        posts.forEach(post => loadImagesForGallery(post.id_postagem, post.link_midia));
    } catch (e) {
        console.error('Erro ao carregar galerias:', e);
        container.innerHTML = `
      <div class="empty-state">
        <h3>Erro ao carregar galerias</h3>
        <p>Verifique a conexão ou tente novamente mais tarde.</p>
      </div>
    `;
    }
}

// ===== IMAGENS =====
async function loadImagesForGallery(postId, linkMidia) {
    const gridContainer = document.getElementById(`gallery-${postId}`);
    if (!gridContainer) return;

    // link_midia já vem com as URLs das imagens (Cloudinary ou Assets)
    let imagens = [];

    try {
        if (Array.isArray(linkMidia)) {
            imagens = linkMidia;
        } else if (typeof linkMidia === 'string') {
            const trimmed = linkMidia.trim();
            if (trimmed.startsWith('[')) {
                imagens = JSON.parse(trimmed);
            } else if (trimmed) {
                imagens = [trimmed];
            }
        }
    } catch (e) {
        console.error('Erro ao interpretar link_midia:', e);
        imagens = [];
    }

    if (!imagens.length) {
        gridContainer.innerHTML = '<p class="no-images">Sem imagens</p>';
        return;
    }

    gridContainer.innerHTML = imagens.map(src => {
        // se for caminho local em /Assets, mantém compatível com o CSS da página
        let finalSrc = src;
        if (src.startsWith('/Assets/')) {
            finalSrc = src; // já absoluto
        }

        return `
      <div class="image-card" data-galeria-img="${finalSrc}">
        <img src="${finalSrc}" alt="Imagem da galeria" loading="lazy">
        <div class="image-overlay">
          <span class="overlay-text">Clique para ampliar</span>
        </div>
      </div>
    `;
    }).join('');

    gridContainer.querySelectorAll('[data-galeria-img]').forEach(card => {
        card.addEventListener('click', () => {
            openGaleriaModal(card.dataset.galeriaImg);
        });
    });
}

// ===== MODAL =====
function openGaleriaModal(src) {
    const modal = document.getElementById('imageModal');
    const img = document.getElementById('modalImage');
    if (!modal || !img) return;

    img.src = src;
    modal.classList.add('active');
}

function closeGaleriaModal() {
    const modal = document.getElementById('imageModal');
    if (!modal) return;
    modal.classList.remove('active');
}

function bindGaleriaModalEvents() {
    const modal = document.getElementById('imageModal');
    if (!modal) return;

    modal.addEventListener('click', (e) => {
        if (e.target.id === 'imageModal' || e.target.classList.contains('modal-backdrop')) {
            closeGaleriaModal();
        }
    });

    document.querySelectorAll('[data-galeria-close]').forEach(btn => {
        btn.addEventListener('click', closeGaleriaModal);
    });
}

// ===== COMENTÁRIOS (usa /js/common/comentarios.js) =====
function selectGaleriaPost(postId, title) {
    galeriaCurrentPostId = postId;

    const context = document.getElementById('commentContext');
    if (context) {
        context.textContent = `Em resposta ao post: ${title}`;
    }

    const commentsSection = document.getElementById('commentsSection');
    if (commentsSection) {
        commentsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    const list = document.getElementById('commentsList1');
    const emptyText = document.getElementById('commentsEmpty1');
    // se estiver logado, mostra o form quando um post é selecionado
    const form = document.getElementById('commentsForm1');
    const warning = document.getElementById('commentsWarning1');
    const isAuth = window.suap && suap.isAuthenticated();

    if (warning) warning.style.display = isAuth ? 'none' : 'block';
    if (form) form.style.display = isAuth ? 'flex' : 'none';

    if (typeof loadCommentsGaleria === 'function') {
        loadCommentsGaleria(list, emptyText);
    }

}

// ===== BOOTSTRAP =====
document.addEventListener('DOMContentLoaded', () => {

    renderThemeNav();
    bindThemeNavEvents();
    bindGaleriaModalEvents();
    loadGalleries();


    if (typeof initCommentsSectionGaleria === 'function') {
        initCommentsSectionGaleria();
    }
});
