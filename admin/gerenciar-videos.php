<?php
/**
 * Gerenciador de vídeos do GTA VI Ultimate
 */

// Se este arquivo é chamado diretamente, aborta.
if (!defined('WPINC')) {
    die;
}

/**
 * Função para renderizar a página de gerenciamento de vídeos
 */
function gta6_admin_videos_page() {
    // Verificar se o usuário tem permissão
    if (!current_user_can('manage_options')) {
        wp_die(__('Você não tem permissão para acessar esta página.', 'gta6-ultimate'));
    }
    
    // Obter vídeo para edição (se houver)
    $edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
    $video_item = null;
    
    if ($edit_id > 0) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'gta6_videos';
        $video_item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $edit_id));
    }
    ?>
    <div class="wrap gta6-admin">
        <h1>
            <?php if ($video_item): ?>
                <span class="dashicons dashicons-edit"></span> Editar Vídeo
            <?php else: ?>
                <span class="dashicons dashicons-video-alt3"></span> Gerenciar Vídeos
            <?php endif; ?>
        </h1>
        
        <?php if ($video_item): ?>
            <!-- Formulário de edição -->
            <div class="gta6-admin-form-container">
                <form id="gta6-video-form" class="gta6-admin-form">
                    <input type="hidden" name="id" value="<?php echo esc_attr($video_item->id); ?>">
                    
                    <div class="gta6-admin-form-field">
                        <label for="title">Título</label>
                        <input type="text" id="title" name="title" value="<?php echo esc_attr($video_item->title); ?>" required>
                    </div>
                    
                    <div class="gta6-admin-form-field">
                        <label for="description">Descrição</label>
                        <textarea id="description" name="description" rows="4"><?php echo esc_textarea($video_item->description); ?></textarea>
                    </div>
                    
                    <div class="gta6-admin-form-row">
                        <div class="gta6-admin-form-field">
                            <label for="video_type">Tipo de Vídeo</label>
                            <select id="video_type" name="video_type">
                                <option value="youtube" <?php selected($video_item->video_type, 'youtube'); ?>>YouTube</option>
                                <option value="mp4" <?php selected($video_item->video_type, 'mp4'); ?>>MP4</option>
                            </select>
                        </div>
                        
                        <div class="gta6-admin-form-field">
                            <label for="video_url">URL do Vídeo</label>
                            <input type="text" id="video_url" name="video_url" value="<?php echo esc_url($video_item->video_url); ?>" required>
                            <p class="description">Para YouTube, insira o ID do vídeo ou URL completa. Para MP4, insira a URL do arquivo.</p>
                        </div>
                    </div>
                    
                    <div class="gta6-admin-form-field">
                        <label for="thumbnail_url">URL da Miniatura</label>
                        <div class="gta6-admin-media-field">
                            <input type="text" id="thumbnail_url" name="thumbnail_url" value="<?php echo esc_url($video_item->thumbnail_url); ?>">
                            <button type="button" class="button gta6-media-button" data-target="thumbnail_url">Selecionar Imagem</button>
                        </div>
                        <?php if (!empty($video_item->thumbnail_url)): ?>
                            <div class="gta6-admin-image-preview">
                                <img src="<?php echo esc_url($video_item->thumbnail_url); ?>" alt="Preview">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="gta6-admin-form-actions">
                        <button type="submit" class="button button-primary">Salvar Vídeo</button>
                        <a href="<?php echo admin_url('admin.php?page=gta6-ultimate-videos'); ?>" class="button">Cancelar</a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <!-- Formulário de adição -->
            <div class="gta6-admin-form-container">
                <form id="gta6-video-form" class="gta6-admin-form">
                    <input type="hidden" name="id" value="0">
                    
                    <div class="gta6-admin-form-field">
                        <label for="title">Título</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    
                    <div class="gta6-admin-form-field">
                        <label for="description">Descrição</label>
                        <textarea id="description" name="description" rows="4"></textarea>
                    </div>
                    
                    <div class="gta6-admin-form-row">
                        <div class="gta6-admin-form-field">
                            <label for="video_type">Tipo de Vídeo</label>
                            <select id="video_type" name="video_type">
                                <option value="youtube">YouTube</option>
                                <option value="mp4">MP4</option>
                            </select>
                        </div>
                        
                        <div class="gta6-admin-form-field">
                            <label for="video_url">URL do Vídeo</label>
                            <input type="text" id="video_url" name="video_url" required>
                            <p class="description">Para YouTube, insira o ID do vídeo ou URL completa. Para MP4, insira a URL do arquivo.</p>
                        </div>
                    </div>
                    
                    <div class="gta6-admin-form-field">
                        <label for="thumbnail_url">URL da Miniatura</label>
                        <div class="gta6-admin-media-field">
                            <input type="text" id="thumbnail_url" name="thumbnail_url">
                            <button type="button" class="button gta6-media-button" data-target="thumbnail_url">Selecionar Imagem</button>
                        </div>
                        <div class="gta6-admin-image-preview"></div>
                    </div>
                    
                    <div class="gta6-admin-form-actions">
                        <button type="submit" class="button button-primary">Adicionar Vídeo</button>
                    </div>
                </form>
            </div>
            
            <!-- Listagem de vídeos -->
            <div class="gta6-admin-list-container">
                <h2>Vídeos Existentes</h2>
                
                <div id="gta6-videos-list">
                    <?php
                    global $wpdb;
                    $table_name = $wpdb->prefix . 'gta6_videos';
                    $items = $wpdb->get_results("SELECT * FROM $table_name ORDER BY date DESC");
                    
                    if (!empty($items)):
                    ?>
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th>Miniatura</th>
                                    <th>Título</th>
                                    <th>Tipo</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($item->thumbnail_url)): ?>
                                                <img src="<?php echo esc_url($item->thumbnail_url); ?>" alt="<?php echo esc_attr($item->title); ?>" style="max-width: 100px; height: auto;">
                                            <?php else: ?>
                                                <div style="width: 100px; height: 56px; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                                    <span class="dashicons dashicons-format-video"></span>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo esc_html($item->title); ?>
                                        </td>
                                        <td>
                                            <?php echo esc_html($item->video_type); ?>
                                        </td>
                                        <td>
                                            <?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($item->date)); ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo admin_url('admin.php?page=gta6-ultimate-videos&edit=' . $item->id); ?>" class="button button-small">
                                                <span class="dashicons dashicons-edit"></span> Editar
                                            </a>
                                            <button type="button" class="button button-small gta6-delete-item" data-id="<?php echo $item->id; ?>" data-type="videos">
                                                <span class="dashicons dashicons-trash"></span> Excluir
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Nenhum vídeo cadastrado.</p>
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
            $('#thumbnail_url').on('change', function() {
                const url = $(this).val();
                const previewContainer = $(this).closest('.gta6-admin-form-field').find('.gta6-admin-image-preview');
                
                if (url) {
                    previewContainer.html('<img src="' + url + '" alt="Preview">');
                } else {
                    previewContainer.empty();
                }
            });
            
            // Salvar vídeo
            $('#gta6-video-form').on('submit', function(e) {
                e.preventDefault();
                
                const form = $(this);
                const submitButton = form.find('button[type="submit"]');
                submitButton.prop('disabled', true).text('Salvando...');
                
                // Enviar dados via AJAX
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'gta6_save_video',
                        id: form.find('input[name="id"]').val(),
                        title: form.find('input[name="title"]').val(),
                        description: form.find('textarea[name="description"]').val(),
                        video_type: form.find('select[name="video_type"]').val(),
                        video_url: form.find('input[name="video_url"]').val(),
                        thumbnail_url: form.find('input[name="thumbnail_url"]').val(),
                        nonce: '<?php echo wp_create_nonce('gta6-ultimate-nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Redirecionar para a listagem
                            window.location.href = '<?php echo admin_url('admin.php?page=gta6-ultimate-videos&saved=1'); ?>';
                        } else {
                            alert('Erro ao salvar vídeo: ' + response.data);
                            submitButton.prop('disabled', false).text('Salvar Vídeo');
                        }
                    },
                    error: function() {
                        alert('Erro ao processar a solicitação.');
                        submitButton.prop('disabled', false).text('Salvar Vídeo');
                    }
                });
            });
        });
    </script>
</div>
<?php
}
