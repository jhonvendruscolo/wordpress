<?php
/**
 * Funções de internacionalização para o GTA VI Ultimate
 * Suporte para PT-BR, EN e ES
 */

// Se este arquivo é chamado diretamente, aborta.
if (!defined('WPINC')) {
    die;
}

/**
 * Inicializar suporte a idiomas
 */
function gta6_init_languages() {
    // Registrar scripts e estilos para o seletor de idiomas
    add_action('wp_enqueue_scripts', 'gta6_enqueue_language_scripts');
    
    // Adicionar AJAX handlers para troca de idioma
    add_action('wp_ajax_gta6_change_language', 'gta6_ajax_change_language');
    add_action('wp_ajax_nopriv_gta6_change_language', 'gta6_ajax_change_language');
    
    // Filtrar strings para tradução
    add_filter('gta6_translate_string', 'gta6_translate_string', 10, 2);
}

/**
 * Enfileirar scripts e estilos para o seletor de idiomas
 */
function gta6_enqueue_language_scripts() {
    // Verificar se estamos em uma página do GTA VI Ultimate
    if (!isset($GLOBALS['wp_query']->query_vars['gta6_page'])) {
        return;
    }
    
    // Registrar e enfileirar JavaScript
    wp_enqueue_script('gta6-language-script', GTA6_ULTIMATE_PLUGIN_URL . 'assets/js/language.js', array('jquery'), GTA6_ULTIMATE_VERSION, true);
    
    // Passar variáveis para o JavaScript
    wp_localize_script('gta6-language-script', 'gta6_lang_vars', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'current_language' => gta6_get_current_language(),
        'nonce' => wp_create_nonce('gta6-language-nonce')
    ));
}

/**
 * AJAX handler para troca de idioma
 */
function gta6_ajax_change_language() {
    // Verificar nonce
    check_ajax_referer('gta6-language-nonce', 'nonce');
    
    // Obter idioma
    $language = isset($_POST['language']) ? sanitize_text_field($_POST['language']) : '';
    
    // Validar idioma
    if (!in_array($language, array('pt_BR', 'en_US', 'es_ES'))) {
        wp_send_json_error('Idioma inválido');
    }
    
    // Salvar idioma em cookie (30 dias)
    setcookie('gta6_language', $language, time() + (30 * DAY_IN_SECONDS), '/');
    
    wp_send_json_success(array(
        'message' => 'Idioma alterado com sucesso'
    ));
}

/**
 * Obter idioma atual
 */
function gta6_get_current_language() {
    // Verificar se há um idioma no cookie
    if (isset($_COOKIE['gta6_language'])) {
        return $_COOKIE['gta6_language'];
    }
    
    // Obter idioma do navegador
    $browser_language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'pt-BR', 0, 2);
    
    // Mapear idioma do navegador para os idiomas suportados
    $language_map = array(
        'pt' => 'pt_BR',
        'en' => 'en_US',
        'es' => 'es_ES'
    );
    
    return $language_map[$browser_language] ?? 'pt_BR';
}

/**
 * Traduzir string
 */
function gta6_translate_string($string, $context = '') {
    $language = gta6_get_current_language();
    
    // Carregar traduções
    $translations = gta6_get_translations($language);
    
    // Verificar se há uma tradução para esta string
    if (isset($translations[$string])) {
        return $translations[$string];
    }
    
    // Verificar se há uma tradução com contexto
    $key_with_context = $context ? $context . '|' . $string : '';
    if (!empty($key_with_context) && isset($translations[$key_with_context])) {
        return $translations[$key_with_context];
    }
    
    // Retornar string original se não houver tradução
    return $string;
}

/**
 * Obter traduções para um idioma
 */
function gta6_get_translations($language) {
    static $translations = array();
    
    // Verificar se já carregamos as traduções para este idioma
    if (isset($translations[$language])) {
        return $translations[$language];
    }
    
    // Carregar traduções do arquivo JSON
    $file_path = GTA6_ULTIMATE_PLUGIN_DIR . 'languages/' . $language . '.json';
    
    if (file_exists($file_path)) {
        $json_content = file_get_contents($file_path);
        $translations[$language] = json_decode($json_content, true);
        
        // Verificar se houve erro no parsing do JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('Erro ao carregar traduções para ' . $language . ': ' . json_last_error_msg());
            $translations[$language] = array();
        }
    } else {
        $translations[$language] = array();
    }
    
    return $translations[$language];
}

/**
 * Função de tradução simplificada
 */
function gta6_t($string, $context = '') {
    return apply_filters('gta6_translate_string', $string, $context);
}

/**
 * Renderizar seletor de idiomas
 */
function gta6_language_selector() {
    $current_language = gta6_get_current_language();
    
    // Definir idiomas disponíveis
    $languages = array(
        'pt_BR' => array(
            'name' => 'Português',
            'flag' => 'br'
        ),
        'en_US' => array(
            'name' => 'English',
            'flag' => 'us'
        ),
        'es_ES' => array(
            'name' => 'Español',
            'flag' => 'es'
        )
    );
    
    // Iniciar HTML
    $html = '<div class="gta6-language-selector">';
    
    // Adicionar botões de idioma
    foreach ($languages as $code => $language) {
        $active_class = ($code === $current_language) ? 'active' : '';
        $html .= sprintf(
            '<button type="button" class="gta6-language-button %s" data-language="%s">%s</button>',
            $active_class,
            $code,
            $language['name']
        );
    }
    
    $html .= '</div>';
    
    return $html;
}

// Inicializar suporte a idiomas
gta6_init_languages();
