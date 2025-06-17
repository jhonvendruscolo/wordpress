<?php
/**
 * Template para o rodapé das páginas do GTA VI Ultimate
 */

// Se este arquivo é chamado diretamente, aborta.
if (!defined('WPINC')) {
    die;
}
?>

    <footer class="gta6-footer">
        <div class="gta6-footer-text">
            &copy; <?php echo date('Y'); ?> GTA VI Ultimate - Um plugin para WordPress
            <br>
            Grand Theft Auto e todos os elementos relacionados são marcas registradas da Rockstar Games.
            <br>
            Este é um projeto não oficial criado por fãs.
        </div>
    </footer>
</div><!-- .gta6-container -->

<?php wp_footer(); ?>

</body>
</html>
