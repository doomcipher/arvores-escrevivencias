// js/common/commentsNovembro.js

function initCommentsSectionNovembro() {
  const warning = document.getElementById('commentsWarning1');
  const form = document.getElementById('commentsForm1');
  const textarea = document.getElementById('commentText1');
  const btnSend = document.getElementById('sendComment1');
  const list = document.getElementById('commentsList1');
  const emptyText = document.getElementById('commentsEmpty1');
  const loginLink = document.getElementById('loginComments1');

  const isAuth = window.suap && suap.isAuthenticated();

  if (loginLink && window.suap) {
    loginLink.onclick = e => {
      e.preventDefault();
      window.location.href = suap.getLoginURL();
    };
  }

  if (!isAuth) {
    if (warning) warning.style.display = 'block';
    if (form) form.style.display = 'none';
  } else {
    if (warning) warning.style.display = 'none';
    if (form) form.style.display = 'block';
  }

  if (textarea && btnSend) {
    textarea.addEventListener('input', () => {
      btnSend.disabled = textarea.value.trim().length === 0;
    });

    btnSend.addEventListener('click', () => {
      const texto = textarea.value.trim();
      if (!texto) return;

      if (!window.suap) {
        alert('Autenticação indisponível.');
        return;
      }

      const scope = suap.getToken().getScope();
      suap.getResource(scope, function (resp) {
        const matricula = resp.identificacao;
        if (!matricula) {
          alert('Erro ao identificar usuário.');
          return;
        }

        const postId = typeof getNovembroCurrentSlidePostId === 'function'
          ? getNovembroCurrentSlidePostId()
          : null;

        if (!postId) {
          alert('Nenhum vídeo selecionado.');
          return;
        }

        btnSend.disabled = true;

        const formData = new FormData();
        formData.append('id_postagem', postId);
        formData.append('comentarios', texto);

        fetch('/api/comentarios/criar', {
          method: 'POST',
          body: formData
        })
          .then(r => r.json())
          .then(ret => {
            if (ret.status === 'ok') {
              textarea.value = '';
              btnSend.disabled = true;
              alert('Comentário enviado para aprovação.');
              loadCommentsNovembro(list, emptyText);
            } else {
              alert('Erro: ' + (ret.msg || 'desconhecido'));
              btnSend.disabled = false;
            }
          })
          .catch(() => {
            alert('Erro ao enviar. Tente novamente.');
            btnSend.disabled = false;
          });
      });
    });
  }

  loadCommentsNovembro(list, emptyText);
}

function loadCommentsNovembro(listEl, emptyEl) {
  if (!listEl) return;

  const postIds = typeof getNovembroCurrentPostIds === 'function'
    ? getNovembroCurrentPostIds()
    : [];

  if (!postIds || !postIds.length) {
    if (emptyEl) emptyEl.style.display = 'block';
    return;
  }

  const videoTitleByPostId =
    typeof getNovembroVideoTitles === 'function'
      ? getNovembroVideoTitles()
      : {};

  Promise.all(
    postIds.map(id =>
      fetch(`/api/comentarios/post/${id}?status=aprovado`)
        .then(r => r.json())
        .then(json => (json.status === 'ok' ? json.data : []))
        .catch(() => [])
    )
  )
    .then(listas => {
      const comments = listas.flat();
      listEl.innerHTML = '';

      if (comments && comments.length) {
        comments.sort((a, b) => new Date(a.data) - new Date(b.data));

        comments.forEach(c => {
          const div = document.createElement('div');
          div.className = 'comment-item';

          let referencia = '';
          if (c.id_postagem && videoTitleByPostId[c.id_postagem]) {
            referencia = ` • em resposta ao vídeo: ${videoTitleByPostId[c.id_postagem]}`;
          }

          div.innerHTML = `
            <div class="comment-meta">
              <span data-matricula="${c.matricula}">
                <strong>${c.nome || 'Anônimo'}</strong>${referencia}
              </span>
              <span>${new Date(c.data).toLocaleString('pt-BR')}</span>
            </div>
            <div class="comment-text">${c.comentarios}</div>
          `;

          listEl.appendChild(div);
        });

        if (emptyEl) emptyEl.style.display = 'none';

        if (typeof initAlunoTooltip === 'function') {
          setTimeout(initAlunoTooltip, 100);
        }
      } else {
        if (emptyEl) emptyEl.style.display = 'block';
      }
    })
    .catch(() => {
      if (emptyEl) {
        emptyEl.textContent = 'Erro ao carregar comentários.';
        emptyEl.style.display = 'block';
      }
    });
}

// expõe no global
window.initCommentsSectionNovembro = initCommentsSectionNovembro;
window.loadCommentsNovembro = loadCommentsNovembro;


// ===== GALERIA: init + load (compatível com HTML da galeria) =====
function initCommentsSectionGaleria() {
  const warning = document.getElementById('commentsWarning1');
  const form = document.getElementById('commentsForm1');
  const textarea = document.getElementById('commentText1');
  const btnSend = document.getElementById('sendComment1');
  const list = document.getElementById('commentsList1');
  const emptyText = document.getElementById('commentsEmpty1');
  const loginLink = document.getElementById('loginComments1');

  const isAuth = window.suap && suap.isAuthenticated();

  if (loginLink && window.suap) {
    loginLink.onclick = (e) => {
      e.preventDefault();
      window.location.href = suap.getLoginURL();
    };
  }

  // mostra/oculta form conforme login
  if (!isAuth) {
    if (warning) warning.style.display = 'block';
    if (form) form.style.display = 'none';
  } else {
    if (warning) warning.style.display = 'none';
    // OBS: só mostra o form depois que selecionar um post
    if (form) form.style.display = 'none';
  }

  if (textarea && btnSend) {
    textarea.addEventListener('input', () => {
      btnSend.disabled = textarea.value.trim().length === 0;
    });

    btnSend.addEventListener('click', () => {
      const texto = textarea.value.trim();
      if (!texto) return;

      if (!window.suap) {
        alert('Autenticação indisponível.');
        return;
      }

      const postId = (typeof getGaleriaCurrentPostId === 'function') ? getGaleriaCurrentPostId() : null;
      if (!postId) {
        alert('Selecione um post para comentar.');
        return;
      }

      const scope = suap.getToken().getScope();
      suap.getResource(scope, function (resp) {
        const matricula = resp.identificacao;
        if (!matricula) {
          alert('Erro ao identificar usuário.');
          return;
        }

        btnSend.disabled = true;

        const formData = new FormData();
        formData.append('id_postagem', postId);
        formData.append('comentarios', texto);

        fetch('/api/comentarios/criar', {
          method: 'POST',
          body: formData
        })
          .then(r => r.json())
          .then(ret => {
            if (ret.status === 'ok') {
              textarea.value = '';
              btnSend.disabled = true;
              alert('Comentário enviado para aprovação.');
              loadCommentsGaleria(list, emptyText);
            } else {
              alert('Erro: ' + (ret.msg || 'desconhecido'));
              btnSend.disabled = false;
            }
          })
          .catch(() => {
            alert('Erro ao enviar. Tente novamente.');
            btnSend.disabled = false;
          });
      });
    });
  }

  // estado inicial
  if (emptyText) emptyText.style.display = 'block';
}

function loadCommentsGaleria(listEl, emptyEl) {
  if (!listEl) return;

  const postId = (typeof getGaleriaCurrentPostId === 'function') ? getGaleriaCurrentPostId() : null;
  if (!postId) {
    if (emptyEl) {
      emptyEl.textContent = 'Selecione um post para ver os comentários.';
      emptyEl.style.display = 'block';
    }
    listEl.innerHTML = '';
    return;
  }

  // se autenticado, mostra o form (agora faz sentido)
  const isAuth = window.suap && suap.isAuthenticated();
  const form = document.getElementById('commentsForm1');
  if (form) form.style.display = isAuth ? 'block' : 'none';

  const titleMap = (typeof getGaleriaTitleByPostId === 'function') ? getGaleriaTitleByPostId() : {};
  const referencia = titleMap && titleMap[postId] ? titleMap[postId] : '';

  fetch(`/api/comentarios/post/${postId}?status=aprovado`)
    .then(r => r.json())
    .then(json => {
      const comments = (json.status === 'ok' && Array.isArray(json.data)) ? json.data : [];

      listEl.innerHTML = '';

      if (!comments.length) {
        if (emptyEl) {
          emptyEl.textContent = referencia
            ? `Nenhum comentário ainda para: ${referencia}`
            : 'Nenhum comentário ainda.';
          emptyEl.style.display = 'block';
        }
        return;
      }

      if (emptyEl) emptyEl.style.display = 'none';

      comments
        .sort((a, b) => new Date(a.data) - new Date(b.data))
        .forEach(c => {
          const div = document.createElement('div');
          div.className = 'comment-item';

          div.innerHTML = `
            <div class="comment-meta">
              <span data-matricula="${c.matricula || ''}">
                <strong>${c.nome || 'Anônimo'}</strong>
              </span>
              <span>${new Date(c.data).toLocaleString('pt-BR')}</span>
            </div>
            <div class="comment-text">${c.comentarios || ''}</div>
          `;

          listEl.appendChild(div);
        });

      // tooltip (se existir)
      if (typeof initAlunoTooltip === 'function') {
        setTimeout(initAlunoTooltip, 100);
      }
    })
    .catch(() => {
      if (emptyEl) {
        emptyEl.textContent = 'Erro ao carregar comentários.';
        emptyEl.style.display = 'block';
      }
    });
}

// expõe no window, igual Novembro
window.initCommentsSectionGaleria = initCommentsSectionGaleria;
window.loadCommentsGaleria = loadCommentsGaleria;
