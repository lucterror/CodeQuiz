<?php
$titulo = 'Sobre nós'; $pagina = 'sobre';
$recursos = [['📚', 'Conteúdos', 'Explicações sobre conceitos e linguagens de programação.'],
             ['💡', 'Exemplos', 'Exemplos práticos para ajudar na compreensão.'],
             ['🎯', 'Quizzes', 'Teste seus conhecimentos e veja o quanto aprendeu.'],
             ['⌨️', 'Editor de código', 'Escreva código de verdade, receba correção na hora e ganhe XP.'],
             ['🏆', 'XP e ranking', 'Suba de nível, ganhe medalhas e compare sua evolução.'],
             ['🌐', 'Quatro linguagens', 'HTML, CSS, JavaScript e PHP: a base para criar sites completos.']];
include 'includes/header.php';
?>
<main class="wrap">
    <section class="ph"><h1>Sobre nós</h1><p>Aprenda, pratique e evolua no mundo da programação.</p></section>

    <section class="quem">
        <div>
            <h2>Quem somos?</h2>
            <p>Somos uma plataforma criada para ajudar pessoas que desejam aprender programação de forma simples, organizada e divertida.</p>
            <p>Nosso site foi pensado principalmente para quem está começando e quer aprender os primeiros conceitos sem complicações.</p>
        </div>
        <div class="card destaque"><span class="ic">💻</span><h3>Aprendizado simples</h3><p>Conteúdos feitos para facilitar o entendimento e ajudar você a aprender no seu próprio ritmo.</p></div>
    </section>

    <section class="missao card">
        <h2>Nosso objetivo</h2>
        <p>Tornar o aprendizado de programação mais acessível, reunindo explicações, exemplos, desafios no editor de código e quizzes de HTML, CSS, JavaScript e PHP em um único lugar.</p>
    </section>

    <section class="sec2">
        <h2>O que você encontra aqui</h2>
        <div class="grid3">
            <?php foreach ($recursos as [$ic, $t, $d]): ?>
            <div class="card"><span class="ic"><?= $ic ?></span><h3><?= $t ?></h3><p><?= $d ?></p></div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="cta"><h2>Comece sua jornada!</h2><p>Comece pelo básico, pratique e evolua cada vez mais.</p><a href="curso-html.php" class="btn">Começar a aprender</a></section>
</main>
<?php include 'includes/footer.php'; ?>
