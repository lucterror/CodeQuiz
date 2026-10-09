<?php
// Template único para as páginas de curso. Defina $slug antes de incluir.
$cursos = require __DIR__ . '/../data/cursos.php';
$c = $cursos[$slug] ?? null;
if (!$c) { http_response_code(404); exit('Curso não encontrado.'); }
$titulo = $c['titulo']; $pagina = 'curso'; $descricao = $c['sub'];
include __DIR__ . '/header.php';
?>
<main class="wrap" style="--c:<?= $c['cor'] ?>">
    <section class="ph">
        <span class="chip">&lt;<?= $c['nome'] ?>/&gt;</span>
        <h1><?= $c['titulo'] ?></h1>
        <p><?= $c['sub'] ?></p>
    </section>

    <div class="curso">
        <aside class="toc">
            <strong>Neste curso</strong>
            <?php foreach ($c['secoes'] as $n => $s): ?>
            <a href="#s<?= $n ?>"><?= $s[0] ?></a>
            <?php endforeach; ?>
        </aside>

        <article>
            <?php foreach ($c['secoes'] as $n => $s): ?>
            <section id="s<?= $n ?>">
                <h2><?= $s[0] ?></h2>
                <p><?= $s[1] ?></p>
                <?php if (!empty($s[2])): ?><pre class="code"><?= htmlspecialchars($s[2]) ?></pre><?php endif; ?>
            </section>
            <?php endforeach; ?>

            <div class="card fim">
                <h3>Quer se aprofundar?</h3>
                <p>Consulte a documentação oficial e depois teste o que aprendeu.</p>
                <div class="acts">
                    <a href="<?= $c['doc'] ?>" target="_blank" rel="noopener" class="btn btn-o">Documentação <?= $c['nome'] ?></a>
                    <a href="<?= $c['proximo'][0] ?>" class="btn btn-p">Próximo: <?= $c['proximo'][1] ?></a>
                </div>
            </div>
        </article>
    </div>
</main>
<?php include __DIR__ . '/footer.php'; ?>
