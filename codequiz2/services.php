<?php
$titulo = 'Cursos'; $pagina = 'services';
$servicos = [
 ['🌐', 'Curso de HTML', 'Estruture páginas web do zero: tags, links, imagens, listas e formulários.', 'curso-html.php', 'Ver curso'],
 ['🎨', 'Curso de CSS', 'Cores, caixas, Flexbox, Grid e responsividade para deixar tudo bonito.', 'curso-css.php', 'Ver curso'],
 ['⚡', 'Curso de JavaScript', 'Variáveis, funções, arrays e DOM para dar vida às páginas.', 'curso-js.php', 'Ver curso'],
 ['🐘', 'Curso de PHP', 'Programação no servidor para criar sites dinâmicos e seguros.', 'curso-php.php', 'Ver curso'],
 ['🎯', 'Quiz de linguagens', 'Teste HTML, CSS, JavaScript e PHP com XP, sequências e cronômetro.', 'quiz.php', 'Jogar agora'],
 ['⌨️', 'Editor de código', 'Resolva desafios escrevendo código de verdade e ganhe XP no ranking.', 'editor.php', 'Abrir editor'],
 ['🏆', 'Ranking e perfil', 'Suba de nível, conquiste medalhas e dispute o topo.', 'ranking.php', 'Ver ranking'],
 ['⭐', 'Planos e assinaturas', 'Desbloqueie todos os cursos e desafios exclusivos.', 'assinaturas.php', 'Ver planos'],
];
include 'includes/header.php';
?>
<main class="wrap">
    <section class="ph"><h1>Tudo para aprender programando</h1><p>Cursos, quizzes e desafios de HTML, CSS, JavaScript e PHP em um só lugar.</p></section>
    <div class="grid">
        <?php foreach ($servicos as [$ic, $t, $d, $url, $cta]): ?>
        <a class="card item" href="<?= $url ?>"><span class="ic"><?= $ic ?></span><h3><?= $t ?></h3><p><?= $d ?></p><span class="lnk"><?= $cta ?> →</span></a>
        <?php endforeach; ?>
    </div>
</main>
<?php include 'includes/footer.php'; ?>
