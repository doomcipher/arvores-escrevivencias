// Depende de: settings.js, client.js, jQuery

window.SuapAuth = (function () {
    const suap = new SuapClient(SUAP_URL, CLIENT_ID, REDIRECT_URI, SCOPE);
    suap.init();
    window.suap = suap;


    function initLoginLink(loginLinkId = 'loginLink') {
        const loginLink = document.getElementById(loginLinkId);
        if (!loginLink) return;

        let hoverTimeout;

        // Hover animado
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

        // Clique → redireciona pro SUAP
        loginLink.onclick = e => {
            e.preventDefault();
            window.location.href = suap.getLoginURL();
        };

        // Quando a página carrega, verifica se já voltou do SUAP
        $(document).ready(function () {
            $('#loginLink').attr('href', suap.getLoginURL());

            if (suap.isAuthenticated()) {
                const scope = suap.getToken().getScope();
                const callback = function (response) {
                    const matricula = response.identificacao;
                    const nome = response.nome;

                    // 1) cria ou encontra aluno
                    syncAluno(matricula, nome);

                    // 2) cria sessão no backend (aluno ou professor)
                    authCallback(matricula, nome);
                };
                suap.getResource(scope, callback);
            } else {
                console.log('Usuário anônimo navegando normalmente.');
            }
        });
    }

    function syncAluno(matricula, nome) {
        const dados = { matricula, nome };

        fetch('/api/alunos/criar', { // copie exatamente como está em routes
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(dados)
        })
            .then(res => res.json())
            .then(ret => console.log('Aluno sync:', ret))
            .catch(err => console.error('Erro ao sincronizar aluno:', err));
    }

    function authCallback(matricula, nome) {
        const dados = { matricula, nome };

        fetch('/api/auth/callback', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(dados)
        })
            .then(res => res.json())
            .then(ret => {
                console.log('Auth callback:', ret);
            })
            .catch(err => {
                console.error('Erro no auth callback:', err);
            });
    }

    return {
        initLoginLink
    };
})();
SuapAuth.initLoginLink('loginLink');
SuapAuth.initLoginLink('loginComments1');
