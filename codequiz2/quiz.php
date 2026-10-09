<?php
require_once 'includes/xp.php';
$titulo = 'Quiz de linguagens'; $pagina = 'quiz';
$linguagens = require 'data/quiz-data.php';
$cfg = require 'data/quiz-config.php';
$usuario = usuario_atual();
include 'includes/header.php';
?>
<main class="qz">
    <section id="menu">
        <h1>Escolha sua linguagem</h1>
        <p class="sub"><?= $cfg['por_rodada'] ?> perguntas sorteadas por rodada, <?= $cfg['tempo'] ?> segundos cada. Use as teclas 1–4 ou A–D para responder e H para a dica 50/50. Sequências e rapidez valem mais XP.</p>
        <div class="langs">
        <?php foreach ($linguagens as $id => $l): ?>
            <button class="lang" style="--c:<?= h($l[1]) ?>" data-id="<?= h($id) ?>">
                <span class="tag">&lt;<?= h($l[0]) ?>/&gt;</span><h3><?= h($l[0]) ?></h3>
                <small><?= h($l[2]) ?> · <?= count($l[3]) ?> perguntas no banco</small>
                <em class="melhor" data-melhor="<?= h($id) ?>"></em>
            </button>
        <?php endforeach; ?>
        </div>
        <p class="sub dica-editor">Quer praticar escrevendo código? <a href="editor.php">Abra o editor de desafios →</a></p>
    </section>

    <section id="jogo" class="hidden">
        <div class="hud">
            <button class="back" id="sair">← Trocar linguagem</button>
            <div class="bar" role="progressbar" aria-label="Progresso da rodada" aria-valuemin="0" aria-valuemax="100" id="barra"><i id="prog"></i></div>
            <span class="pill" id="num"></span><span class="pill" id="tempo">⏱ <?= $cfg['tempo'] ?>s</span><span class="pill" title="Sequência de acertos">🔥 <span id="seq">0</span></span><span class="pill">⭐ <span id="xp">0</span> XP</span>
        </div>
        <div class="timebar" aria-hidden="true"><i id="tbar"></i></div>
        <div class="box" id="card">
            <h2 id="perg"></h2><div class="code hidden" id="cod"></div>
            <div class="opts" id="ops" role="group" aria-label="Alternativas"></div>
            <div class="fb" id="fb" aria-live="polite"></div>
            <div class="rodape">
                <button class="btn btn-o dica" id="dica" type="button" title="Remove 2 alternativas erradas (a pergunta vale metade do XP)">💡 50/50 <small>(H)</small></button>
                <button class="btn btn-p next" id="prox">Próxima</button>
            </div>
        </div>
    </section>

    <section id="fim" class="hidden">
        <div class="box res">
            <div class="medal" id="medal"></div><div class="big" id="pct"></div>
            <h2 id="msg"></h2><p id="det"></p><p id="recorde" class="recorde"></p>
            <div id="rev" class="rev hidden"></div>
            <p id="salvo" class="salvo" aria-live="polite"></p>
            <div class="acts" style="justify-content:center"><button class="btn btn-p" id="de-novo">Jogar de novo</button><button class="btn btn-o" id="outra">Outra linguagem</button></div>
        </div>
    </section>
</main>
<script type="application/json" id="quiz-boot"><?= json_encode(['logado' => (bool) $usuario, 'csrf' => csrf_token(), 'cfg' => $cfg, 'dados' => $linguagens], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
<script src="js/quiz.js"></script>
<?php include 'includes/footer.php'; ?>
