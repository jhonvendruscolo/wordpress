/**
 * GTA VI Ultimate - Script principal
 * Funcionalidades JavaScript para o plugin
 */

(function($) {
    'use strict';

    // Contador regressivo para o lançamento
    function initCountdown() {
        // Data de lançamento do GTA VI: 26 de maio de 2026
        const releaseDate = new Date(gta6_vars.release_date).getTime();
        
        // Atualizar contador a cada segundo
        const countdownTimer = setInterval(function() {
            // Data e hora atuais
            const now = new Date().getTime();
            
            // Diferença entre a data de lançamento e a data atual
            const distance = releaseDate - now;
            
            // Cálculos de tempo
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            // Formatar números com zero à esquerda
            const formattedDays = String(days).padStart(2, '0');
            const formattedHours = String(hours).padStart(2, '0');
            const formattedMinutes = String(minutes).padStart(2, '0');
            const formattedSeconds = String(seconds).padStart(2, '0');
            
            // Atualizar elementos HTML
            $('#gta6-days').text(formattedDays);
            $('#gta6-hours').text(formattedHours);
            $('#gta6-minutes').text(formattedMinutes);
            $('#gta6-seconds').text(formattedSeconds);
            
            // Se a contagem regressiva terminar
            if (distance < 0) {
                clearInterval(countdownTimer);
                $('.gta6-countdown').html('<div class="gta6-released">GTA VI JÁ ESTÁ DISPONÍVEL!</div>');
            }
        }, 1000);
    }

    // Filtros para conteúdo
    function initFilters() {
        $('.gta6-filter-button').on('click', function() {
            const category = $(this).data('category');
            
            // Ativar botão de filtro
            $('.gta6-filter-button').removeClass('active');
            $(this).addClass('active');
            
            if (category === 'all') {
                // Mostrar todos os itens
                $('.gta6-filterable-item').show();
            } else {
                // Filtrar por categoria
                $('.gta6-filterable-item').hide();
                $(`.gta6-filterable-item[data-category="${category}"]`).show();
            }
        });
    }

    // Galeria de imagens com lightbox
    function initGallery() {
        $('.gta6-gallery-item').on('click', function() {
            const imageUrl = $(this).data('full-image');
            const imageTitle = $(this).find('.gta6-gallery-title').text();
            
            // Criar lightbox
            const lightbox = `
                <div class="gta6-lightbox">
                    <div class="gta6-lightbox-content">
                        <span class="gta6-lightbox-close">&times;</span>
                        <img src="${imageUrl}" alt="${imageTitle}">
                        <div class="gta6-lightbox-caption">${imageTitle}</div>
                    </div>
                </div>
            `;
            
            // Adicionar lightbox ao corpo do documento
            $('body').append(lightbox);
            
            // Fechar lightbox ao clicar no botão de fechar ou fora da imagem
            $('.gta6-lightbox, .gta6-lightbox-close').on('click', function() {
                $('.gta6-lightbox').remove();
            });
            
            // Evitar que o clique na imagem feche o lightbox
            $('.gta6-lightbox-content img').on('click', function(e) {
                e.stopPropagation();
            });
        });
    }

    // Efeitos de animação para elementos
    function initAnimations() {
        // Adicionar classe de animação aos elementos ao rolar a página
        $(window).on('scroll', function() {
            $('.gta6-animate').each(function() {
                const elementTop = $(this).offset().top;
                const elementHeight = $(this).outerHeight();
                const windowHeight = $(window).height();
                const scrollY = $(window).scrollTop();
                
                // Verificar se o elemento está visível na janela
                if (scrollY > elementTop - windowHeight + elementHeight / 2) {
                    $(this).addClass('gta6-animated');
                }
            });
        });
        
        // Disparar verificação inicial
        $(window).trigger('scroll');
    }

    // Navegação ativa
    function initActiveNav() {
        // Obter URL atual
        const currentUrl = window.location.href;
        
        // Verificar cada link de navegação
        $('.gta6-nav a').each(function() {
            const linkUrl = $(this).attr('href');
            
            // Marcar link como ativo se corresponder à URL atual
            if (currentUrl === linkUrl || currentUrl.indexOf(linkUrl) !== -1) {
                $(this).addClass('active');
            }
        });
    }

    // Inicializar todas as funcionalidades quando o documento estiver pronto
    $(document).ready(function() {
        // Verificar se estamos em uma página do GTA VI Ultimate
        if ($('.gta6-container').length > 0) {
            initCountdown();
            initFilters();
            initGallery();
            initAnimations();
            initActiveNav();
        }
    });

})(jQuery);
