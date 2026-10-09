<?php
$titulo = 'Planos'; $pagina = 'assinaturas';
$planos = require 'data/planos.php';
include 'includes/header.php';
?>
<main class="wrap">
    <section class="ph"><h1>Escolha seu plano e comece a programar</h1><p>Aprenda, pratique e evolua com o CodeQuiz.</p></section>
    <div class="planos">
        <?php foreach ($planos as $p): ?>
        <article class="plano <?= !empty($p['destaque']) ? 'destaque' : '' ?>">
            <?php if ($p['selo']): ?><span class="selo"><?= $p['selo'] ?></span><?php endif; ?>
            <h2><?= $p['nome'] ?></h2>
            <div class="preco"><?= $p['preco'] ?></div><div class="periodo"><?= $p['periodo'] ?></div>
            <p><?= $p['desc'] ?></p>
            <ul><?php foreach ($p['itens'] as $i): ?><li><?= $i ?></li><?php endforeach; ?></ul>
            <a href="<?= $p['url'] ?>" class="btn <?= !empty($p['destaque']) ? 'btn-p' : 'btn-o' ?>"><?= $p['cta'] ?></a>
        </article>
        <?php endforeach; ?>
    </div>
</main>
<?php include 'includes/footer.php'; ?>
