<?php
/**
 * Gerenciador de planos de fundo do GTA VI Ultimate
 */

// Se este arquivo é chamado diretamente, aborta.
if (!defined('WPINC')) {
    die;
}

/**
 * Função para renderizar a página de gerenciamento de planos de fundo
 */
function gta6_admin_backgrounds_page() {
    // Verificar se o usuário tem permissão
    if (!current_user_can('manage_options')) {
        wp_die(__('Você não tem permissão para acessar esta página.', 'gta6-ultimate'));
    }
    
    // Obter plano de fundo para edição (se houver)
    $edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
    $background_item = null;
    
    if ($edit_id > 0) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'gta6_backgrounds';
        $background_item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $edit_id));
    }
    ?>
    <div class="wrap gta6-admin">
        <h1>
            <?php if ($background_item): ?>
                <span class="dashicons dashicons-edit"></span> Editar Plano de Fundo
            <?php else: ?>
                <span class="dashicons dashicons-admin-appearance"></span> Gerenciar Planos de Fundo
            <?php endif; ?>
        </h1>
        
        <?php if ($background_item): ?>
            <!-- Formulário de edição -->
            <div class="gta6-admin-form-container">
                <form id="gta6-background-form" class="gta6-admin-form">
                    <input type="hidden" name="id" value="<?php echo esc_attr($background_item->id); ?>">
                    
                    <div class="gta6-admin-form-field">
                        <label for="title">Título</label>
                        <input type="text" id="title" name="title" value="<?php echo esc_attr($background_item->title); ?>" required>
                    </div>
                    
                    <div class="gta6-admin-form-field">
                        <label for="image_url">URL da Imagem</label>
                        <div class="gta6-admin-media-field">
                            <input type="text" id="image_url" name="image_url" value="<?php echo esc_url($background_item->image_url); ?>" required>
                            <button type="button" class="button gta6-media-button" data-target="image_url">Selecionar Imagem</button>
                        </div>
                        <?php if (!empty($background_item->image_url)): ?>
                            <div class="gta6-admin-image-preview">
                                <img src="<?php echo esc_url($background_item->image_url); ?>" alt="Preview">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="gta6-admin-form-actions">
                        <button type="submit" class="button button-primary">Salvar Plano de Fundo</button>
                        <a href="<?php echo admin_url('admin.php?page=gta6-ultimate-backgrounds'); ?>" class="button">Cancelar</a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <!-- Formulário de adição -->
            <div class="gta6-admin-form-container">
                <form id="gta6-background-form" class="gta6-admin-form">
                    <input type="hidden" name="id" value="0">
                    
                    <div class="gta6-admin-form-field">
                        <label for="title">Título</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    
                    <div class="gta6-admin-form-field">
                        <label for="image_url">URL da Imagem</label>
                        <div class="gta6-admin-media-field">
                            <input type="text" id="image_url" name="image_url" required>
                            <button type="button" class="button gta6-media-button" data-target="image_url">Selecionar Imagem</button>
                        </div>
                        <div class="gta6-admin-image-preview"></div>
                    </div>
                    
                    <div class="gta6-admin-form-actions">
                        <button type="submit" class="button button-primary">Adicionar Plano de Fundo</button>
                    </div>
                </form>
            </div>
            
            <!-- Listagem de planos de fundo -->
            <div class="gta6-admin-list-container">
                <h2>Planos de Fundo Existentes</h2>
                
                <div id="gta6-backgrounds-list">
                    <?php
                    global $wpdb;
                    $table_name = $wpdb->prefix . 'gta6_backgrounds';
                    $items = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC");
                    
                    if (!empty($items)):
                    ?>
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th>Imagem</th>
                                    <th>Título</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($item->image_url)): ?>
                                                <img src="<?php echo esc_url($item->image_url); ?>" alt="<?php echo esc_attr($item->title); ?>" style="max-width: 100px; height: auto;">
                                            <?php else: ?>
                                                <div style="width: 100px; height: 56px; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                                    <span class="dashicons dashicons-format-image"></span>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo esc_html($item->title); ?>
                                        </td>
                                        <td>
                                            <?php if ($item->is_active): ?>
                                                <span class="gta6-active-badge">Ativo</span>
                                            <?php else: ?>
                                                <button type="button" class="button button-small gta6-activate-background" data-id="<?php echo $item->id; ?>">
                                                    Ativar
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo admin_url('admin.php?page=gta6-ultimate-backgrounds&edit=' . $item->id); ?>" class="button button-small">
                                                <span class="dashicons dashicons-edit"></span> Editar
                                            </a>
                                            <button type="button" class="button button-small gta6-delete-item" data-id="<?php echo $item->id; ?>" data-type="backgrounds">
                                                <span class="dashicons dashicons-trash"></span> Excluir
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Nenhum plano de fundo cadastrado.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        jQuery(document).ready(function($) {
            // Seletor de mídia para imagens
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
            
            // Preview de imagem ao digitar URL
            $('#image_url').on('change', function() {
                const url = $(this).val();
                const previewContainer = $(this).closest('.gta6-admin-form-field').find('.gta6-admin-image-preview');
                
                if (url) {
                    previewContainer.html('<img src="' + url + '" alt="Preview">');
                } else {
                    previewContainer.empty();
                }
            });
            
            // Salvar plano de fundo
            $('#gta6-background-form').on('submit', function(e) {
                e.preventDefault();
                
                const form = $(this);
                const submitButton = form.find('button[type="submit"]');
                submitButton.prop('disabled', true).text('Salvando...');
                
                // Enviar dados via AJAX
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'gta6_save_background',
                        id: form.find('input[name="id"]').val(),
                        title: form.find('input[name="title"]').val(),
                        image_url: form.find('input[name="image_url"]').val(),
                        nonce: '<?php echo wp_create_nonce('gta6-ultimate-nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Redirecionar para a listagem
                            window.location.href = '<?php echo admin_url('admin.php?page=gta6-ultimate-backgrounds&saved=1'); ?>';
                        } else {
                            alert('Erro ao salvar plano de fundo: ' + response.data);
                            submitButton.prop('disabled', false).text('Salvar Plano de Fundo');
                        }
                    },
                    error: function() {
                        alert('Erro ao processar a solicitação.');
                        submitButton.prop('disabled', false).text('Salvar Plano de Fundo');
                    }
                });
            });
            
            // Ativar plano de fundo
            $('.gta6-activate-background').on('click', function() {
                const button = $(this);
                const id = button.data('id');
                
                button.prop('disabled', true).text('Ativando...');
                
                // Enviar dados via AJAX
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'gta6_activate_background',
                        id: id,
                        nonce: '<?php echo wp_create_nonce('gta6-ultimate-nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Recarregar a página
                            window.location.reload();
                        } else {
                            alert('Erro ao ativar plano de fundo: ' + response.data);
                            button.prop('disabled', false).text('Ativar');
                        }
                    },
                    error: function() {
                        alert('Erro ao processar a solicitação.');
                        button.prop('disabled', false).text('Ativar');
                    }
                });
            });
        });
    </script>
</div>
<?php
}
