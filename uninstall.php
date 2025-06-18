<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;
$tables = array(
    $wpdb->prefix . 'gta6_news',
    $wpdb->prefix . 'gta6_videos',
    $wpdb->prefix . 'gta6_images',
    $wpdb->prefix . 'gta6_newsletter',
    $wpdb->prefix . 'gta6_backgrounds',
    $wpdb->prefix . 'gta6_characters',
);

foreach ( $tables as $table ) {
    $wpdb->query( "DROP TABLE IF EXISTS $table" );
}
?>
