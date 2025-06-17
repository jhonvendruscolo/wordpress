<?php
/**
 * Template para o cabeçalho das páginas do GTA VI Ultimate
 */

// Se este arquivo é chamado diretamente, aborta.
if (!defined('WPINC')) {
    die;
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php wp_head(); ?>
</head>

<body <?php body_class('gta6-page'); ?>>
<?php wp_body_open(); ?>

<div class="gta6-container">
    <!-- Seletor de idiomas -->
    <?php echo gta6_language_selector(); ?>
    
    <header class="gta6-header">
        <a href="<?php echo esc_url(home_url('gta')); ?>">
            <img src="<?php echo esc_url(GTA6_ULTIMATE_PLUGIN_URL . 'assets/logo.png'); ?>" alt="GTA VI Logo" class="gta6-logo">
        </a>
        
        <nav class="gta6-nav">
            <a href="<?php echo esc_url(home_url('gta')); ?>" <?php echo (isset($wp_query->query_vars['gta6_page']) && $wp_query->query_vars['gta6_page'] == 'home') ? 'class="active"' : ''; ?>><?php echo gta6_t('home'); ?></a>
            <a href="<?php echo esc_url(home_url('gta/noticias')); ?>" <?php echo (isset($wp_query->query_vars['gta6_page']) && $wp_query->query_vars['gta6_page'] == 'noticias') ? 'class="active"' : ''; ?>><?php echo gta6_t('news'); ?></a>
            <a href="<?php echo esc_url(home_url('gta/videos')); ?>" <?php echo (isset($wp_query->query_vars['gta6_page']) && $wp_query->query_vars['gta6_page'] == 'videos') ? 'class="active"' : ''; ?>><?php echo gta6_t('videos'); ?></a>
            <a href="<?php echo esc_url(home_url('gta/imagens')); ?>" <?php echo (isset($wp_query->query_vars['gta6_page']) && $wp_query->query_vars['gta6_page'] == 'imagens') ? 'class="active"' : ''; ?>><?php echo gta6_t('images'); ?></a>
            <a href="<?php echo esc_url(home_url('gta/sobre')); ?>" <?php echo (isset($wp_query->query_vars['gta6_page']) && $wp_query->query_vars['gta6_page'] == 'sobre') ? 'class="active"' : ''; ?>><?php echo gta6_t('about'); ?></a>
            <a href="<?php echo esc_url(home_url('gta/timeline')); ?>" <?php echo (isset($wp_query->query_vars['gta6_page']) && $wp_query->query_vars['gta6_page'] == 'timeline') ? 'class="active"' : ''; ?>><?php echo gta6_t('timeline'); ?></a>
        </nav>
    </header>
