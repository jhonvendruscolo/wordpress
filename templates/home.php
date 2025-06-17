<?php
/**
 * Template para a página inicial do GTA VI Ultimate
 * Exibe o contador regressivo e links para as seções
 */

// Se este arquivo é chamado diretamente, aborta.
if (!defined('WPINC')) {
    die;
}

// Obter o título da página
$page_title = 'GTA VI - Home';

// Incluir o cabeçalho
include_once GTA6_ULTIMATE_PLUGIN_DIR . 'templates/header.php';
?>

<div class="gta6-content">
    <!-- Contador regressivo -->
    <div class="gta6-countdown">
        <div class="gta6-countdown-item">
            <div class="gta6-countdown-value" id="gta6-days">00</div>
            <div class="gta6-countdown-label"><?php echo gta6_t('days', 'DIAS'); ?></div>
        </div>
        <div class="gta6-countdown-item">
            <div class="gta6-countdown-value" id="gta6-hours">00</div>
            <div class="gta6-countdown-label"><?php echo gta6_t('hours', 'HORAS'); ?></div>
        </div>
        <div class="gta6-countdown-item">
            <div class="gta6-countdown-value" id="gta6-minutes">00</div>
            <div class="gta6-countdown-label"><?php echo gta6_t('minutes', 'MINUTOS'); ?></div>
        </div>
        <div class="gta6-countdown-item">
            <div class="gta6-countdown-value" id="gta6-seconds">00</div>
            <div class="gta6-countdown-label"><?php echo gta6_t('seconds', 'SEGUNDOS'); ?></div>
        </div>
    </div>

    <!-- Trailer em destaque -->
    <h2 class="gta6-section-title gta6-neon-text"><?php echo gta6_t('trailer', 'TRAILER'); ?></h2>
    <div class="gta6-card gta6-animate">
        <div class="gta6-card-body">
            <div class="gta6-video-container">
                <iframe width="560" height="315" src="https://www.youtube.com/embed/QdBZY2fkU-0" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        </div>
    </div>

    <!-- Personagens -->
    <div class="gta6-character-section">
        <h2 class="gta6-character-title gta6-neon-text"><?php echo gta6_t('characters', 'PERSONAGENS'); ?></h2>
        <div class="gta6-character-grid">
            <?php
            global $wpdb;
            $table_characters = $wpdb->prefix . 'gta6_characters';
            $characters = $wpdb->get_results("SELECT * FROM $table_characters ORDER BY display_order ASC LIMIT 2");

            foreach ($characters as $character) :
            ?>
            <div class="gta6-character-card">
                <img src="<?php echo esc_url($character->image_url); ?>" alt="<?php echo esc_attr($character->name); ?>" class="gta6-character-image">
                <div class="gta6-character-info">
                    <h3 class="gta6-character-name"><?php echo esc_html($character->name); ?></h3>
                    <p class="gta6-character-description"><?php echo esc_html($character->description); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="gta6-buttons-center" style="text-align: center; margin-top: 20px;">
            <a href="<?php echo esc_url(home_url('gta/personagens')); ?>" class="gta6-button"><?php echo gta6_t('view_all_characters', 'Ver Mais Personagens'); ?></a>
        </div>
    </div>

    <!-- Últimas notícias -->
    <h2 class="gta6-section-title gta6-neon-text"><?php echo gta6_t('latest_news', 'Últimas Notícias'); ?></h2>
    <div class="gta6-grid">
        <?php
        global $wpdb;
        $table_news = $wpdb->prefix . 'gta6_news';
        $news = $wpdb->get_results("SELECT * FROM $table_news ORDER BY date DESC LIMIT 3");

        foreach ($news as $item) :
        ?>
        <div class="gta6-card gta6-news-item gta6-animate">
            <img src="<?php echo esc_url($item->image_url); ?>" alt="<?php echo esc_attr($item->title); ?>" class="gta6-news-image">
            <div class="gta6-card-body">
                <h3 class="gta6-news-title"><?php echo esc_html($item->title); ?></h3>
                <div class="gta6-news-date"><?php echo date_i18n(get_option('date_format'), strtotime($item->date)); ?></div>
                <div class="gta6-news-excerpt"><?php echo wp_trim_words(wp_kses_post($item->content), 30, '...'); ?></div>
            </div>
            <div class="gta6-card-footer">
                <span class="gta6-news-category"><?php echo esc_html($item->category); ?></span>
                <a href="<?php echo esc_url(home_url('gta/noticias')); ?>" class="gta6-link"><?php echo gta6_t('read_more', 'Leia mais'); ?></a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="gta6-buttons-center" style="text-align: center; margin-top: 20px;">
        <a href="<?php echo esc_url(home_url('gta/noticias')); ?>" class="gta6-button"><?php echo gta6_t('view_all_news', 'Ver Todas as Notícias'); ?></a>
    </div>

    <!-- Newsletter -->
    <div class="gta6-newsletter gta6-animate">
        <h3 class="gta6-newsletter-title"><?php echo gta6_t('newsletter_title', 'Inscreva-se para receber as últimas notícias sobre GTA VI'); ?></h3>
        <p><?php echo gta6_t('newsletter_description', 'Seja o primeiro a saber sobre novidades, atualizações e conteúdo exclusivo.'); ?></p>
        <form class="gta6-newsletter-form" id="gta6-newsletter-form">
            <input type="email" name="email" class="gta6-newsletter-input" placeholder="<?php echo gta6_t('email_placeholder', 'Seu endereço de e-mail'); ?>" required>
            <button type="submit" class="gta6-newsletter-button"><?php echo gta6_t('subscribe', 'Inscrever-se'); ?></button>
            <div class="gta6-newsletter-message" style="display: none;"></div>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Formulário de newsletter
    $('#gta6-newsletter-form').on('submit', function(e) {
        e.preventDefault();
        
        var email = $(this).find('input[name="email"]').val();
        var messageContainer = $(this).find('.gta6-newsletter-message');
        
        // Validar e-mail
        if (!email || !email.includes('@')) {
            messageContainer.html('<?php echo esc_js(gta6_t('invalid_email', 'Por favor, insira um e-mail válido.')); ?>').addClass('error').removeClass('success').show();
            return;
        }
        
        // Desabilitar botão durante o envio
        var submitButton = $(this).find('button[type="submit"]');
        submitButton.prop('disabled', true).text('<?php echo esc_js(gta6_t('sending', 'Enviando...')); ?>');
        
        // Enviar solicitação AJAX
        $.ajax({
            url: gta6_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'gta6_subscribe_newsletter',
                email: email,
                nonce: gta6_vars.nonce
            },
            success: function(response) {
                if (response.success) {
                    messageContainer.html(response.data.message).addClass('success').removeClass('error').show();
                    $('#gta6-newsletter-form')[0].reset();
                } else {
                    messageContainer.html(response.data.message).addClass('error').removeClass('success').show();
                }
            },
            error: function() {
                messageContainer.html('<?php echo esc_js(gta6_t('error_occurred', 'Ocorreu um erro. Por favor, tente novamente.')); ?>').addClass('error').removeClass('success').show();
            },
            complete: function() {
                submitButton.prop('disabled', false).text('<?php echo esc_js(gta6_t('subscribe', 'Inscrever-se')); ?>');
            }
        });
    });
});
</script>

<?php
// Incluir o rodapé
include_once GTA6_ULTIMATE_PLUGIN_DIR . 'templates/footer.php';
?>
