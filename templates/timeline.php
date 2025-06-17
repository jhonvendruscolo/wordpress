<?php
/**
 * Template para a página de timeline do GTA VI Ultimate
 * Exibe a linha do tempo da franquia GTA
 */

// Se este arquivo é chamado diretamente, aborta.
if (!defined('WPINC')) {
    die;
}

// Obter o título da página
$page_title = 'GTA VI - Timeline';

// Incluir o cabeçalho
include_once GTA6_ULTIMATE_PLUGIN_DIR . 'templates/header.php';
?>

<div class="gta6-content">
    <h1 class="gta6-section-title gta6-neon-text">Linha do Tempo da Franquia GTA</h1>
    
    <div class="gta6-timeline">
        <div class="gta6-timeline-item">
            <div class="gta6-timeline-content">
                <div class="gta6-timeline-year">1997</div>
                <h3 class="gta6-timeline-title">Grand Theft Auto</h3>
                <p>O jogo que iniciou tudo. Desenvolvido pela DMA Design (que mais tarde se tornaria a Rockstar North), o primeiro GTA apresentou uma visão de cima para baixo de três cidades fictícias: Liberty City, San Andreas e Vice City. Apesar de seus gráficos simples, o jogo estabeleceu a fórmula básica da série: mundo aberto, missões criminosas e liberdade de escolha.</p>
            </div>
        </div>
        
        <div class="gta6-timeline-item">
            <div class="gta6-timeline-content">
                <div class="gta6-timeline-year">1999</div>
                <h3 class="gta6-timeline-title">Grand Theft Auto 2</h3>
                <p>Mantendo a visão de cima para baixo, GTA 2 introduziu melhorias gráficas e jogabilidade mais refinada. Ambientado em um futuro próximo distópico, o jogo apresentou o conceito de facções rivais com as quais o jogador poderia ganhar ou perder respeito, influenciando o desenvolvimento da história.</p>
            </div>
        </div>
        
        <div class="gta6-timeline-item">
            <div class="gta6-timeline-content">
                <div class="gta6-timeline-year">2001</div>
                <h3 class="gta6-timeline-title">Grand Theft Auto III</h3>
                <p>A revolução. GTA III transformou a série e os jogos de mundo aberto para sempre ao mudar para um ambiente 3D totalmente navegável. Ambientado em Liberty City (inspirada em Nova York), o jogo apresentou uma narrativa mais complexa, dublagem completa e uma liberdade sem precedentes para explorar e causar caos. Foi um sucesso comercial e crítico massivo.</p>
            </div>
        </div>
        
        <div class="gta6-timeline-item">
            <div class="gta6-timeline-content">
                <div class="gta6-timeline-year">2002</div>
                <h3 class="gta6-timeline-title">Grand Theft Auto: Vice City</h3>
                <p>Ambientado nos anos 80 em Vice City (inspirada em Miami), este título aprimorou a fórmula de GTA III com um protagonista falante (Tommy Vercetti), uma trilha sonora icônica e uma atmosfera vibrante inspirada em filmes como "Scarface" e séries como "Miami Vice". Introduziu a capacidade de comprar propriedades e negócios.</p>
            </div>
        </div>
        
        <div class="gta6-timeline-item">
            <div class="gta6-timeline-content">
                <div class="gta6-timeline-year">2004</div>
                <h3 class="gta6-timeline-title">Grand Theft Auto: San Andreas</h3>
                <p>O mais ambicioso da era PS2, San Andreas expandiu o conceito para um estado inteiro com três cidades distintas. Seguindo CJ em sua jornada pelos anos 90, o jogo introduziu personalização de personagens, habilidades melhoráveis e uma variedade enorme de atividades secundárias. É frequentemente citado como um dos melhores jogos já feitos.</p>
            </div>
        </div>
        
        <div class="gta6-timeline-item">
            <div class="gta6-timeline-content">
                <div class="gta6-timeline-year">2008</div>
                <h3 class="gta6-timeline-title">Grand Theft Auto IV</h3>
                <p>Com o poder dos consoles de nova geração, GTA IV apresentou uma versão mais realista e sombria de Liberty City. Seguindo o imigrante Niko Bellic, o jogo ofereceu uma narrativa mais madura e um sistema de física avançado. Introduziu o modo multijogador online e expandiu-se com dois grandes DLCs: The Lost and Damned e The Ballad of Gay Tony.</p>
            </div>
        </div>
        
        <div class="gta6-timeline-item">
            <div class="gta6-timeline-content">
                <div class="gta6-timeline-year">2013</div>
                <h3 class="gta6-timeline-title">Grand Theft Auto V</h3>
                <p>Revolucionando novamente a série, GTA V introduziu três protagonistas jogáveis entre os quais o jogador pode alternar. Ambientado em Los Santos e arredores (inspirados em Los Angeles), o jogo ofereceu o maior e mais detalhado mundo aberto até então. O lançamento do GTA Online transformou a franquia em um fenômeno de serviço ao vivo que continua popular até hoje.</p>
            </div>
        </div>
        
        <div class="gta6-timeline-item">
            <div class="gta6-timeline-content">
                <div class="gta6-timeline-year">2025</div>
                <h3 class="gta6-timeline-title">Grand Theft Auto VI</h3>
                <p>Retornando a Vice City com tecnologia de última geração, GTA VI promete ser o título mais ambicioso da Rockstar Games até hoje. Apresentando dois protagonistas jogáveis, Jason e Lucia (a primeira protagonista feminina da série principal), o jogo expandirá os limites do que é possível em um mundo aberto, com um sistema climático dinâmico, inteligência artificial avançada e uma narrativa complexa sobre crime, lealdade e sobrevivência.</p>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir o rodapé
include_once GTA6_ULTIMATE_PLUGIN_DIR . 'templates/footer.php';
?>
