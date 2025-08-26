document.addEventListener('DOMContentLoaded', function() {
    // --- VARIÁVEIS E SELETORES ---
    let carrinhoContador = 0;
    const contadorCarrinho = document.getElementById('contador-carrinho');
    const statusLoja = document.getElementById('status-loja');
    const btnVerMais = document.getElementById('btn-ver-mais');
    const inputPesquisa = document.getElementById('input-pesquisa');
    const todosProdutos = document.querySelectorAll('.card-v');

    // --- INICIALIZAÇÃO DO SLIDER 'MAIS VENDIDOS' ---
    const maisVendidosSwiper = new Swiper('.mais-vendidos-swiper', {
        effect: 'slide',
        centeredSlides: true,
        slidesPerView: 'auto',
        spaceBetween: 15,
        grabCursor: true,
        loop: true,
    });

    // --- LÓGICA DO BANNER PRINCIPAL ---
    const banner = document.querySelector('.banner');
    if (banner) {
        const bannerSlides = banner.querySelector('.banner-slides');
        const nextBtn = banner.querySelector('.next');
        const prevBtn = banner.querySelector('.prev');
        let slideAtual = 0;
        let slideInterval;

        function mostrarSlide(index) {
            const totalSlides = bannerSlides.children.length;
            if (index >= totalSlides) slideAtual = 0;
            else if (index < 0) slideAtual = totalSlides - 1;
            else slideAtual = index;
            bannerSlides.style.transform = `translateX(${-slideAtual * 100}%)`;
        }

        function proximoSlide() { mostrarSlide(slideAtual + 1); }
        function slideAnterior() { mostrarSlide(slideAtual - 1); }
        function iniciarBanner() {
            clearInterval(slideInterval);
            slideInterval = setInterval(proximoSlide, 3500);
        }

        nextBtn.addEventListener('click', () => { proximoSlide(); iniciarBanner(); });
        prevBtn.addEventListener('click', () => { slideAnterior(); iniciarBanner(); });
        iniciarBanner();
    }
    
    // --- LÓGICA DO MODAL DE FILTROS ---
    const modalFiltro = document.getElementById('modal-filtro');
    const btnAbrirFiltro = document.querySelector('.btn-filtro');
    const btnFecharFiltro = document.getElementById('btn-fechar-filtro');

    if (modalFiltro && btnAbrirFiltro && btnFecharFiltro) {
        btnAbrirFiltro.addEventListener('click', () => modalFiltro.style.display = 'flex');
        btnFecharFiltro.addEventListener('click', () => modalFiltro.style.display = 'none');
        window.addEventListener('click', (event) => {
            if (event.target == modalFiltro) {
                modalFiltro.style.display = 'none';
            }
        });

        function atualizarVisualizacaoFiltros() {
            const params = new URLSearchParams(window.location.search);
            const ordenar = params.get('ordenar');
            if (ordenar) {
                const radioSelecionado = document.querySelector(`input[name="ordenar"][value="${ordenar}"]`);
                if(radioSelecionado) radioSelecionado.checked = true;
            } else {
                const radioPadrao = document.getElementById('ordem-padrao');
                if(radioPadrao) radioPadrao.checked = true;
            }
            const categorias = params.getAll('categorias[]');
            if (categorias.length > 0) {
                categorias.forEach(cat => {
                    const checkboxSelecionado = document.querySelector(`input[name="categorias[]"][value="${cat}"]`);
                    if(checkboxSelecionado) checkboxSelecionado.checked = true;
                });
            }
        }
        atualizarVisualizacaoFiltros();
    }

    // --- OUTRAS FUNÇÕES ---

    function atualizarHorarioFuncionamento() {
        const diaSemana = new Date().getDay();
        let status = "FECHADO";
        if (diaSemana >= 2 && diaSemana <= 6) status = "ABERTO ATÉ ÀS 23:00h";
        else if (diaSemana === 0) status = "ABERTO ATÉ ÀS 22:00h";
        if (statusLoja) statusLoja.textContent = status;
    }

    if (btnVerMais) {
        btnVerMais.addEventListener('click', () => {
            const produtosEscondidos = document.querySelectorAll('.produto-escondido');
            for (let i = 0; i < 6 && i < produtosEscondidos.length; i++) {
                produtosEscondidos[i].classList.remove('produto-escondido');
            }
            if (document.querySelectorAll('.produto-escondido').length === 0) {
                btnVerMais.style.display = 'none';
            }
        });
    }

    document.querySelectorAll('.btn-adicionar').forEach(btn => {
        btn.addEventListener('click', () => {
            if (isUserLoggedIn) {
                carrinhoContador++;
                contadorCarrinho.textContent = carrinhoContador;
                const cartButton = document.querySelector('.btn-carrinho');
                cartButton.style.transform = 'scale(1.2)';
                setTimeout(() => { cartButton.style.transform = 'scale(1)'; }, 200);
            } else {
                alert('Você precisa fazer login para adicionar itens ao carrinho!');
                window.location.href = 'login.html';
            }
        });
    }
    );

    if (inputPesquisa) {
        inputPesquisa.addEventListener('keyup', () => {
            const termo = inputPesquisa.value.toLowerCase();
            todosProdutos.forEach(produto => {
                const nomeProduto = produto.dataset.nome.toLowerCase();
                produto.style.display = nomeProduto.includes(termo) ? 'flex' : 'none';
            });
        });
    }
    
    atualizarHorarioFuncionamento();
});