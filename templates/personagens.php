<?php
/**
 * Template para a página de personagens do GTA VI Ultimate
 */

// Se este arquivo é chamado diretamente, aborta.
if (!defined('WPINC')) {
    die;
}

// Incluir cabeçalho
include_once GTA6_ULTIMATE_PLUGIN_DIR . 'templates/header.php';

// Obter personagens
global $wpdb;
$table_name = $wpdb->prefix . 'gta6_characters';
$characters = $wpdb->get_results("SELECT * FROM $table_name ORDER BY display_order ASC, id ASC");
?>

<div class="gta6-content">
    <h1 class="gta6-section-title gta6-neon-text"><?php echo gta6_t('characters', 'Personagens'); ?></h1>

    <?php if (empty($characters)) : ?>
        <p class="gta6-no-content"><?php echo gta6_t('no_characters_found', 'Nenhum personagem encontrado.'); ?></p>
    <?php else : ?>
        <div class="gta6-character-grid">
            <?php foreach ($characters as $character) : ?>
                <div class="gta6-character-card">
                    <img src="<?php echo esc_url($character->image_url); ?>" alt="<?php echo esc_attr($character->name); ?>" class="gta6-character-image">
                    <div class="gta6-character-info">
                        <h3 class="gta6-character-name"><?php echo esc_html($character->name); ?></h3>
                        <?php if (!empty($character->role)) : ?>
                            <div class="gta6-character-role"><?php echo esc_html($character->role); ?></div>
                        <?php endif; ?>
                        <p class="gta6-character-description"><?php echo esc_html($character->description); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
// Incluir rodapé
include_once GTA6_ULTIMATE_PLUGIN_DIR . 'templates/footer.php';
?>
