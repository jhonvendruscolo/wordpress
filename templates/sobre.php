<?php
/**
 * Template para a página "Sobre" do GTA VI Ultimate
 * Exibe informações sobre Vice City e o jogo
 */

// Se este arquivo é chamado diretamente, aborta.
if (!defined('WPINC')) {
    die;
}

// Obter o título da página
$page_title = 'GTA VI - Sobre';

// Incluir o cabeçalho
include_once GTA6_ULTIMATE_PLUGIN_DIR . 'templates/header.php';
?>

<div class="gta6-content">
    <h1 class="gta6-section-title gta6-neon-text">Sobre Vice City</h1>
    
    <div class="gta6-card gta6-animate">
        <div class="gta6-card-body">
            <div class="gta6-about">
                <div class="gta6-about-image">
                    <img src="<?php echo GTA6_ULTIMATE_PLUGIN_URL; ?>assets/jason_lucia.jpg" alt="Vice City" class="gta6-neon-border">
                </div>
                <div class="gta6-about-content">
                    <h2 class="gta6-about-title">Vice City</h2>
                    <p>Vice City é uma metrópole vibrante inspirada em Miami, Flórida, que retorna como cenário principal em Grand Theft Auto VI. Localizada no estado fictício de Leonida, esta versão moderna e expandida da icônica cidade apresenta uma mistura única de cultura latina, vida noturna eletrizante e contrastes sociais marcantes.</p>
                    
                    <p>Originalmente introduzida em GTA: Vice City (2002), a cidade agora retorna completamente reimaginada com tecnologia de última geração, oferecendo o maior e mais detalhado mundo aberto já criado pela Rockstar Games. Os jogadores poderão explorar praias ensolaradas, pântanos misteriosos, arranha-céus imponentes e subúrbios decadentes, tudo em um ambiente vivo e dinâmico.</p>
                    
                    <p>A cidade é dividida em diversos distritos distintos, cada um com sua própria identidade, habitantes e oportunidades. Das luxuosas mansões de Starfish Island aos bairros perigosos de Little Haiti, Vice City oferece uma experiência imersiva e contrastante que reflete as complexidades da sociedade americana contemporânea.</p>
                </div>
            </div>
        </div>
    </div>
    
    <h2 class="gta6-section-title gta6-neon-text">Protagonistas</h2>
    
    <div class="gta6-grid">
        <div class="gta6-card gta6-animate">
            <div class="gta6-card-header">Lucia</div>
            <div class="gta6-card-body">
                <div class="gta6-character">
                    <img src="<?php echo GTA6_ULTIMATE_PLUGIN_URL; ?>assets/lucia.jpg" alt="Lucia" class="gta6-character-image">
                    <div class="gta6-character-info">
                        <p>Lucia é a primeira protagonista feminina jogável na série principal de Grand Theft Auto. Ex-presidiária de origem latina, ela se vê forçada a retornar ao mundo do crime após sua libertação. Inteligente, determinada e implacável quando necessário, Lucia traz uma nova perspectiva à narrativa da série.</p>
                        <p>Com habilidades únicas em combate corpo a corpo e infiltração, Lucia representa uma mudança histórica para a franquia, oferecendo aos jogadores uma experiência de jogo distinta e uma história de origem cativante.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="gta6-card gta6-animate">
            <div class="gta6-card-header">Jason</div>
            <div class="gta6-card-body">
                <div class="gta6-character">
                    <img src="<?php echo GTA6_ULTIMATE_PLUGIN_URL; ?>assets/jason.jpg" alt="Jason" class="gta6-character-image">
                    <div class="gta6-character-info">
                        <p>Jason é o parceiro de Lucia tanto no crime quanto na vida pessoal. Com um passado misterioso e conexões no submundo criminal de Vice City, ele traz experiência e recursos para a dupla. Especialista em armas e direção, Jason complementa as habilidades de Lucia.</p>
                        <p>A dinâmica entre Jason e Lucia promete ser um dos elementos centrais da narrativa de GTA VI, explorando temas de lealdade, traição e sobrevivência em um mundo implacável.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <h2 class="gta6-section-title gta6-neon-text">Sobre o Jogo</h2>
    
    <div class="gta6-card gta6-animate">
        <div class="gta6-card-body">
            <p>Grand Theft Auto VI é o próximo capítulo da aclamada série de jogos de mundo aberto da Rockstar Games. Previsto para lançamento em 17 de setembro de 2025, o jogo promete redefinir os padrões da indústria com gráficos de última geração, uma narrativa madura e complexa, e um sistema de jogabilidade revolucionário.</p>
            
            <p>Pela primeira vez na série, os jogadores poderão alternar entre dois protagonistas durante a campanha principal, cada um com habilidades únicas e perspectivas diferentes sobre o mundo do crime. A história explorará a relação entre Lucia e Jason enquanto eles navegam pelo submundo criminal de Vice City e arredores.</p>
            
            <p>O jogo apresentará um sistema climático dinâmico, incluindo furacões devastadores que podem alterar permanentemente partes do mapa. A economia in-game também foi completamente redesenhada, com um mercado imobiliário funcional, oportunidades de investimento e múltiplas formas de construir um império criminal.</p>
            
            <p>GTA VI também trará de volta elementos queridos pelos fãs, como a capacidade de comprar e gerenciar propriedades, personalizar veículos em detalhes sem precedentes, e participar de uma ampla variedade de atividades secundárias, desde golfe e tênis até mergulho e caça submarina.</p>
        </div>
    </div>
</div>

<?php
// Incluir o rodapé
include_once GTA6_ULTIMATE_PLUGIN_DIR . 'templates/footer.php';
?>
