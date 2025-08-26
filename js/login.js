document.addEventListener('DOMContentLoaded', function() {

    // --- SELETORES DOS ELEMENTOS ---
    const abaLogin = document.getElementById('aba-login');
    const abaCadastro = document.getElementById('aba-cadastro');
    const formLogin = document.getElementById('form-login');
    const formCadastro = document.getElementById('form-cadastro');
    const containerMensagem = document.getElementById('mensagem-feedback');

    // --- FUNÇÕES DE CONTROLE DAS ABAS ---
    function mostrarLogin() {
        abaLogin.classList.add('ativa');
        abaCadastro.classList.remove('ativa');
        formLogin.classList.add('ativo');
        formCadastro.classList.remove('ativo');
    }

    function mostrarCadastro() {
        abaCadastro.classList.add('ativa');
        abaLogin.classList.remove('ativa');
        formCadastro.classList.add('ativo');
        formLogin.classList.remove('ativo');
    }

    // --- FUNÇÃO PARA EXIBIR MENSAGENS ---
    function exibirMensagem(texto, tipo = 'erro') {
        // Remove qualquer mensagem anterior
        containerMensagem.innerHTML = ''; 
        
        const divMensagem = document.createElement('div');
        divMensagem.textContent = texto;
        divMensagem.className = `mensagem ${tipo}`; // Adiciona classes para estilização
        
        containerMensagem.appendChild(divMensagem);
    }

    // --- LÓGICA PRINCIPAL AO CARREGAR A PÁGINA ---

    // 1. Pega os parâmetros da URL
    const params = new URLSearchParams(window.location.search);
    const aba = params.get('aba');
    const erro = params.get('erro');
    const sucesso = params.get('sucesso');

    // 2. Decide qual aba mostrar
    if (aba === 'cadastro') {
        mostrarCadastro();
    } else {
        mostrarLogin(); // Padrão
    }

    // 3. Exibe mensagens de erro ou sucesso
    if (erro) {
        if (erro === 'login') {
            exibirMensagem('E-mail ou senha inválidos.', 'erro');
        } else if (erro === 'email') {
            exibirMensagem('Este e-mail já está cadastrado.', 'erro');
        }
    }

    if (sucesso) {
        if (sucesso === 'cadastro') {
            exibirMensagem('Cadastro realizado com sucesso! Faça o login.', 'sucesso');
        }
    }

    // --- EVENT LISTENERS PARA CLIQUES NAS ABAS ---
    abaLogin.addEventListener('click', mostrarLogin);
    abaCadastro.addEventListener('click', mostrarCadastro);

});