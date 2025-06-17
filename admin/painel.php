<?php
/**
 * Painel de administração principal do GTA VI Ultimate
 */

// Se este arquivo é chamado diretamente, aborta.
if (!defined('WPINC')) {
    die;
}

/**
 * Função para renderizar a página principal do painel de administração
 */
function gta6_admin_page() {
    ?>
    <div class="wrap gta6-admin">
        <h1><span class="dashicons dashicons-games"></span> GTA VI Ultimate</h1>
        
        <div class="gta6-admin-welcome">
            <div class="gta6-admin-logo">
                <img src="<?php echo GTA6_ULTIMATE_PLUGIN_URL; ?>assets/logo.png" alt="GTA VI Logo">
            </div>
            <div class="gta6-admin-intro">
                <h2>Bem-vindo ao Painel de Administração do GTA VI Ultimate</h2>
                <p>Este plugin cria um mini site exclusivo de GTA VI, acessível via a rota fixa: <a href="<?php echo home_url('gta'); ?>" target="_blank"><?php echo home_url('gta'); ?></a></p>
                <p>Use as opções abaixo para gerenciar o conteúdo do seu site GTA VI.</p>
            </div>
        </div>
        
        <div class="gta6-admin-cards">
            <div class="gta6-admin-card">
                <div class="gta6-admin-card-header">
                    <span class="dashicons dashicons-admin-post"></span>
                    <h3>Gerenciar Notícias</h3>
                </div>
                <div class="gta6-admin-card-body">
                    <p>Adicione, edite ou remova notícias sobre GTA VI.</p>
                    <p>As notícias aparecem na página inicial e na seção de notícias.</p>
                </div>
                <div class="gta6-admin-card-footer">
                    <a href="<?php echo admin_url('admin.php?page=gta6-ultimate-news'); ?>" class="button button-primary">Gerenciar Notícias</a>
                </div>
            </div>
            
            <div class="gta6-admin-card">
                <div class="gta6-admin-card-header">
                    <span class="dashicons dashicons-format-video"></span>
                    <h3>Gerenciar Vídeos</h3>
                </div>
                <div class="gta6-admin-card-body">
                    <p>Adicione, edite ou remova vídeos do YouTube ou MP4.</p>
                    <p>Os vídeos aparecem na página inicial e na seção de vídeos.</p>
                </div>
                <div class="gta6-admin-card-footer">
                    <a href="<?php echo admin_url('admin.php?page=gta6-ultimate-videos'); ?>" class="button button-primary">Gerenciar Vídeos</a>
                </div>
            </div>
            
            <div class="gta6-admin-card">
                <div class="gta6-admin-card-header">
                    <span class="dashicons dashicons-format-gallery"></span>
                    <h3>Gerenciar Imagens</h3>
                </div>
                <div class="gta6-admin-card-body">
                    <p>Adicione, edite ou remova imagens para a galeria.</p>
                    <p>As imagens aparecem na seção de imagens.</p>
                </div>
                <div class="gta6-admin-card-footer">
                    <a href="<?php echo admin_url('admin.php?page=gta6-ultimate-images'); ?>" class="button button-primary">Gerenciar Imagens</a>
                </div>
            </div>
        </div>
        
        <div class="gta6-admin-newsletter">
            <h3><span class="dashicons dashicons-email"></span> Inscritos na Newsletter</h3>
            <?php
            global $wpdb;
            $table_newsletter = $wpdb->prefix . 'gta6_newsletter';
            $subscribers = $wpdb->get_results("SELECT * FROM $table_newsletter ORDER BY date DESC");
            
            if (!empty($subscribers)) {
                ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Data de Inscrição</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subscribers as $subscriber) : ?>
                            <tr>
                                <td><?php echo esc_html($subscriber->email); ?></td>
                                <td><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($subscriber->date)); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p>
                    <a href="#" class="button" id="gta6-export-subscribers">Exportar Lista (CSV)</a>
                </p>
                <?php
            } else {
                echo '<p>Nenhum inscrito na newsletter ainda.</p>';
            }
            ?>
        </div>
        
        <div class="gta6-admin-footer">
            <p>GTA VI Ultimate v<?php echo GTA6_ULTIMATE_VERSION; ?> | Desenvolvido por Manus AI</p>
        </div>
    </div>
    
    <script>
        jQuery(document).ready(function($) {
            // Exportar inscritos para CSV
            $('#gta6-export-subscribers').on('click', function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'gta6_export_subscribers',
                        nonce: '<?php echo wp_create_nonce('gta6-ultimate-nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Criar link de download
                            var a = document.createElement('a');
                            var blob = new Blob([response.data], {type: 'text/csv'});
                            a.href = window.URL.createObjectURL(blob);
                            a.download = 'gta6_subscribers.csv';
                            a.click();
                        } else {
                            alert('Erro ao exportar inscritos: ' + response.data);
                        }
                    },
                    error: function() {
                        alert('Erro ao processar a solicitação.');
                    }
                });
            });
        });
    </script>
    
    <style>
        .gta6-admin h1 {
            color: #ff00cc;
            text-shadow: 0 0 5px rgba(255, 0, 204, 0.5);
        }
        
        .gta6-admin-welcome {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin: 20px 0;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .gta6-admin-logo {
            flex: 0 0 200px;
        }
        
        .gta6-admin-logo img {
            max-width: 100%;
            height: auto;
        }
        
        .gta6-admin-intro {
            flex: 1;
            min-width: 300px;
        }
        
        .gta6-admin-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .gta6-admin-card {
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .gta6-admin-card-header {
            background: #1a1a1a;
            color: #fff;
            padding: 15px;
            display: flex;
            align-items: center;
        }
        
        .gta6-admin-card-header .dashicons {
            margin-right: 10px;
            color: #ff00cc;
        }
        
        .gta6-admin-card-header h3 {
            margin: 0;
            color: #fff;
        }
        
        .gta6-admin-card-body {
            padding: 15px;
        }
        
        .gta6-admin-card-footer {
            padding: 15px;
            background: #f5f5f5;
            text-align: right;
        }
        
        .gta6-admin-newsletter {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
        }
        
        .gta6-admin-newsletter h3 {
            display: flex;
            align-items: center;
            margin-top: 0;
        }
        
        .gta6-admin-newsletter h3 .dashicons {
            margin-right: 10px;
            color: #ff00cc;
        }
        
        .gta6-admin-footer {
            margin-top: 20px;
            color: #666;
            font-style: italic;
        }
    </style>
    <?php
}
