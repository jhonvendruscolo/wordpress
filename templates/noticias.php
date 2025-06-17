<?php
/**
 * Template para a página de notícias do GTA VI Ultimate
 * Exibe a listagem de notícias com filtros por categoria
 */

// Se este arquivo é chamado diretamente, aborta.
if (!defined('WPINC')) {
    die;
}

// Obter o título da página
$page_title = 'GTA VI - Notícias';

// Incluir o cabeçalho
include_once GTA6_ULTIMATE_PLUGIN_DIR . 'templates/header.php';

// Obter categoria do filtro (se existir)
$category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';

// Consultar notícias
global $wpdb;
$table_news = $wpdb->prefix . 'gta6_news';

// Preparar consulta SQL
$sql = "SELECT * FROM $table_news";
if (!empty($category) && $category != 'all') {
    $sql .= $wpdb->prepare(" WHERE category = %s", $category);
}
$sql .= " ORDER BY date DESC";

// Executar consulta
$news = $wpdb->get_results($sql);

// Obter categorias únicas para filtros
$categories = $wpdb->get_col("SELECT DISTINCT category FROM $table_news ORDER BY category");
?>

<div class="gta6-content">
    <h1 class="gta6-section-title gta6-neon-text">Notícias de GTA VI</h1>
    
    <!-- Filtros -->
    <div class="gta6-filters">
        <button class="gta6-filter-button <?php echo empty($category) || $category == 'all' ? 'active' : ''; ?>" data-category="all">Todas</button>
        <?php foreach ($categories as $cat) : ?>
            <button class="gta6-filter-button <?php echo $category == $cat ? 'active' : ''; ?>" data-category="<?php echo esc_attr($cat); ?>"><?php echo esc_html($cat); ?></button>
        <?php endforeach; ?>
    </div>
    
    <!-- Listagem de notícias -->
    <div class="gta6-grid">
        <?php if (!empty($news)) : ?>
            <?php foreach ($news as $item) : ?>
                <div class="gta6-card gta6-news-item gta6-filterable-item gta6-animate" data-category="<?php echo esc_attr($item->category); ?>">
                    <img src="<?php echo esc_url($item->image_url); ?>" alt="<?php echo esc_attr($item->title); ?>" class="gta6-news-image">
                    <div class="gta6-card-body">
                        <h3 class="gta6-news-title"><?php echo esc_html($item->title); ?></h3>
                        <div class="gta6-news-date"><?php echo date_i18n(get_option('date_format'), strtotime($item->date)); ?></div>
                        <div class="gta6-news-excerpt"><?php echo wp_trim_words(wp_kses_post($item->content), 30, '...'); ?></div>
                    </div>
                    <div class="gta6-card-footer">
                        <span class="gta6-news-category"><?php echo esc_html($item->category); ?></span>
                        <a href="#" class="gta6-link gta6-news-read-more" data-id="<?php echo esc_attr($item->id); ?>">Leia mais</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="gta6-no-results">
                <p>Nenhuma notícia encontrada.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Modal para exibir notícia completa -->
    <div class="gta6-modal" id="gta6-news-modal">
        <div class="gta6-modal-content">
            <span class="gta6-modal-close">&times;</span>
            <div class="gta6-modal-body">
                <!-- Conteúdo carregado via JavaScript -->
            </div>
        </div>
    </div>
</div>

<script>
    // JavaScript para modal de notícias
    jQuery(document).ready(function($) {
        // Abrir modal ao clicar em "Leia mais"
        $('.gta6-news-read-more').on('click', function(e) {
            e.preventDefault();
            
            const newsId = $(this).data('id');
            
            // Carregar conteúdo da notícia via AJAX
            $.ajax({
                url: gta6_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'gta6_get_news',
                    id: newsId,
                    nonce: gta6_vars.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Preencher modal com conteúdo
                        $('#gta6-news-modal .gta6-modal-body').html(`
                            <h2 class="gta6-modal-title">${response.data.title}</h2>
                            <div class="gta6-modal-meta">
                                <span class="gta6-modal-date">${response.data.date}</span>
                                <span class="gta6-modal-category">${response.data.category}</span>
                            </div>
                            <img src="${response.data.image_url}" alt="${response.data.title}" class="gta6-modal-image">
                            <div class="gta6-modal-content-text">${response.data.content}</div>
                        `);
                        
                        // Exibir modal
                        $('#gta6-news-modal').fadeIn();
                    }
                }
            });
        });
        
        // Fechar modal
        $('.gta6-modal-close').on('click', function() {
            $('#gta6-news-modal').fadeOut();
        });
        
        // Fechar modal ao clicar fora do conteúdo
        $(window).on('click', function(e) {
            if ($(e.target).is('.gta6-modal')) {
                $('.gta6-modal').fadeOut();
            }
        });
        
        // Filtrar notícias por categoria
        $('.gta6-filter-button').on('click', function() {
            const category = $(this).data('category');
            
            // Atualizar URL com parâmetro de categoria
            const url = new URL(window.location);
            if (category === 'all') {
                url.searchParams.delete('category');
            } else {
                url.searchParams.set('category', category);
            }
            window.history.pushState({}, '', url);
            
            // Atualizar estado ativo dos botões
            $('.gta6-filter-button').removeClass('active');
            $(this).addClass('active');
            
            // Filtrar itens
            if (category === 'all') {
                $('.gta6-filterable-item').show();
            } else {
                $('.gta6-filterable-item').hide();
                $(`.gta6-filterable-item[data-category="${category}"]`).show();
            }
        });
    });
</script>

<?php
// Incluir o rodapé
include_once GTA6_ULTIMATE_PLUGIN_DIR . 'templates/footer.php';
?>
