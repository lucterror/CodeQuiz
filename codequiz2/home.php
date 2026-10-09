<?php
require_once 'includes/xp.php';
$titulo = 'Aprenda programação com quizzes e desafios'; $pagina = 'home';
$langs = require 'data/quiz-data.php';
$desafios = require 'data/desafios.php';
$nPerguntas = array_sum(array_map(fn($l) => count($l[3]), $langs));
$top = ranking(null, null, 3);
$medalhas = ['🥇', '🥈', '🥉'];
include 'includes/header.php';
?>
<main class="wrap">
    <section class="hero">
        <div>
            <h1>Aprender programação pode ser um <em>jogo</em></h1>
            <p>Responda quizzes e resolva desafios no editor de código de HTML, CSS, JavaScript e PHP. Ganhe XP, suba de nível e dispute o ranking.</p>
            <div class="acts">
                <?php if ($usuario): ?>
                <a href="quiz.php" class="btn btn-p">Jogar o quiz</a>
                <a href="editor.php" class="btn btn-o">Abrir o editor</a>
                <?php else: ?>
                <a href="cadastro.php" class="btn btn-p">Cadastre-se aqui</a>
                <a href="login.php" class="btn btn-o">Já tenho uma conta</a>
                <?php endif; ?>
            </div>
            <div class="stats">
                <div><b data-count="<?= count($langs) ?>"><?= count($langs) ?></b><span>linguagens</span></div>
                <div><b data-count="<?= $nPerguntas ?>" data-suffix="+"><?= $nPerguntas ?>+</b><span>perguntas</span></div>
                <div><b data-count="<?= count($desafios) ?>"><?= count($desafios) ?></b><span>desafios no editor</span></div>
            </div>
        </div>
        <div class="editor">
            <div class="ed-bar"><i></i><i></i><i></i><span>desafio.js</span></div>
<pre><span class="k">const</span> nums = [<span class="s">1</span>, <span class="s">2</span>, <span class="s">3</span>];
<span class="k">const</span> r = nums.<span class="f">map</span>(n =&gt; n * <span class="s">2</span>);
<span class="f">console</span>.<span class="f">log</span>(r);  <span class="c">// ?</span></pre>
            <div class="ed-q">
                <p>Qual é a saída?</p>
                <div><button class="demo-opt" data-ok="0">[1, 2, 3]</button><button class="demo-opt" data-ok="1">[2, 4, 6]</button><button class="demo-opt" data-ok="0">6</button><button class="demo-opt" data-ok="0">undefined</button></div>
                <p id="demo-fb">Clique em uma resposta.</p>
            </div>
        </div>
    </section>

    <section class="techs" aria-label="Linguagens abordadas">
        <?php foreach ($langs as $id => $l): ?><a class="tech" href="quiz.php" style="--c:<?= h($l[1]) ?>"><?= h($l[0]) ?></a><?php endforeach; ?>
    </section>

    <section class="sec">
        <h2>Como funciona</h2>
        <p>Três passos para virar o hábito de praticar todo dia.</p>
        <div class="passos">
            <div><span class="n">1</span><h3>Aprenda</h3><p>Leia os cursos curtos de HTML, CSS, JavaScript e PHP.</p></div>
            <div><span class="n">2</span><h3>Pratique</h3><p>Responda quizzes cronometrados e resolva desafios escrevendo código.</p></div>
            <div><span class="n">3</span><h3>Evolua</h3><p>Ganhe XP, suba de nível, conquiste medalhas e entre no ranking.</p></div>
        </div>
    </section>

    <section class="sec">
        <h2>Pratique do jeito que o cérebro gosta</h2>
        <p>Perguntas curtas, código de verdade e correção na hora.</p>
        <div class="feat">
            <div><span class="ic">⌨️</span><h3>Editor de código com desafios</h3><p>Escreva HTML, CSS, JavaScript e PHP direto no navegador, veja o resultado e receba o XP quando todos os testes passarem.</p></div>
            <div><span class="ic">⚡</span><h3>Feedback instantâneo</h3><p>Errou? A explicação aparece na hora, com o motivo da resposta certa.</p></div>
            <div><span class="ic">🔥</span><h3>Sequências e XP</h3><p>Acerte em sequência e responda rápido para ganhar pontos extras.</p></div>
            <div><span class="ic">🏆</span><h3>Níveis e medalhas</h3><p>Suba do nível Curioso até Lenda e conquiste medalhas ao dominar cada linguagem.</p></div>
            <div><span class="ic">📚</span><h3>Cursos de apoio</h3><p>Introduções objetivas às quatro linguagens antes de jogar.</p></div>
        </div>
    </section>

    <?php if ($top): ?>
    <section class="sec">
        <h2>Quem está no topo</h2>
        <p>Os jogadores com mais XP no momento.</p>
        <ol class="mini-rank">
            <?php foreach ($top as $n => $r): $ni = nivel_info((int) $r['xp']); ?>
            <li><span class="pos"><?= $medalhas[$n] ?></span><span class="nome"><?= h($r['nome']) ?></span><span class="meta">Nv <?= $ni['nivel'] ?> · <?= h($ni['titulo']) ?></span><strong><?= (int) $r['xp'] ?> XP</strong></li>
            <?php endforeach; ?>
        </ol>
        <a href="ranking.php" class="btn btn-o">Ver ranking completo</a>
    </section>
    <?php endif; ?>

    <section class="cta">
        <h2>Pronto para o primeiro desafio?</h2>
        <p>Escolha uma linguagem e jogue em menos de 2 minutos.</p>
        <a href="quiz.php" class="btn">Escolher linguagem</a>
    </section>
</main>
<?php include 'includes/footer.php'; ?>
