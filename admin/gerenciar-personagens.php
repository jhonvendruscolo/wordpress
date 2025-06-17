<?php
/**
 * Gerenciador de personagens para o GTA VI Ultimate
 * Permite adicionar, editar e excluir personagens do jogo
 */

// Se este arquivo é chamado diretamente, aborta.
if (!defined('WPINC')) {
    die;
}

/**
 * Página de administração para gerenciar personagens
 */
function gta6_admin_characters_page() {
    // Verificar permissões
    if (!current_user_can('manage_options')) {
        wp_die(__('Você não tem permissão para acessar esta página.', 'gta6-ultimate'));
    }

    // Obter personagens existentes
    global $wpdb;
    $table_name = $wpdb->prefix . 'gta6_characters';
    $characters = $wpdb->get_results("SELECT * FROM $table_name ORDER BY display_order ASC, id DESC");

    // Incluir scripts e estilos do WordPress
    wp_enqueue_media();
    ?>
    <div class="wrap">
        <h1><?php _e('Gerenciar Personagens', 'gta6-ultimate'); ?></h1>
        
        <div class="notice notice-info">
            <p><?php _e('Aqui você pode gerenciar os personagens do GTA VI que serão exibidos no seu site.', 'gta6-ultimate'); ?></p>
        </div>
        
        <h2><?php _e('Adicionar Novo Personagem', 'gta6-ultimate'); ?></h2>
        <form method="post" action="" enctype="multipart/form-data">
            <?php wp_nonce_field('gta6_save_character', 'gta6_character_nonce'); ?>
            <input type="hidden" name="action" value="gta6_save_character">
            <input type="hidden" name="character_id" value="0">
            
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="character_name"><?php _e('Nome', 'gta6-ultimate'); ?></label></th>
                    <td>
                        <input type="text" id="character_name" name="character_name" class="regular-text" required>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="character_description"><?php _e('Descrição', 'gta6-ultimate'); ?></label></th>
                    <td>
                        <textarea id="character_description" name="character_description" class="large-text" rows="3" required></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="character_image"><?php _e('Imagem', 'gta6-ultimate'); ?></label></th>
                    <td>
                        <input type="hidden" id="character_image" name="character_image" class="regular-text" required>
                        <div id="character-image-preview" style="max-width: 300px; margin-bottom: 10px;"></div>
                        <button type="button" id="upload-character-image" class="button"><?php _e('Selecionar Imagem', 'gta6-ultimate'); ?></button>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="character_role"><?php _e('Papel', 'gta6-ultimate'); ?></label></th>
                    <td>
                        <input type="text" id="character_role" name="character_role" class="regular-text">
                        <p class="description"><?php _e('Ex: Protagonista, Antagonista, etc.', 'gta6-ultimate'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="character_order"><?php _e('Ordem de Exibição', 'gta6-ultimate'); ?></label></th>
                    <td>
                        <input type="number" id="character_order" name="character_order" class="small-text" min="0" value="0">
                        <p class="description"><?php _e('Menor número = maior prioridade', 'gta6-ultimate'); ?></p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(__('Adicionar Personagem', 'gta6-ultimate'), 'primary', 'submit_character'); ?>
        </form>
        
        <hr>
        
        <h2><?php _e('Personagens Cadastrados', 'gta6-ultimate'); ?></h2>
        
        <?php if (empty($characters)) : ?>
            <p><?php _e('Nenhum personagem encontrado. Adicione um novo acima.', 'gta6-ultimate'); ?></p>
        <?php else : ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Imagem', 'gta6-ultimate'); ?></th>
                        <th><?php _e('Nome', 'gta6-ultimate'); ?></th>
                        <th><?php _e('Papel', 'gta6-ultimate'); ?></th>
                        <th><?php _e('Ordem', 'gta6-ultimate'); ?></th>
                        <th><?php _e('Ações', 'gta6-ultimate'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($characters as $character) : ?>
                        <tr>
                            <td>
                                <img src="<?php echo esc_url($character->image_url); ?>" alt="<?php echo esc_attr($character->name); ?>" style="max-width: 100px; max-height: 100px;">
                            </td>
                            <td>
                                <strong><?php echo esc_html($character->name); ?></strong>
                                <div class="row-actions">
                                    <span class="edit">
                                        <a href="#" class="edit-character" 
                                           data-id="<?php echo esc_attr($character->id); ?>"
                                           data-name="<?php echo esc_attr($character->name); ?>"
                                           data-description="<?php echo esc_attr($character->description); ?>"
                                           data-image="<?php echo esc_url($character->image_url); ?>"
                                           data-role="<?php echo esc_attr($character->role); ?>"
                                           data-order="<?php echo esc_attr($character->display_order); ?>">
                                            <?php _e('Editar', 'gta6-ultimate'); ?>
                                        </a> |
                                    </span>
                                    <span class="delete">
                                        <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=gta6-ultimate-characters&action=delete&id=' . $character->id), 'delete_character_' . $character->id); ?>" class="delete-character" onclick="return confirm('<?php _e('Tem certeza de que deseja excluir este personagem?', 'gta6-ultimate'); ?>')">
                                            <?php _e('Excluir', 'gta6-ultimate'); ?>
                                        </a>
                                    </span>
                                </div>
                            </td>
                            <td><?php echo esc_html($character->role); ?></td>
                            <td><?php echo esc_html($character->display_order); ?></td>
                            <td>
                                <a href="#" class="button edit-character" 
                                   data-id="<?php echo esc_attr($character->id); ?>"
                                   data-name="<?php echo esc_attr($character->name); ?>"
                                   data-description="<?php echo esc_attr($character->description); ?>"
                                   data-image="<?php echo esc_url($character->image_url); ?>"
                                   data-role="<?php echo esc_attr($character->role); ?>"
                                   data-order="<?php echo esc_attr($character->display_order); ?>">
                                    <?php _e('Editar', 'gta6-ultimate'); ?>
                                </a>
                                <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=gta6-ultimate-characters&action=delete&id=' . $character->id), 'delete_character_' . $character->id); ?>" class="button delete-character" onclick="return confirm('<?php _e('Tem certeza de que deseja excluir este personagem?', 'gta6-ultimate'); ?>')">
                                    <?php _e('Excluir', 'gta6-ultimate'); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Inicializar o media uploader
        var mediaUploader;
        
        $('#upload-character-image').on('click', function(e) {
            e.preventDefault();
            
            // Se o uploader já existe, abra-o
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            
            // Criar o uploader
            mediaUploader = wp.media({
                title: '<?php _e('Selecionar Imagem do Personagem', 'gta6-ultimate'); ?>',
                button: {
                    text: '<?php _e('Usar esta imagem', 'gta6-ultimate'); ?>'
                },
                multiple: false
            });
            
            // Quando uma imagem é selecionada
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#character_image').val(attachment.url);
                $('#character-image-preview').html('<img src="' + attachment.url + '" alt="" style="max-width: 100%;">');
            });
            
            // Abrir o uploader
            mediaUploader.open();
        });
        
        // Editar personagem
        $('.edit-character').on('click', function(e) {
            e.preventDefault();
            
            var id = $(this).data('id');
            var name = $(this).data('name');
            var description = $(this).data('description');
            var image = $(this).data('image');
            var role = $(this).data('role');
            var order = $(this).data('order');
            
            // Preencher o formulário
            $('#character_name').val(name);
            $('#character_description').val(description);
            $('#character_image').val(image);
            $('#character-image-preview').html('<img src="' + image + '" alt="" style="max-width: 100%;">');
            $('#character_role').val(role);
            $('#character_order').val(order);
            $('input[name="character_id"]').val(id);
            
            // Alterar o texto do botão
            $('input[name="submit_character"]').val('<?php _e('Atualizar Personagem', 'gta6-ultimate'); ?>');
            
            // Rolar para o formulário
            $('html, body').animate({
                scrollTop: $('form').offset().top - 50
            }, 500);
        });
    });
    </script>
    <?php
}

/**
 * Processar formulário de personagem
 */
function gta6_process_character_form() {
    // Verificar se é uma submissão de formulário de personagem
    if (!isset($_POST['action']) || $_POST['action'] != 'gta6_save_character') {
        return;
    }
    
    // Verificar nonce
    if (!isset($_POST['gta6_character_nonce']) || !wp_verify_nonce($_POST['gta6_character_nonce'], 'gta6_save_character')) {
        wp_die(__('Verificação de segurança falhou.', 'gta6-ultimate'));
    }
    
    // Verificar permissões
    if (!current_user_can('manage_options')) {
        wp_die(__('Você não tem permissão para realizar esta ação.', 'gta6-ultimate'));
    }
    
    // Obter dados do formulário
    $character_id = isset($_POST['character_id']) ? intval($_POST['character_id']) : 0;
    $name = isset($_POST['character_name']) ? sanitize_text_field($_POST['character_name']) : '';
    $description = isset($_POST['character_description']) ? sanitize_textarea_field($_POST['character_description']) : '';
    $image_url = isset($_POST['character_image']) ? esc_url_raw($_POST['character_image']) : '';
    $role = isset($_POST['character_role']) ? sanitize_text_field($_POST['character_role']) : '';
    $display_order = isset($_POST['character_order']) ? intval($_POST['character_order']) : 0;
    
    // Validar dados
    if (empty($name) || empty($description) || empty($image_url)) {
        add_settings_error('gta6_characters', 'required-fields', __('Nome, descrição e imagem são obrigatórios.', 'gta6-ultimate'), 'error');
        return;
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'gta6_characters';
    
    // Preparar dados
    $data = array(
        'name' => $name,
        'description' => $description,
        'image_url' => $image_url,
        'role' => $role,
        'display_order' => $display_order
    );
    
    // Formato dos dados
    $format = array('%s', '%s', '%s', '%s', '%d');
    
    // Inserir ou atualizar
    if ($character_id > 0) {
        // Atualizar
        $wpdb->update(
            $table_name,
            $data,
            array('id' => $character_id),
            $format,
            array('%d')
        );
        
        add_settings_error('gta6_characters', 'character-updated', __('Personagem atualizado com sucesso.', 'gta6-ultimate'), 'success');
    } else {
        // Inserir
        $data['date'] = current_time('mysql');
        $format[] = '%s';
        
        $wpdb->insert(
            $table_name,
            $data,
            $format
        );
        
        add_settings_error('gta6_characters', 'character-added', __('Personagem adicionado com sucesso.', 'gta6-ultimate'), 'success');
    }
    
    // Redirecionar para evitar reenvio do formulário
    wp_redirect(admin_url('admin.php?page=gta6-ultimate-characters&updated=true'));
    exit;
}
add_action('admin_init', 'gta6_process_character_form');

/**
 * Processar exclusão de personagem
 */
function gta6_process_character_delete() {
    // Verificar se é uma solicitação de exclusão
    if (!isset($_GET['action']) || $_GET['action'] != 'delete' || !isset($_GET['id'])) {
        return;
    }
    
    // Obter ID
    $character_id = intval($_GET['id']);
    
    // Verificar nonce
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'delete_character_' . $character_id)) {
        wp_die(__('Verificação de segurança falhou.', 'gta6-ultimate'));
    }
    
    // Verificar permissões
    if (!current_user_can('manage_options')) {
        wp_die(__('Você não tem permissão para realizar esta ação.', 'gta6-ultimate'));
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'gta6_characters';
    
    // Excluir personagem
    $wpdb->delete(
        $table_name,
        array('id' => $character_id),
        array('%d')
    );
    
    // Redirecionar
    wp_redirect(admin_url('admin.php?page=gta6-ultimate-characters&deleted=true'));
    exit;
}
add_action('admin_init', 'gta6_process_character_delete');

/**
 * Exibir mensagens de sucesso/erro
 */
function gta6_characters_admin_notices() {
    // Verificar se estamos na página correta
    $screen = get_current_screen();
    if ($screen->id != 'gta6-ultimate_page_gta6-ultimate-characters') {
        return;
    }
    
    // Exibir mensagens
    settings_errors('gta6_characters');
    
    // Mensagem de exclusão
    if (isset($_GET['deleted']) && $_GET['deleted'] == 'true') {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Personagem excluído com sucesso.', 'gta6-ultimate'); ?></p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'gta6_characters_admin_notices');
