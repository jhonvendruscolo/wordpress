<?php
/**
 * Plugin Name: GTA VI Ultimate
 * Plugin URI: https://www.example.com/gta6-ultimate
 * Description: Um plugin completo e profissional para WordPress que funciona como um mini site exclusivo de GTA VI, acessível via a rota fixa: dominio.com/gta
 * Version: 1.3.3
 * Author: Manus AI
 * Author URI: https://www.example.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: gta6-ultimate
 * Domain Path: /languages
 */

// Se este arquivo é chamado diretamente, aborta.
if (!defined('WPINC')) {
    die;
}

// Definir constantes
define('GTA6_ULTIMATE_VERSION', '1.3.3');
define('GTA6_ULTIMATE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GTA6_ULTIMATE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('GTA6_ULTIMATE_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('GTA6_ULTIMATE_ASSET_VERSION', GTA6_ULTIMATE_VERSION);

/**
 * Classe principal do plugin
 */
class GTA6_Ultimate {

    /**
     * Instância única da classe
     */
    private static $instance = null;

    /**
     * Obtém a instância única da classe
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Construtor
     */
    private function __construct() {
        // Iniciar sessão para suporte a idiomas (usando cookies em vez de sessão para evitar problemas de cache)
        add_action('init', array($this, 'init_language_support'));

        // Registrar hooks de ativação e desativação
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        // Inicializar o plugin
        add_action('plugins_loaded', array($this, 'init'));

        // Registrar scripts e estilos
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

        // Adicionar rewrite rules
        add_action('init', array($this, 'add_rewrite_rules'));

        // Registrar template para as rotas personalizadas
        add_filter('template_include', array($this, 'template_include'));

        // Adicionar menu no admin
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Registrar AJAX handlers
        add_action('wp_ajax_gta6_delete_item', array($this, 'ajax_delete_item'));
        
        // Adicionar headers para controle de cache
        add_action('send_headers', array($this, 'add_cache_headers'));
        
    }
    
    /**
     * Inicializar suporte a idiomas
     */
    public function init_language_support() {
        // Usar cookies em vez de sessão para armazenar preferências de idioma
        if (!isset($_COOKIE['gta6_language']) && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browser_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            $supported_langs = array('pt' => 'pt_BR', 'en' => 'en_US', 'es' => 'es_ES');
            $lang = isset($supported_langs[$browser_lang]) ? $supported_langs[$browser_lang] : 'pt_BR';
            setcookie('gta6_language', $lang, time() + 30 * DAY_IN_SECONDS, '/');
        }
    }

    /**
     * Inicializar o plugin
     */
    public function init() {
        // Carregar arquivos de tradução
        load_plugin_textdomain('gta6-ultimate', false, dirname(plugin_basename(__FILE__)) . '/languages');

        // Incluir arquivos necessários
        $this->includes();
    }

    /**
     * Incluir arquivos necessários
     */
    private function includes() {
        // Incluir arquivos de administração
        require_once GTA6_ULTIMATE_PLUGIN_DIR . 'admin/painel.php';
        require_once GTA6_ULTIMATE_PLUGIN_DIR . 'admin/gerenciar-noticias.php';
        require_once GTA6_ULTIMATE_PLUGIN_DIR . 'admin/gerenciar-videos.php';
        require_once GTA6_ULTIMATE_PLUGIN_DIR . 'admin/gerenciar-imagens.php';
        require_once GTA6_ULTIMATE_PLUGIN_DIR . 'admin/gerenciar-background.php';
        require_once GTA6_ULTIMATE_PLUGIN_DIR . 'admin/gerenciar-personagens.php';
        
        // Incluir arquivos de funcionalidades
        require_once GTA6_ULTIMATE_PLUGIN_DIR . 'includes/language.php';
        require_once GTA6_ULTIMATE_PLUGIN_DIR . 'includes/ajax.php';
    }

    /**
     * Ativação do plugin
     */
    public function activate() {
        // Criar tabelas no banco de dados
        $this->create_tables();

        // Adicionar conteúdo inicial
        $this->add_initial_content();

        // Atualizar regras de reescrita
        flush_rewrite_rules();
        
        // Limpar qualquer cache existente
        $this->clear_plugin_cache();
    }

    /**
     * Desativação do plugin
     */
    public function deactivate() {
        // Limpar regras de reescrita
        flush_rewrite_rules();
        
        // Limpar cache
        $this->clear_plugin_cache();
    }

    /**
     * Criar tabelas no banco de dados
     */
    private function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Tabela de notícias
        $table_news = $wpdb->prefix . 'gta6_news';
        $sql_news = "CREATE TABLE $table_news (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title text NOT NULL,
            content longtext NOT NULL,
            image_url varchar(255) DEFAULT '' NOT NULL,
            category varchar(50) DEFAULT 'Geral' NOT NULL,
            date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Tabela de vídeos
        $table_videos = $wpdb->prefix . 'gta6_videos';
        $sql_videos = "CREATE TABLE $table_videos (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title text NOT NULL,
            description text DEFAULT '' NOT NULL,
            video_url varchar(255) NOT NULL,
            video_type varchar(20) DEFAULT 'youtube' NOT NULL,
            thumbnail_url varchar(255) DEFAULT '' NOT NULL,
            category varchar(50) DEFAULT 'Geral' NOT NULL,
            date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Tabela de imagens
        $table_images = $wpdb->prefix . 'gta6_images';
        $sql_images = "CREATE TABLE $table_images (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title text NOT NULL,
            description text DEFAULT '' NOT NULL,
            image_url varchar(255) NOT NULL,
            category varchar(50) DEFAULT 'Geral' NOT NULL,
            date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Tabela de inscritos na newsletter
        $table_newsletter = $wpdb->prefix . 'gta6_newsletter';
        $sql_newsletter = "CREATE TABLE $table_newsletter (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            email varchar(100) NOT NULL,
            date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY email (email)
        ) $charset_collate;";
        
        // Tabela de planos de fundo
        $table_backgrounds = $wpdb->prefix . 'gta6_backgrounds';
        $sql_backgrounds = "CREATE TABLE $table_backgrounds (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title text NOT NULL,
            image_url varchar(255) NOT NULL,
            is_active tinyint(1) DEFAULT 0 NOT NULL,
            date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        // Tabela de personagens
        $table_characters = $wpdb->prefix . 'gta6_characters';
        $sql_characters = "CREATE TABLE $table_characters (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            description text NOT NULL,
            image_url varchar(255) NOT NULL,
            role varchar(100) DEFAULT '' NOT NULL,
            display_order int(11) DEFAULT 0 NOT NULL,
            date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_news);
        dbDelta($sql_videos);
        dbDelta($sql_images);
        dbDelta($sql_newsletter);
        dbDelta($sql_backgrounds);
        dbDelta($sql_characters);
    }

    /**
     * Adicionar conteúdo inicial
     */
    private function add_initial_content() {
        global $wpdb;

        // Adicionar notícias iniciais
        $table_news = $wpdb->prefix . 'gta6_news';
        $news = array(
            array(
                'title' => 'Rockstar Games confirma data de lançamento de GTA VI',
                'content' => 'A Rockstar Games finalmente confirmou a data de lançamento oficial de Grand Theft Auto VI. O jogo mais aguardado da década chegará às lojas em 26 de maio de 2026, exatamente 12 anos após o lançamento de GTA V. Os fãs poderão retornar a Vice City em uma versão moderna e expandida da icônica cidade inspirada em Miami. O jogo contará com dois protagonistas jogáveis, Jason e Lucia, em uma história épica de crime e sobrevivência no estado fictício de Leonida.',
                'image_url' => GTA6_ULTIMATE_PLUGIN_URL . 'assets/news1.jpg',
                'category' => 'Geral',
                'date' => current_time('mysql')
            ),
            array(
                'title' => 'Lucia será a primeira protagonista feminina da série GTA',
                'content' => 'Em uma mudança histórica para a franquia, Lucia será a primeira protagonista feminina jogável na série principal de Grand Theft Auto. De acordo com informações divulgadas pela Rockstar, Lucia é uma ex-presidiária de origem latina que se envolve no mundo do crime ao lado de seu parceiro Jason. Os dois personagens terão habilidades únicas e complementares, permitindo aos jogadores alternar entre eles durante a campanha principal. A Rockstar promete uma dinâmica de relacionamento complexa entre os protagonistas, com decisões que afetarão o desenvolvimento da história.',
                'image_url' => GTA6_ULTIMATE_PLUGIN_URL . 'assets/news2.jpg',
                'category' => 'Lucia',
                'date' => current_time('mysql')
            ),
            array(
                'title' => 'Vice City terá o maior mapa da história da franquia GTA',
                'content' => 'A Rockstar Games revelou que o mapa de Vice City em GTA VI será o maior já criado para a franquia, superando os já impressionantes mundos de GTA V e Red Dead Redemption 2. Além da cidade principal inspirada em Miami, o jogo incluirá várias áreas rurais, pântanos inspirados nos Everglades da Flórida, e diversas ilhas menores que poderão ser exploradas. O mapa terá um ciclo dinâmico de clima, incluindo tempestades tropicais e furacões que afetarão a jogabilidade e o ambiente.',
                'image_url' => GTA6_ULTIMATE_PLUGIN_URL . 'assets/news3.jpg',
                'category' => 'Mundo',
                'date' => current_time('mysql')
            )
        );

        foreach ($news as $item) {
            $wpdb->insert($table_news, $item);
        }

        // Adicionar vídeos iniciais
        $table_videos = $wpdb->prefix . 'gta6_videos';
        $videos = array(
            array(
                'title' => 'Trailer Oficial de GTA VI',
                'description' => 'Trailer oficial de lançamento de Grand Theft Auto VI, revelando os protagonistas Jason e Lucia.',
                'video_url' => 'https://www.youtube.com/embed/QdBZY2fkU-0',
                'video_type' => 'youtube',
                'thumbnail_url' => GTA6_ULTIMATE_PLUGIN_URL . 'assets/video_thumb1.jpg',
                'category' => 'Geral',
                'date' => current_time('mysql')
            )
        );

        foreach ($videos as $item) {
            $wpdb->insert($table_videos, $item);
        }

        // Adicionar imagens iniciais
        $table_images = $wpdb->prefix . 'gta6_images';
        $images = array(
            array(
                'title' => 'Jason em Vice City',
                'description' => 'Jason explorando as ruas de Vice City.',
                'image_url' => GTA6_ULTIMATE_PLUGIN_URL . 'assets/jason.jpg',
                'category' => 'Jason',
                'date' => current_time('mysql')
            ),
            array(
                'title' => 'Lucia em ação',
                'description' => 'Lucia em uma cena de ação em Vice City.',
                'image_url' => GTA6_ULTIMATE_PLUGIN_URL . 'assets/lucia.jpg',
                'category' => 'Lucia',
                'date' => current_time('mysql')
            )
        );

        foreach ($images as $item) {
            $wpdb->insert($table_images, $item);
        }
        
        // Adicionar plano de fundo inicial
        $table_backgrounds = $wpdb->prefix . 'gta6_backgrounds';
        $backgrounds = array(
            array(
                'title' => 'Cal Hampton em Vice City',
                'image_url' => GTA6_ULTIMATE_PLUGIN_URL . 'assets/background-new.jpg',
                'is_active' => 1,
                'date' => current_time('mysql')
            )
        );

        foreach ($backgrounds as $item) {
            $wpdb->insert($table_backgrounds, $item);
        }
        
        // Adicionar personagens iniciais
        $table_characters = $wpdb->prefix . 'gta6_characters';
        $characters = array(
            array(
                'name' => 'Lucia Caminos',
                'description' => 'Uma jovem mulher de Vice City, conhecida por seu temperamento forte e inteligência aguçada.',
                'image_url' => GTA6_ULTIMATE_PLUGIN_URL . 'assets/lucia.jpg',
                'role' => 'Protagonista',
                'display_order' => 0,
                'date' => current_time('mysql')
            ),
            array(
                'name' => 'Jason Duval',
                'description' => 'Um criminoso de carreira dos subúrbios de Vice City, lutando para conseguir um grande golpe.',
                'image_url' => GTA6_ULTIMATE_PLUGIN_URL . 'assets/jason.jpg',
                'role' => 'Protagonista',
                'display_order' => 1,
                'date' => current_time('mysql')
            ),
            array(
                'name' => 'Cal Hampton',
                'description' => 'Um traficante de drogas de baixo escalão que opera em Vice City, conhecido por seu estilo de vida extravagante.',
                'image_url' => GTA6_ULTIMATE_PLUGIN_URL . 'assets/background-new.jpg',
                'role' => 'Coadjuvante',
                'display_order' => 2,
                'date' => current_time('mysql')
            )
        );

        foreach ($characters as $item) {
            $wpdb->insert($table_characters, $item);
        }
    }

    /**
     * Registrar scripts e estilos para o frontend
     */
    public function enqueue_scripts() {
        // Verificar se estamos em uma página do GTA VI Ultimate
        if (!$this->is_gta6_page()) {
            return;
        }

        // Registrar e enfileirar CSS com versão dinâmica para evitar cache
        wp_enqueue_style('gta6-ultimate-style', GTA6_ULTIMATE_PLUGIN_URL . 'assets/css/style.css', array(), GTA6_ULTIMATE_ASSET_VERSION);
        
        // Registrar e enfileirar JavaScript com versão dinâmica para evitar cache
        wp_enqueue_script('gta6-ultimate-script', GTA6_ULTIMATE_PLUGIN_URL . 'assets/js/script.js', array('jquery'), GTA6_ULTIMATE_ASSET_VERSION, true);
        
        // Passar variáveis para o JavaScript
        wp_localize_script('gta6-ultimate-script', 'gta6_vars', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'plugin_url' => GTA6_ULTIMATE_PLUGIN_URL,
            'release_date' => '2026-05-26 00:00:00', // Data de lançamento do GTA VI
            'nonce' => wp_create_nonce('gta6-ultimate-nonce'),
            'cache_buster' => time() // Adicionar timestamp para evitar cache
        ));
    }
    
    /**
     * Registrar scripts e estilos para o admin
     */
    public function admin_enqueue_scripts($hook) {
        // Verificar se estamos em uma página do plugin
        if (strpos($hook, 'gta6-ultimate') === false) {
            return;
        }
        
        // Enfileirar mídia do WordPress
        wp_enqueue_media();
        
        // Registrar e enfileirar CSS admin com versão dinâmica para evitar cache
        wp_enqueue_style('gta6-ultimate-admin-style', GTA6_ULTIMATE_PLUGIN_URL . 'assets/css/admin.css', array(), GTA6_ULTIMATE_ASSET_VERSION);
        
        // Registrar e enfileirar JavaScript admin com versão dinâmica para evitar cache
        wp_enqueue_script('gta6-ultimate-admin-script', GTA6_ULTIMATE_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), GTA6_ULTIMATE_ASSET_VERSION, true);
        
        // Passar variáveis para o JavaScript
        wp_localize_script('gta6-ultimate-admin-script', 'gta6_admin_vars', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'plugin_url' => GTA6_ULTIMATE_PLUGIN_URL,
            'nonce' => wp_create_nonce('gta6-ultimate-admin-nonce'),
            'confirm_delete' => __('Tem certeza de que deseja excluir este item?', 'gta6-ultimate'),
            'deleting' => __('Excluindo...', 'gta6-ultimate'),
            'delete_error' => __('Erro ao excluir o item. Tente novamente.', 'gta6-ultimate'),
            'cache_buster' => time() // Adicionar timestamp para evitar cache
        ));
    }

    /**
     * Verificar se estamos em uma página do GTA VI Ultimate
     */
    private function is_gta6_page() {
        global $wp_query;
        return isset($wp_query->query_vars['gta6_page']);
    }

    /**
     * Adicionar regras de reescrita
     */
    public function add_rewrite_rules() {
        add_rewrite_rule(
            '^gta/?$',
            'index.php?gta6_page=home',
            'top'
        );
        
        add_rewrite_rule(
            '^gta/([^/]+)/?$',
            'index.php?gta6_page=$matches[1]',
            'top'
        );
        
        add_rewrite_tag('%gta6_page%', '([^&]+)');
    }

    /**
     * Incluir template para as rotas personalizadas
     */
    public function template_include($template) {
        global $wp_query;
        
        if (isset($wp_query->query_vars['gta6_page'])) {
            $page = $wp_query->query_vars['gta6_page'];
            
            // Definir título da página
            add_filter('document_title_parts', function($title) use ($page) {
                $title['title'] = 'GTA VI - ' . ucfirst($page);
                return $title;
            });
            
            // Carregar template correspondente
            $template_file = GTA6_ULTIMATE_PLUGIN_DIR . 'templates/' . $page . '.php';
            
            if (file_exists($template_file)) {
                return $template_file;
            } else {
                // Template padrão se o específico não existir
                return GTA6_ULTIMATE_PLUGIN_DIR . 'templates/home.php';
            }
        }
        
        return $template;
    }

    /**
     * Adicionar menu no admin
     */
    public function add_admin_menu() {
        add_menu_page(
            __('GTA VI Ultimate', 'gta6-ultimate'),
            __('GTA VI Ultimate', 'gta6-ultimate'),
            'manage_options',
            'gta6-ultimate',
            'gta6_admin_page',
            'dashicons-games',
            30
        );
        
        add_submenu_page(
            'gta6-ultimate',
            __('Painel', 'gta6-ultimate'),
            __('Painel', 'gta6-ultimate'),
            'manage_options',
            'gta6-ultimate',
            'gta6_admin_page'
        );
        
        add_submenu_page(
            'gta6-ultimate',
            __('Gerenciar Notícias', 'gta6-ultimate'),
            __('Gerenciar Notícias', 'gta6-ultimate'),
            'manage_options',
            'gta6-ultimate-news',
            'gta6_admin_news_page'
        );
        
        add_submenu_page(
            'gta6-ultimate',
            __('Gerenciar Vídeos', 'gta6-ultimate'),
            __('Gerenciar Vídeos', 'gta6-ultimate'),
            'manage_options',
            'gta6-ultimate-videos',
            'gta6_admin_videos_page'
        );
        
        add_submenu_page(
            'gta6-ultimate',
            __('Gerenciar Imagens', 'gta6-ultimate'),
            __('Gerenciar Imagens', 'gta6-ultimate'),
            'manage_options',
            'gta6-ultimate-images',
            'gta6_admin_images_page'
        );
        
        add_submenu_page(
            'gta6-ultimate',
            __('Gerenciar Planos de Fundo', 'gta6-ultimate'),
            __('Gerenciar Planos de Fundo', 'gta6-ultimate'),
            'manage_options',
            'gta6-ultimate-backgrounds',
            'gta6_admin_backgrounds_page'
        );
        
        add_submenu_page(
            'gta6-ultimate',
            __('Gerenciar Personagens', 'gta6-ultimate'),
            __('Gerenciar Personagens', 'gta6-ultimate'),
            'manage_options',
            'gta6-ultimate-characters',
            'gta6_admin_characters_page'
        );
    }
    
    /**
     * Handler AJAX para exclusão de itens
     */
    public function ajax_delete_item() {
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
        
        // Limpar cache após exclusão
        $this->clear_plugin_cache();
        
        // Definir cabeçalhos para evitar cache
        nocache_headers();
        
        wp_send_json_success(array(
            'message' => __('Item excluído com sucesso.', 'gta6-ultimate'),
            'timestamp' => time()
        ));
    }
    
    /**
     * Adicionar headers para controle de cache
     */
    public function add_cache_headers() {
        if ($this->is_gta6_page()) {
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        }
    }
    
    /**
     * Limpar cache do plugin
     */
    public function clear_plugin_cache() {
        // Limpar transients específicos do plugin
        global $wpdb;
        $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '%_transient_gta6_%'");
        $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '%_transient_timeout_gta6_%'");
        
        // Limpar cache de objetos se estiver usando
        if (wp_using_ext_object_cache()) {
            wp_cache_flush();
        }
        
        // Definir cabeçalhos para evitar cache
        nocache_headers();
        
        return true;
    }
}

// Inicializar o plugin
function gta6_ultimate_init() {
    return GTA6_Ultimate::get_instance();
}
gta6_ultimate_init();
