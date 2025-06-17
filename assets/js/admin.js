/**
 * GTA VI Ultimate - Script para o painel administrativo
 * Gerencia interações AJAX e outras funcionalidades do painel
 */

jQuery(document).ready(function($) {
    // Inicializar Media Uploader do WordPress
    $('.gta6-media-button').on('click', function() {
        const targetInput = $(this).data('target');
        
        const mediaUploader = wp.media({
            title: 'Selecionar Imagem',
            button: {
                text: 'Usar esta imagem'
            },
            multiple: false
        });
        
        mediaUploader.on('select', function() {
            const attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#' + targetInput).val(attachment.url);
            
            // Atualizar preview
            const previewContainer = $('#' + targetInput).closest('.gta6-admin-form-field').find('.gta6-admin-image-preview');
            previewContainer.html('<img src="' + attachment.url + '" alt="Preview">');
        });
        
        mediaUploader.open();
    });
    
    // Exclusão de itens
    $('.gta6-delete-item').on('click', function() {
        const button = $(this);
        const id = button.data('id');
        const type = button.data('type');
        
        if (!id || !type) {
            alert('Erro: Dados inválidos para exclusão.');
            return;
        }
        
        // Confirmar exclusão
        if (!confirm('Tem certeza que deseja excluir este item? Esta ação não pode ser desfeita.')) {
            return;
        }
        
        button.prop('disabled', true).html('<span class="dashicons dashicons-update gta6-spin"></span> Excluindo...');
        
        // Enviar solicitação AJAX
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'gta6_delete_item',
                id: id,
                type: type,
                nonce: gta6_admin_vars.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Remover linha da tabela
                    button.closest('tr').fadeOut(400, function() {
                        $(this).remove();
                        
                        // Verificar se a tabela está vazia
                        const tbody = $('tbody');
                        if (tbody.children().length === 0) {
                            tbody.html('<tr><td colspan="5">Nenhum item encontrado.</td></tr>');
                        }
                    });
                } else {
                    alert('Erro ao excluir: ' + response.data);
                    button.prop('disabled', false).html('<span class="dashicons dashicons-trash"></span> Excluir');
                }
            },
            error: function() {
                alert('Erro ao processar a solicitação.');
                button.prop('disabled', false).html('<span class="dashicons dashicons-trash"></span> Excluir');
            }
        });
    });
    
    // Exportar inscritos da newsletter
    $('#gta6-export-subscribers').on('click', function(e) {
        e.preventDefault();
        
        const button = $(this);
        button.prop('disabled', true).text('Exportando...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'gta6_export_subscribers',
                nonce: gta6_admin_vars.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Criar link de download
                    const a = document.createElement('a');
                    const blob = new Blob([response.data], {type: 'text/csv'});
                    a.href = window.URL.createObjectURL(blob);
                    a.download = 'gta6_subscribers.csv';
                    a.click();
                } else {
                    alert('Erro ao exportar inscritos: ' + response.data);
                }
            },
            error: function() {
                alert('Erro ao processar a solicitação.');
            },
            complete: function() {
                button.prop('disabled', false).text('Exportar Lista (CSV)');
            }
        });
    });
});
