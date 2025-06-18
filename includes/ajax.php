<?php
/**
 * Funções AJAX para o GTA VI Ultimate
 * Manipula requisições AJAX para newsletter e outras funcionalidades
 */

// Se este arquivo é chamado diretamente, aborta.
if (!defined('WPINC')) {
    die;
}

/**
 * Inicializar funções AJAX
 */
function gta6_init_ajax() {
    // Adicionar handlers AJAX
    add_action('wp_ajax_gta6_subscribe_newsletter', 'gta6_ajax_subscribe_newsletter');
    add_action('wp_ajax_nopriv_gta6_subscribe_newsletter', 'gta6_ajax_subscribe_newsletter');
    
    // Adicionar handlers para salvar conteúdo
    add_action('wp_ajax_gta6_save_news', 'gta6_ajax_save_news');
    add_action('wp_ajax_gta6_save_video', 'gta6_ajax_save_video');
    add_action('wp_ajax_gta6_save_image', 'gta6_ajax_save_image');
    add_action('wp_ajax_gta6_save_background', 'gta6_ajax_save_background');
    add_action('wp_ajax_gta6_save_character', 'gta6_ajax_save_character');

    // Endpoints públicos
    add_action('wp_ajax_gta6_get_news', 'gta6_ajax_get_news');
    add_action('wp_ajax_nopriv_gta6_get_news', 'gta6_ajax_get_news');

    // Adicionar handler para exportar inscritos
    add_action('wp_ajax_gta6_export_subscribers', 'gta6_ajax_export_subscribers');
    
    // Adicionar handler para ativar plano de fundo
    add_action('wp_ajax_gta6_activate_background', 'gta6_ajax_activate_background');
}
add_action('init', 'gta6_init_ajax');

/**
 * Handler AJAX para inscrição na newsletter
 */
function gta6_ajax_subscribe_newsletter() {
    // Verificar nonce
    check_ajax_referer('gta6-ultimate-nonce', 'nonce');
    
    // Obter e-mail
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    
    // Validar e-mail
    if (empty($email) || !is_email($email)) {
        wp_send_json_error(array(
            'message' => __('Por favor, insira um e-mail válido.', 'gta6-ultimate')
        ));
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'gta6_newsletter';
    
    // Verificar se o e-mail já está cadastrado
    $existing = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table_name WHERE email = %s", $email));
    
    if ($existing) {
        wp_send_json_error(array(
            'message' => __('Este e-mail já está inscrito na nossa newsletter.', 'gta6-ultimate')
        ));
    }
    
    // Inserir e-mail
    $result = $wpdb->insert(
        $table_name,
        array(
            'email' => $email,
            'date' => current_time('mysql')
        ),
        array('%s', '%s')
    );
    
    if ($result) {
        // Enviar e-mail de confirmação (opcional)
        gta6_send_confirmation_email($email);
        
        wp_send_json_success(array(
            'message' => __('Obrigado! Seu e-mail foi cadastrado com sucesso.', 'gta6-ultimate')
        ));
    } else {
        wp_send_json_error(array(
            'message' => __('Ocorreu um erro ao cadastrar seu e-mail. Por favor, tente novamente.', 'gta6-ultimate')
        ));
    }
}

/**
 * Handler AJAX para exportar inscritos
 */
function gta6_ajax_export_subscribers() {
    // Verificar nonce
    check_ajax_referer('gta6-ultimate-nonce', 'nonce');
    
    // Verificar permissões
    if (!current_user_can('manage_options')) {
        wp_send_json_error(__('Você não tem permissão para realizar esta ação.', 'gta6-ultimate'));
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'gta6_newsletter';
    $subscribers = $wpdb->get_results("SELECT * FROM $table_name ORDER BY date DESC");
    
    // Criar CSV
    $csv = "Email,Data de Inscrição\n";
    
    foreach ($subscribers as $subscriber) {
        $csv .= '"' . $subscriber->email . '","' . $subscriber->date . "\"\n";
    }
    
    wp_send_json_success($csv);
}

/**
 * Handler AJAX para ativar plano de fundo
 */
function gta6_ajax_activate_background() {
    // Verificar nonce
    check_ajax_referer('gta6-ultimate-nonce', 'nonce');
    
    // Verificar permissões
    if (!current_user_can('manage_options')) {
        wp_send_json_error(__('Você não tem permissão para realizar esta ação.', 'gta6-ultimate'));
    }
    
    // Obter ID
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
    // Validar ID
    if (empty($id)) {
        wp_send_json_error(__('ID inválido.', 'gta6-ultimate'));
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'gta6_backgrounds';
    
    // Verificar se o plano de fundo existe
    $background = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
    if (!$background) {
        wp_send_json_error(__('Plano de fundo não encontrado.', 'gta6-ultimate'));
    }
    
    // Desativar todos os planos de fundo
    $wpdb->update(
        $table_name,
        array('is_active' => 0),
        array('1' => 1),
        array('%d'),
        array('%d')
    );
    
    // Ativar o plano de fundo selecionado
    $result = $wpdb->update(
        $table_name,
        array('is_active' => 1),
        array('id' => $id),
        array('%d'),
        array('%d')
    );
    
    if ($result === false) {
        wp_send_json_error(__('Erro ao ativar o plano de fundo.', 'gta6-ultimate'));
    }
    
    wp_send_json_success(array(
        'message' => __('Plano de fundo ativado com sucesso.', 'gta6-ultimate')
    ));
}

/**
 * Salvar notícia via AJAX
 */
function gta6_ajax_save_news() {
    check_ajax_referer('gta6-ultimate-nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(__('Você não tem permissão para realizar esta ação.', 'gta6-ultimate'));
    }

    $id        = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $title     = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
    $content   = isset($_POST['content']) ? wp_kses_post($_POST['content']) : '';
    $category  = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : 'Geral';
    $image_url = isset($_POST['image_url']) ? esc_url_raw($_POST['image_url']) : '';

    if (empty($title) || empty($content)) {
        wp_send_json_error(__('Título e conteúdo são obrigatórios.', 'gta6-ultimate'));
    }

    global $wpdb;
    $table = $wpdb->prefix . 'gta6_news';

    $data = array(
        'title'     => $title,
        'content'   => $content,
        'category'  => $category,
        'image_url' => $image_url,
    );

    $format = array('%s', '%s', '%s', '%s');

    if ($id > 0) {
        $wpdb->update($table, $data, array('id' => $id), $format, array('%d'));
    } else {
        $data['date'] = current_time('mysql');
        $format[] = '%s';
        $wpdb->insert($table, $data, $format);
    }

    GTA6_Ultimate::get_instance()->clear_plugin_cache();

    wp_send_json_success(array('message' => __('Notícia salva com sucesso.', 'gta6-ultimate')));
}

/**
 * Salvar vídeo via AJAX
 */
function gta6_ajax_save_video() {
    check_ajax_referer('gta6-ultimate-nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(__('Você não tem permissão para realizar esta ação.', 'gta6-ultimate'));
    }

    $id           = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $title        = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
    $description  = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
    $video_type   = isset($_POST['video_type']) ? sanitize_text_field($_POST['video_type']) : 'youtube';
    $video_url    = isset($_POST['video_url']) ? esc_url_raw($_POST['video_url']) : '';
    $thumbnail_url= isset($_POST['thumbnail_url']) ? esc_url_raw($_POST['thumbnail_url']) : '';

    if (empty($title) || empty($video_url)) {
        wp_send_json_error(__('Título e URL do vídeo são obrigatórios.', 'gta6-ultimate'));
    }

    global $wpdb;
    $table = $wpdb->prefix . 'gta6_videos';

    $data = array(
        'title'        => $title,
        'description'   => $description,
        'video_type'    => $video_type,
        'video_url'     => $video_url,
        'thumbnail_url' => $thumbnail_url,
    );

    $format = array('%s', '%s', '%s', '%s', '%s');

    if ($id > 0) {
        $wpdb->update($table, $data, array('id' => $id), $format, array('%d'));
    } else {
        $data['date'] = current_time('mysql');
        $format[] = '%s';
        $wpdb->insert($table, $data, $format);
    }

    GTA6_Ultimate::get_instance()->clear_plugin_cache();

    wp_send_json_success(array('message' => __('Vídeo salvo com sucesso.', 'gta6-ultimate')));
}

/**
 * Salvar imagem via AJAX
 */
function gta6_ajax_save_image() {
    check_ajax_referer('gta6-ultimate-nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(__('Você não tem permissão para realizar esta ação.', 'gta6-ultimate'));
    }

    $id        = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $title     = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
    $description= isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
    $category  = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : 'Geral';
    $image_url = isset($_POST['image_url']) ? esc_url_raw($_POST['image_url']) : '';

    if (empty($title) || empty($image_url)) {
        wp_send_json_error(__('Título e imagem são obrigatórios.', 'gta6-ultimate'));
    }

    global $wpdb;
    $table = $wpdb->prefix . 'gta6_images';

    $data = array(
        'title'       => $title,
        'description' => $description,
        'category'    => $category,
        'image_url'   => $image_url,
    );

    $format = array('%s', '%s', '%s', '%s');

    if ($id > 0) {
        $wpdb->update($table, $data, array('id' => $id), $format, array('%d'));
    } else {
        $data['date'] = current_time('mysql');
        $format[] = '%s';
        $wpdb->insert($table, $data, $format);
    }

    GTA6_Ultimate::get_instance()->clear_plugin_cache();

    wp_send_json_success(array('message' => __('Imagem salva com sucesso.', 'gta6-ultimate')));
}

/**
 * Salvar plano de fundo via AJAX
 */
function gta6_ajax_save_background() {
    check_ajax_referer('gta6-ultimate-nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(__('Você não tem permissão para realizar esta ação.', 'gta6-ultimate'));
    }

    $id        = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $title     = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
    $image_url = isset($_POST['image_url']) ? esc_url_raw($_POST['image_url']) : '';

    if (empty($title) || empty($image_url)) {
        wp_send_json_error(__('Título e imagem são obrigatórios.', 'gta6-ultimate'));
    }

    global $wpdb;
    $table = $wpdb->prefix . 'gta6_backgrounds';

    $data = array(
        'title'     => $title,
        'image_url' => $image_url,
    );

    $format = array('%s', '%s');

    if ($id > 0) {
        $wpdb->update($table, $data, array('id' => $id), $format, array('%d'));
    } else {
        $data['date'] = current_time('mysql');
        $format[] = '%s';
        $wpdb->insert($table, $data, $format);
    }

    GTA6_Ultimate::get_instance()->clear_plugin_cache();

    wp_send_json_success(array('message' => __('Plano de fundo salvo com sucesso.', 'gta6-ultimate')));
}

/**
 * Salvar personagem via AJAX
 */
function gta6_ajax_save_character() {
    check_ajax_referer('gta6-ultimate-nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(__('Você não tem permissão para realizar esta ação.', 'gta6-ultimate'));
    }

    $id           = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name         = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $description  = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
    $image_url    = isset($_POST['image_url']) ? esc_url_raw($_POST['image_url']) : '';
    $role         = isset($_POST['role']) ? sanitize_text_field($_POST['role']) : '';
    $display_order= isset($_POST['display_order']) ? intval($_POST['display_order']) : 0;

    if (empty($name) || empty($description) || empty($image_url)) {
        wp_send_json_error(__('Nome, descrição e imagem são obrigatórios.', 'gta6-ultimate'));
    }

    global $wpdb;
    $table = $wpdb->prefix . 'gta6_characters';

    $data = array(
        'name'          => $name,
        'description'   => $description,
        'image_url'     => $image_url,
        'role'          => $role,
        'display_order' => $display_order,
    );

    $format = array('%s', '%s', '%s', '%s', '%d');

    if ($id > 0) {
        $wpdb->update($table, $data, array('id' => $id), $format, array('%d'));
    } else {
        $data['date'] = current_time('mysql');
        $format[] = '%s';
        $wpdb->insert($table, $data, $format);
    }

    GTA6_Ultimate::get_instance()->clear_plugin_cache();

    wp_send_json_success(array('message' => __('Personagem salvo com sucesso.', 'gta6-ultimate')));
}

/**
 * Obter notícia via AJAX
 */
function gta6_ajax_get_news() {
    check_ajax_referer('gta6-ultimate-nonce', 'nonce');

    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if (empty($id)) {
        wp_send_json_error(__('ID inválido.', 'gta6-ultimate'));
    }

    global $wpdb;
    $table = $wpdb->prefix . 'gta6_news';

    $news = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
    if (!$news) {
        wp_send_json_error(__('Notícia não encontrada.', 'gta6-ultimate'));
    }

    wp_send_json_success(array(
        'title'     => $news->title,
        'content'   => wp_kses_post($news->content),
        'category'  => $news->category,
        'date'      => mysql2date(get_option('date_format'), $news->date),
        'image_url' => esc_url($news->image_url),
    ));
}

/**
 * Enviar e-mail de confirmação
 */
function gta6_send_confirmation_email($email) {
    // Esta função é opcional e pode ser implementada para enviar um e-mail de confirmação
    // Utilizando wp_mail() ou outro método de envio de e-mail
    
    $subject = __('Confirmação de inscrição na newsletter do GTA VI Ultimate', 'gta6-ultimate');
    $message = __('Obrigado por se inscrever na nossa newsletter! Você receberá as últimas notícias e atualizações sobre GTA VI.', 'gta6-ultimate');
    
    // Descomente a linha abaixo para ativar o envio de e-mail
    // wp_mail($email, $subject, $message);
}
