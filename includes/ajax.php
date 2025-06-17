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
    
    // Adicionar handler para exclusão de itens
    add_action('wp_ajax_gta6_delete_item', 'gta6_ajax_delete_item');
    
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
 * Handler AJAX para exclusão de itens
 */
function gta6_ajax_delete_item() {
    // Verificar nonce
    check_ajax_referer('gta6-ultimate-admin-nonce', 'nonce');
    
    // Verificar permissões
    if (!current_user_can('manage_options')) {
        wp_send_json_error(__('Você não tem permissão para realizar esta ação.', 'gta6-ultimate'));
    }
    
    // Obter dados
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
    
    // Validar dados
    if (empty($id) || empty($type)) {
        wp_send_json_error(__('Dados inválidos.', 'gta6-ultimate'));
    }
    
    global $wpdb;
    
    // Determinar tabela com base no tipo
    $table_map = array(
        'news' => $wpdb->prefix . 'gta6_news',
        'videos' => $wpdb->prefix . 'gta6_videos',
        'images' => $wpdb->prefix . 'gta6_images',
        'backgrounds' => $wpdb->prefix . 'gta6_backgrounds',
        'characters' => $wpdb->prefix . 'gta6_characters',
        'newsletter' => $wpdb->prefix . 'gta6_newsletter'
    );
    
    if (!isset($table_map[$type])) {
        wp_send_json_error(__('Tipo de item inválido.', 'gta6-ultimate'));
    }
    
    $table_name = $table_map[$type];
    
    // Verificar se o item existe
    $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
    if (!$item) {
        wp_send_json_error(__('Item não encontrado.', 'gta6-ultimate'));
    }
    
    // Excluir item
    $result = $wpdb->delete(
        $table_name,
        array('id' => $id),
        array('%d')
    );
    
    if ($result === false) {
        wp_send_json_error(__('Erro ao excluir o item.', 'gta6-ultimate'));
    }
    
    wp_send_json_success(array(
        'message' => __('Item excluído com sucesso.', 'gta6-ultimate')
    ));
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
