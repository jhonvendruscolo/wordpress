<?php
/**
 * Template para a página de imagens do GTA VI Ultimate
 * Exibe a galeria de imagens com filtros por categoria
 */

// Se este arquivo é chamado diretamente, aborta.
if (!defined('WPINC')) {
    die;
}

// Obter o título da página
$page_title = 'GTA VI - Imagens';

// Incluir o cabeçalho
include_once GTA6_ULTIMATE_PLUGIN_DIR . 'templates/header.php';

// Obter categoria do filtro (se existir)
$category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';

// Consultar imagens
global $wpdb;
$table_images = $wpdb->prefix . 'gta6_images';

// Preparar consulta SQL
$sql = "SELECT * FROM $table_images";
if (!empty($category) && $category != 'all') {
    $sql .= $wpdb->prepare(" WHERE category = %s", $category);
}
$sql .= " ORDER BY date DESC";

// Executar consulta
$images = $wpdb->get_results($sql);

// Obter categorias únicas para filtros
$categories = $wpdb->get_col("SELECT DISTINCT category FROM $table_images ORDER BY category");
?>

<div class="gta6-content">
    <h1 class="gta6-section-title gta6-neon-text">Galeria de Imagens</h1>
    
    <!-- Filtros -->
    <div class="gta6-filters">
        <button class="gta6-filter-button <?php echo empty($category) || $category == 'all' ? 'active' : ''; ?>" data-category="all">Todas</button>
        <?php foreach ($categories as $cat) : ?>
            <button class="gta6-filter-button <?php echo $category == $cat ? 'active' : ''; ?>" data-category="<?php echo esc_attr($cat); ?>"><?php echo esc_html($cat); ?></button>
        <?php endforeach; ?>
    </div>
    
    <!-- Galeria de imagens -->
    <div class="gta6-grid">
        <?php if (!empty($images)) : ?>
            <?php foreach ($images as $image) : ?>
                <div class="gta6-gallery-item gta6-filterable-item gta6-animate" data-category="<?php echo esc_attr($image->category); ?>" data-full-image="<?php echo esc_url($image->image_url); ?>">
                    <img src="<?php echo esc_url($image->image_url); ?>" alt="<?php echo esc_attr($image->title); ?>" class="gta6-gallery-image">
                    <div class="gta6-gallery-overlay">
                        <h3 class="gta6-gallery-title"><?php echo esc_html($image->title); ?></h3>
                        <span class="gta6-gallery-category"><?php echo esc_html($image->category); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="gta6-no-results">
                <p>Nenhuma imagem encontrada.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // JavaScript para filtros e lightbox
    jQuery(document).ready(function($) {
        // Filtrar imagens por categoria
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
        
        // Lightbox para imagens
        $('.gta6-gallery-item').on('click', function() {
            const imageUrl = $(this).data('full-image');
            const imageTitle = $(this).find('.gta6-gallery-title').text();
            
            // Criar lightbox
            const lightbox = `
                <div class="gta6-lightbox">
                    <div class="gta6-lightbox-content">
                        <span class="gta6-lightbox-close">&times;</span>
                        <img src="${imageUrl}" alt="${imageTitle}">
                        <div class="gta6-lightbox-caption">${imageTitle}</div>
                    </div>
                </div>
            `;
            
            // Adicionar lightbox ao corpo do documento
            $('body').append(lightbox);
            
            // Fechar lightbox ao clicar no botão de fechar ou fora da imagem
            $('.gta6-lightbox, .gta6-lightbox-close').on('click', function() {
                $('.gta6-lightbox').remove();
            });
            
            // Evitar que o clique na imagem feche o lightbox
            $('.gta6-lightbox-content img').on('click', function(e) {
                e.stopPropagation();
            });
        });
    });
</script>

<?php
// Incluir o rodapé
include_once GTA6_ULTIMATE_PLUGIN_DIR . 'templates/footer.php';
?>
