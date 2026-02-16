// -------- Tooltip de aluno --------
let tooltipMouseMoveHandler = null;

function extrairTurmaEAno(matricula) {
  // segurança: matrícula precisa ter pelo menos 10 dígitos (aluno)
  if (!matricula || matricula.length < 10) {
    return {
      turma: 'Turma desconhecida',
      serie: 'Ano desconhecido',
      ano: '----'
    };
  }

  const ano = matricula.substring(0, 4);
  const codigoTurma = matricula.substring(4, 10);

  const anoNum = parseInt(ano, 10);
  if (Number.isNaN(anoNum)) {
    return {
      turma: 'Turma desconhecida',
      serie: 'Ano desconhecido',
      ano: '----'
    };
  }

  const anoAtual = new Date().getFullYear();
  const anosNoIfr = anoAtual - anoNum;

  const series = {
    0: '1º ano',
    1: '2º ano',
    2: '3º ano',
    3: '4º ano'
  };
  const serie = series[anosNoIfr] || 'Ano desconhecido';

  const turmas = {
    '102101': 'Edificações',
    '102201': 'Eletrotécnica',
    '102202': 'Mecânica',
    '102401': 'Informática',
    '102114': 'Meio Ambiente'
  };

  const nomeTurma = turmas[codigoTurma] || 'Turma desconhecida';

  return { turma: nomeTurma, serie, ano: anoNum.toString() };
}

function removerTooltip() {
  const tooltip = document.getElementById('aluno-tooltip');
  if (tooltip) {
    tooltip.remove();
  }

  if (tooltipMouseMoveHandler) {
    document.removeEventListener('mousemove', tooltipMouseMoveHandler, true);
    tooltipMouseMoveHandler = null;
  }
}

function mostrarTooltip(elemento, nome, matricula) {
  removerTooltip(); // limpa tooltip anterior

  const info = extrairTurmaEAno(matricula);

  const tooltip = document.createElement('div');
  tooltip.id = 'aluno-tooltip';
  tooltip.style.cssText = `
    position: fixed;
    background: linear-gradient(135deg, rgba(100, 180, 220, 0.95) 0%, rgba(120, 200, 240, 0.90) 100%);
    color: #1a3a4a;
    padding: 14px 18px;
    border-radius: 16px;
    border: 2px solid rgba(0, 220, 200, 0.7);
    box-shadow:
      0 8px 24px rgba(0, 150, 180, 0.4),
      inset 0 1px 0 rgba(255, 255, 255, 0.5),
      0 0 20px rgba(100, 220, 255, 0.3);
    font-size: 0.9rem;
    font-weight: 600;
    z-index: 9999;
    pointer-events: none;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    white-space: nowrap;
    letter-spacing: 0.5px;
  `;

  tooltip.innerHTML = `
    <div style="text-align: center; line-height: 1.6;">
      <strong style="font-size: 1rem; color: #0d2a38;">${nome}</strong><br>
      <span style="color: #1a5a7a; font-weight: 500; font-size: 0.85rem;">
        📚 ${info.turma}
      </span><br>
      <span style="color: #2a7a9a; font-size: 0.8rem; opacity: 0.9;">
        ${info.serie}
      </span>
    </div>
  `;

  document.body.appendChild(tooltip);

  tooltipMouseMoveHandler = e => {
    const offsetX = 16;
    const offsetY = 16;
    const rect = tooltip.getBoundingClientRect();

    let left = e.clientX + offsetX;
    let top = e.clientY - offsetY;

    const maxLeft = window.innerWidth - rect.width - 8;
    const maxTop = window.innerHeight - rect.height - 8;
    if (left > maxLeft) left = maxLeft;
    if (top > maxTop) top = maxTop;
    if (top < 8) top = 8;

    tooltip.style.left = left + 'px';
    tooltip.style.top = top + 'px';
  };

  document.addEventListener('mousemove', tooltipMouseMoveHandler, true);

  elemento.addEventListener('mouseleave', removerTooltip, { once: true });
}

function initAlunoTooltip() {
  document.querySelectorAll('.comment-item strong').forEach(nomeEl => {
    if (nomeEl.dataset.tooltipInitialized) return;
    nomeEl.dataset.tooltipInitialized = 'true';

    const spanPai = nomeEl.parentElement;
    const matricula = spanPai.dataset.matricula;
    if (!matricula) return;

    const nomeCompleto = nomeEl.textContent.trim();
    if (!nomeCompleto) return;

    nomeEl.style.cursor = 'help';

    nomeEl.addEventListener('mouseenter', () => {
      mostrarTooltip(nomeEl, nomeCompleto, matricula);
    });

    nomeEl.addEventListener('mouseleave', removerTooltip);
  });
}
