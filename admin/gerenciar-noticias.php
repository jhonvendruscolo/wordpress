<?php
/**
 * Gerenciador de notícias do GTA VI Ultimate
 */

// Se este arquivo é chamado diretamente, aborta.
if (!defined('WPINC')) {
    die;
}

/**
 * Função para renderizar a página de gerenciamento de notícias
 */
function gta6_admin_news_page() {
    // Verificar se o usuário tem permissão
    if (!current_user_can('manage_options')) {
        wp_die(__('Você não tem permissão para acessar esta página.', 'gta6-ultimate'));
    }
    
    // Obter notícia para edição (se houver)
    $edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
    $news_item = null;
    
    if ($edit_id > 0) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'gta6_news';
        $news_item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $edit_id));
    }
    ?>
    <div class="wrap gta6-admin">
        <h1>
            <?php if ($news_item): ?>
                <span class="dashicons dashicons-edit"></span> Editar Notícia
            <?php else: ?>
                <span class="dashicons dashicons-admin-post"></span> Gerenciar Notícias
            <?php endif; ?>
        </h1>
        
        <?php if ($news_item): ?>
            <!-- Formulário de edição -->
            <div class="gta6-admin-form-container">
                <form id="gta6-news-form" class="gta6-admin-form">
                    <input type="hidden" name="id" value="<?php echo esc_attr($news_item->id); ?>">
                    
                    <div class="gta6-admin-form-field">
                        <label for="title">Título</label>
                        <input type="text" id="title" name="title" value="<?php echo esc_attr($news_item->title); ?>" required>
                    </div>
                    
                    <div class="gta6-admin-form-field">
                        <label for="content">Conteúdo</label>
                        <?php
                        wp_editor(
                            $news_item->content,
                            'content',
                            array(
                                'textarea_name' => 'content',
                                'media_buttons' => true,
                                'textarea_rows' => 10,
                                'teeny' => false
                            )
                        );
                        ?>
                    </div>
                    
                    <div class="gta6-admin-form-row">
                        <div class="gta6-admin-form-field">
                            <label for="category">Categoria</label>
                            <select id="category" name="category">
                                <option value="Geral" <?php selected($news_item->category, 'Geral'); ?>>Geral</option>
                                <option value="Jason" <?php selected($news_item->category, 'Jason'); ?>>Jason</option>
                                <option value="Lucia" <?php selected($news_item->category, 'Lucia'); ?>>Lucia</option>
                                <option value="Vice City" <?php selected($news_item->category, 'Vice City'); ?>>Vice City</option>
                            </select>
                        </div>
                        
                        <div class="gta6-admin-form-field">
                            <label for="image_url">URL da Imagem</label>
                            <div class="gta6-admin-media-field">
                                <input type="text" id="image_url" name="image_url" value="<?php echo esc_url($news_item->image_url); ?>">
                                <button type="button" class="button gta6-media-button" data-target="image_url">Selecionar Imagem</button>
                            </div>
                            <?php if (!empty($news_item->image_url)): ?>
                                <div class="gta6-admin-image-preview">
                                    <img src="<?php echo esc_url($news_item->image_url); ?>" alt="Preview">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="gta6-admin-form-actions">
                        <button type="submit" class="button button-primary">Salvar Notícia</button>
                        <a href="<?php echo admin_url('admin.php?page=gta6-ultimate-news'); ?>" class="button">Cancelar</a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <!-- Formulário de adição -->
            <div class="gta6-admin-form-container">
                <form id="gta6-news-form" class="gta6-admin-form">
                    <input type="hidden" name="id" value="0">
                    
                    <div class="gta6-admin-form-field">
                        <label for="title">Título</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    
                    <div class="gta6-admin-form-field">
                        <label for="content">Conteúdo</label>
                        <?php
                        wp_editor(
                            '',
                            'content',
                            array(
                                'textarea_name' => 'content',
                                'media_buttons' => true,
                                'textarea_rows' => 10,
                                'teeny' => false
                            )
                        );
                        ?>
                    </div>
                    
                    <div class="gta6-admin-form-row">
                        <div class="gta6-admin-form-field">
                            <label for="category">Categoria</label>
                            <select id="category" name="category">
                                <option value="Geral">Geral</option>
                                <option value="Jason">Jason</option>
                                <option value="Lucia">Lucia</option>
                                <option value="Vice City">Vice City</option>
                            </select>
                        </div>
                        
                        <div class="gta6-admin-form-field">
                            <label for="image_url">URL da Imagem</label>
                            <div class="gta6-admin-media-field">
                                <input type="text" id="image_url" name="image_url">
                                <button type="button" class="button gta6-media-button" data-target="image_url">Selecionar Imagem</button>
                            </div>
                            <div class="gta6-admin-image-preview"></div>
                        </div>
                    </div>
                    
                    <div class="gta6-admin-form-actions">
                        <button type="submit" class="button button-primary">Adicionar Notícia</button>
                    </div>
                </form>
            </div>
            
            <!-- Listagem de notícias -->
            <div class="gta6-admin-list-container">
                <h2>Notícias Existentes</h2>
                
                <div id="gta6-news-list">
                    <?php
                    global $wpdb;
                    $table_name = $wpdb->prefix . 'gta6_news';
                    $items = $wpdb->get_results("SELECT * FROM $table_name ORDER BY date DESC");
                    
                    if (!empty($items)):
                    ?>
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Categoria</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td>
                                            <?php echo esc_html($item->title); ?>
                                        </td>
                                        <td>
                                            <?php echo esc_html($item->category); ?>
                                        </td>
                                        <td>
                                            <?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($item->date)); ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo admin_url('admin.php?page=gta6-ultimate-news&edit=' . $item->id); ?>" class="button button-small">
                                                <span class="dashicons dashicons-edit"></span> Editar
                                            </a>
                                            <button type="button" class="button button-small gta6-delete-item" data-id="<?php echo $item->id; ?>" data-type="news">
                                                <span class="dashicons dashicons-trash"></span> Excluir
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Nenhuma notícia cadastrada.</p>
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
            
            // Salvar notícia
            $('#gta6-news-form').on('submit', function(e) {
                e.preventDefault();
                
                const form = $(this);
                const submitButton = form.find('button[type="submit"]');
                submitButton.prop('disabled', true).text('Salvando...');
                
                // Obter conteúdo do editor
                if (typeof tinyMCE !== 'undefined' && tinyMCE.get('content')) {
                    const content = tinyMCE.get('content').getContent();
                    $('#content').val(content);
                }
                
                // Enviar dados via AJAX
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'gta6_save_news',
                        id: form.find('input[name="id"]').val(),
                        title: form.find('input[name="title"]').val(),
                        content: form.find('textarea[name="content"]').val(),
                        category: form.find('select[name="category"]').val(),
                        image_url: form.find('input[name="image_url"]').val(),
                        nonce: '<?php echo wp_create_nonce('gta6-ultimate-nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Redirecionar para a listagem
                            window.location.href = '<?php echo admin_url('admin.php?page=gta6-ultimate-news&saved=1'); ?>';
                        } else {
                            alert('Erro ao salvar notícia: ' + response.data);
                            submitButton.prop('disabled', false).text('Salvar Notícia');
                        }
                    },
                    error: function() {
                        alert('Erro ao processar a solicitação.');
                        submitButton.prop('disabled', false).text('Salvar Notícia');
                    }
                });
            });
        });
    </script>
</div>
<?php
}
