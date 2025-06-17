<?php
/**
 * Template para a página de vídeos do GTA VI Ultimate
 * Exibe a galeria de vídeos com filtros por categoria
 */

// Se este arquivo é chamado diretamente, aborta.
if (!defined('WPINC')) {
    die;
}

// Obter o título da página
$page_title = 'GTA VI - Vídeos';

// Incluir o cabeçalho
include_once GTA6_ULTIMATE_PLUGIN_DIR . 'templates/header.php';

// Obter categoria do filtro (se existir)
$category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';

// Consultar vídeos
global $wpdb;
$table_videos = $wpdb->prefix . 'gta6_videos';

// Preparar consulta SQL
$sql = "SELECT * FROM $table_videos";
if (!empty($category) && $category != 'all') {
    $sql .= $wpdb->prepare(" WHERE category = %s", $category);
}
$sql .= " ORDER BY date DESC";

// Executar consulta
$videos = $wpdb->get_results($sql);

// Obter categorias únicas para filtros
$categories = $wpdb->get_col("SELECT DISTINCT category FROM $table_videos ORDER BY category");
?>

<div class="gta6-content">
    <h1 class="gta6-section-title gta6-neon-text">Vídeos de GTA VI</h1>
    
    <!-- Filtros -->
    <div class="gta6-filters">
        <button class="gta6-filter-button <?php echo empty($category) || $category == 'all' ? 'active' : ''; ?>" data-category="all">Todos</button>
        <?php foreach ($categories as $cat) : ?>
            <button class="gta6-filter-button <?php echo $category == $cat ? 'active' : ''; ?>" data-category="<?php echo esc_attr($cat); ?>"><?php echo esc_html($cat); ?></button>
        <?php endforeach; ?>
    </div>
    
    <!-- Galeria de vídeos -->
    <div class="gta6-grid">
        <?php if (!empty($videos)) : ?>
            <?php foreach ($videos as $video) : ?>
                <div class="gta6-card gta6-filterable-item gta6-animate" data-category="<?php echo esc_attr($video->category); ?>">
                    <div class="gta6-card-body">
                        <div class="gta6-video-container">
                            <iframe width="560" height="315" src="<?php echo esc_url($video->video_url); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <h3 class="gta6-video-title"><?php echo esc_html($video->title); ?></h3>
                    </div>
                    <div class="gta6-card-footer">
                        <span class="gta6-video-category"><?php echo esc_html($video->category); ?></span>
                        <span class="gta6-video-date"><?php echo date_i18n(get_option('date_format'), strtotime($video->date)); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="gta6-no-results">
                <p>Nenhum vídeo encontrado.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // JavaScript para filtros
    jQuery(document).ready(function($) {
        // Filtrar vídeos por categoria
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
