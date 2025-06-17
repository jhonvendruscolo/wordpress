/**
 * JavaScript para o seletor de idiomas do GTA VI Ultimate
 */
(function($) {
    'use strict';

    // Inicializar o seletor de idiomas
    function initLanguageSelector() {
        $('.gta6-language-button').on('click', function() {
            const language = $(this).data('language');
            
            // Enviar solicitação AJAX para alterar o idioma
            $.ajax({
                url: gta6_lang_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'gta6_change_language',
                    language: language,
                    nonce: gta6_lang_vars.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Recarregar a página para aplicar o novo idioma
                        location.reload();
                    } else {
                        console.error('Erro ao alterar idioma:', response.data);
                    }
                },
                error: function() {
                    console.error('Erro ao processar solicitação de alteração de idioma');
                }
            });
        });
    }

    // Inicializar quando o documento estiver pronto
    $(document).ready(function() {
        initLanguageSelector();
    });

})(jQuery);
