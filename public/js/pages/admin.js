let currentProfessorId = null;

// Pega elementos assim que o script roda (ele já está no fim do body)
const adminContent = document.getElementById('adminContent');
const accessDenied = document.getElementById('accessDenied');
const headerUser = document.getElementById('headerUser');
const userInfo = document.getElementById('userInfo');
const postsList = document.getElementById('postsList');

const postForm      = document.getElementById('postForm');
const postTitle     = document.getElementById('postTitle');
const postDesc      = document.getElementById('postDesc');
const postTema      = document.getElementById('postTema');
const postTipoInput = document.getElementById('postTipo');
const postFiles     = document.getElementById('postFiles');
const filesGroup    = document.getElementById('filesGroup');
const filesLabel    = document.getElementById('filesLabel');
const submitBtn     = document.getElementById('submitBtn');
const alertBox      = document.getElementById('alert');


console.log('admin.js carregado. postForm existe?', !!postForm);

// ===== ALERTA =====
function showAlert(msg, type = 'success') {
  if (!alertBox) return;
  alertBox.textContent = msg;
  alertBox.className = 'alert show ' + (type === 'error' ? 'error' : 'success');
  setTimeout(() => {
    alertBox.className = 'alert';
  }, 4000);
}

// ===== SUBMIT DO FORM =====

if (postForm) {
  postForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const titulo        = postTitle.value.trim();
    const descricao     = postDesc.value.trim();
    const tema          = postTema.value;
    const tipo_postagem = postTipoInput.value;
    const files         = Array.from(postFiles.files);

    if (!titulo || !descricao) {
      showAlert('Preencha título e descrição.', 'error');
      return;
    }

    if (!files.length) {
      showAlert('Selecione pelo menos um arquivo.', 'error');
      return;
    }

    if (tipo_postagem === 'video') {
      if (files.length !== 1) {
        showAlert('Selecione apenas um arquivo de vídeo.', 'error');
        return;
      }
    } else {
      if (files.length > 10) {
        showAlert('Máximo de 10 imagens por post.', 'error');
        return;
      }
    }

    const maxSize = 50 * 1024 * 1024; // exemplo: 50 MB para vídeo/imagem
    if (files.some(f => f.size > maxSize)) {
      showAlert('Arquivo muito grande.', 'error');
      return;
    }

    const formData = new FormData();
    formData.append('titulo', titulo);
    formData.append('descricao', descricao);
    formData.append('tema', tema);
    formData.append('tipo_postagem', tipo_postagem);

    // Sempre manda em arquivos[], o Controller já cuida
    files.forEach(file => formData.append('arquivos[]', file)); // $_FILES['arquivos']

    submitBtn.disabled = true;
    submitBtn.textContent = 'Enviando...';

    try {
      const resp = await fetch('/api/posts/criar', {
        method: 'POST',
        body: formData
      });
      const json = await resp.json();

      if (json.status === 'ok') {
        showAlert('Post criado com sucesso!');
        postForm.reset();
        postTipoInput.value = 'imagem';
        atualizarCamposArquivo();
        loadPosts();
      } else {
        showAlert(json.msg || 'Erro ao criar post.', 'error');
      }
    } catch (err) {
      console.error(err);
      showAlert('Erro de comunicação com o servidor.', 'error');
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Criar Post';
    }
  });
}

function atualizarCamposArquivo() {
  const tipo = postTipoInput.value;

  if (tipo === 'video') {
    filesLabel.textContent = 'Arquivo do vídeo (será enviado para o Cloudinary)';
    postFiles.accept  = 'video/*';
    postFiles.multiple = false;
    postFiles.value = '';
  } else {
    filesLabel.textContent = 'Imagens da postagem (máx. 10 arquivos, 5 MB cada)';
    postFiles.accept  = 'image/*';
    postFiles.multiple = true;
    postFiles.value = '';
  }
}

postTipoInput.addEventListener('change', atualizarCamposArquivo);
atualizarCamposArquivo();

// ===== AUTH / ACESSO =====
async function checkAccess() {
  try {
    const resp = await fetch('/api/auth/me');
    const json = await resp.json();

    if (json.status === 'ok' && json.data.type === 'professor') {
      currentProfessorId = json.data.id;
      if (adminContent) adminContent.style.display = 'block';
      if (accessDenied) accessDenied.style.display = 'none';
      if (headerUser) headerUser.style.display = 'flex';
      if (userInfo) userInfo.textContent = `👋 ${json.data.nome}`;
      loadPosts();
      carregarComentariosPendentes();
    } else {
      if (adminContent) adminContent.style.display = 'none';
      if (accessDenied) accessDenied.style.display = 'block';
    }
  } catch (e) {
    if (adminContent) adminContent.style.display = 'none';
    if (accessDenied) accessDenied.style.display = 'block';
  }
}

// ===== LISTAR POSTS / COMENTÁRIOS (igual você já tinha) =====
// ... mantém seus loadPosts, carregarComentariosPendentes,
// aprovarComentario, recusarComentario exatamente como estão ...




// ====== LISTAR POSTS ======
function loadPosts() {
  if (!postsList) return;

  fetch('/api/posts/listar')
    .then(r => r.json())
    .then(json => {
      postsList.innerHTML = '';

      if (json.status === 'ok' && Array.isArray(json.data) && json.data.length) {
        json.data.forEach(post => {
          const div = document.createElement('div');
          div.className = 'post-item';
          div.innerHTML = `
            <div class="post-title">${post.titulo}</div>
            <div class="post-meta">
              ${post.nome_professor} • ${new Date(post.data).toLocaleDateString('pt-BR')}
              • ${post.tipo_postagem} • ${post.tema}
            </div>
            <div class="post-desc">${post.descricao}</div>
            <div class="post-actions">
              <button class="btn-edit"
                onclick="editPost(
                  ${post.id_postagem},
                  '${post.titulo.replace(/'/g, "\\'")}',
                  '${post.descricao.replace(/'/g, "\\'")}',
                  '${(post.link_midia || '').replace(/'/g, "\\'")}',
                  '${post.tipo_postagem}',
                  '${post.tema}'
                )">✏️ Editar</button>
              <button class="btn-delete" onclick="deletePost(${post.id_postagem})">🗑️ Deletar</button>
            </div>
          `;
          postsList.appendChild(div);
        });
      } else {
        postsList.innerHTML = '<div class="posts-empty">Nenhum post cadastrado ainda.</div>';
      }
    })
    .catch(() => {
      postsList.innerHTML = '<div class="posts-empty">Erro ao carregar posts.</div>';
    });
}


// ====== COMENTÁRIOS PENDENTES ======
async function carregarComentariosPendentes() {
  try {
    const resp = await fetch('/api/comentarios/pendentes');
    const json = await resp.json();

    const container = document.getElementById('comentariosPendentes');
    if (!container) return;

    container.innerHTML = '';

    if (json.status !== 'ok' || !json.data.length) {
      container.innerHTML = '<p class="comentarios-vazio">Não há comentários pendentes.</p>';
      return;
    }

    json.data.forEach(c => {
      const div = document.createElement('div');
      div.className = 'comentario-pendente';
      div.dataset.id = c.id_comentarios;

      div.innerHTML = `
        <div class="comentario-pendente-header">
          <span class="comentario-pendente-post">${c.titulo_post}</span>
          <span>${new Date(c.data).toLocaleString('pt-BR')}</span>
        </div>
        <p><strong>Aluno:</strong> ${c.nome_aluno}</p>
        <p class="comentario-pendente-texto">${c.comentarios}</p>
        <div class="comentario-pendente-actions">
          <button class="btn-approve" onclick="aprovarComentario(${c.id_comentarios})">Aprovar</button>
          <button class="btn-reject" onclick="recusarComentario(${c.id_comentarios})">Recusar</button>
        </div>
      `;
      container.appendChild(div);
    });
  } catch (e) {
    console.error(e);
  }
}

async function aprovarComentario(id) {
  const resp = await fetch(`/api/comentarios/aprovar/${id}`, { method: 'POST' });
  const dados = await resp.json();
  window.alert(dados.msg || 'OK');

  const card = document.querySelector(`.comentario-pendente[data-id="${id}"]`);
  if (card) card.remove();
  verificarListaVazia();
}

async function recusarComentario(id) {
  const resp = await fetch(`/api/comentarios/reprovar/${id}`, { method: 'DELETE' });
  const dados = await resp.json();
  window.alert(dados.msg || 'OK');

  const card = document.querySelector(`.comentario-pendente[data-id="${id}"]`);
  if (card) card.remove();
  verificarListaVazia();
}


// Chama no final
checkAccess();