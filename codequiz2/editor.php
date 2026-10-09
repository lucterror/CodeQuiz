<?php
require_once 'includes/xp.php';
$titulo = 'Editor de código'; $pagina = 'editor';
$descricao = 'Resolva desafios de HTML, CSS, JavaScript e PHP escrevendo código no navegador e ganhe XP no ranking.';
$langs = require 'data/quiz-data.php';
$desafios = require 'data/desafios.php';
$usuario = usuario_atual();

// Desafios já resolvidos (e a solução salva) do usuário logado.
$resolvidos = [];
if ($usuario) {
    $st = db()->prepare('SELECT desafio_id, codigo FROM desafios_resolvidos WHERE usuario_id = ?');
    $st->execute([$usuario['id']]);
    $resolvidos = array_column($st->fetchAll(), 'codigo', 'desafio_id');
}

// Seleção: ?d=id, ou ?l=linguagem (primeiro ainda não resolvido), ou o primeiro de todos.
$id = (string) ($_GET['d'] ?? '');
if (!isset($desafios[$id])) {
    $l = isset($langs[$_GET['l'] ?? '']) ? $_GET['l'] : array_key_first($langs);
    $id = '';
    foreach ($desafios as $k => $d) {
        if ($d['lang'] !== $l) continue;
        if ($id === '') $id = $k;
        if (!isset($resolvidos[$k])) { $id = $k; break; }
    }
}
$atual = $desafios[$id];
$langAtual = $atual['lang'];

// Dados enviados ao navegador (os padrões do servidor só vão para PHP, onde também servem de teste).
$boot = [
    'id' => $id, 'lang' => $langAtual, 'titulo' => $atual['titulo'], 'enunciado' => $atual['enunciado'], 'dica' => $atual['dica'],
    'xp' => $atual['xp'], 'html' => $atual['html'] ?? '', 'exporta' => $atual['exporta'] ?? [],
    'testes' => $atual['testes'], 'padroes' => $langAtual === 'php' ? $atual['padroes'] : [],
    'inicial' => $atual['inicial'], 'salvo' => $resolvidos[$id] ?? null, 'resolvido' => isset($resolvidos[$id]),
    'logado' => (bool) $usuario, 'csrf' => csrf_token(),
];
$niveis = [1 => 'Fácil', 2 => 'Médio', 3 => 'Difícil'];
$totalGeral = count($desafios); $feitosGeral = count($resolvidos);
include 'includes/header.php';
?>
<main class="ed-page">
    <aside class="ed-side" aria-label="Lista de desafios">
        <div class="ed-prog"><strong><?= $feitosGeral ?>/<?= $totalGeral ?></strong> desafios resolvidos<?php if (!$usuario): ?><br><small><a href="cadastro.php">Crie uma conta</a> para ganhar XP.</small><?php endif; ?></div>
        <?php foreach ($langs as $lid => $lg): ?>
        <details class="grupo" style="--c:<?= h($lg[1]) ?>" <?= $lid === $langAtual ? 'open' : '' ?>>
            <summary><span class="tag">&lt;<?= h($lg[0]) ?>/&gt;</span></summary>
            <?php foreach ($desafios as $k => $d): if ($d['lang'] !== $lid) continue; ?>
            <a href="editor.php?d=<?= h($k) ?>" class="item-d <?= $k === $id ? 'on' : '' ?>" data-id="<?= h($k) ?>"<?= $k === $id ? ' aria-current="true"' : '' ?>>
                <span class="chk"><?= isset($resolvidos[$k]) ? '✓' : '' ?></span>
                <span class="tt"><?= h($d['titulo']) ?></span>
                <small><?= $niveis[$d['nivel']] ?> · <?= (int) $d['xp'] ?> XP</small>
            </a>
            <?php endforeach; ?>
        </details>
        <?php endforeach; ?>
    </aside>

    <section class="ed-main" style="--c:<?= h($langs[$langAtual][1]) ?>">
        <header class="ed-head">
            <span class="chip">&lt;<?= h($langs[$langAtual][0]) ?>/&gt;</span>
            <span class="nivel n<?= (int) $atual['nivel'] ?>"><?= $niveis[$atual['nivel']] ?></span>
            <span class="vale">⭐ <?= (int) $atual['xp'] ?> XP</span>
            <span class="feito <?= isset($resolvidos[$id]) ? '' : 'hidden' ?>" id="feito">✓ Resolvido</span>
        </header>
        <h1><?= h($atual['titulo']) ?></h1>
        <p class="enun"><?= h($atual['enunciado']) ?></p>

        <div class="ed-grid">
            <div class="ce-wrap">
                <div class="ce-bar">
                    <span class="arq" id="arq"></span>
                    <span class="atalho">Ctrl + Enter para <span id="atalho-txt">executar</span></span>
                </div>
                <div class="ce" id="ce">
                    <div class="ce-nums" id="nums" aria-hidden="true"></div>
                    <div class="ce-area">
                        <pre class="ce-hl" id="hl" aria-hidden="true"></pre>
                        <textarea id="cod" class="ce-ta" spellcheck="false" autocapitalize="off" autocomplete="off" autocorrect="off" aria-label="Editor de código"></textarea>
                    </div>
                </div>
                <div class="ed-acoes">
                    <button class="btn btn-p" id="rodar" type="button">▶ Executar e testar</button>
                    <button class="btn btn-o" id="dica-btn" type="button">💡 Dica</button>
                    <button class="btn btn-o" id="reset" type="button">↺ Recomeçar</button>
                </div>
                <p class="dica-box hidden" id="dica-box"></p>
            </div>

            <div class="ed-saida">
                <div class="painel" id="painel-prev">
                    <h2>Resultado</h2>
                    <iframe id="runner" title="Resultado do código" sandbox="allow-scripts" src="runner.html"></iframe>
                    <pre class="console hidden" id="console" aria-live="polite"></pre>
                    <p class="nota-php hidden" id="nota-php">O PHP roda no servidor, então aqui o código é verificado por padrões (sem executar nada). Confira cada item da lista.</p>
                </div>
                <div class="painel">
                    <h2>Testes <span id="placar" class="placar"></span></h2>
                    <ul class="testes" id="testes"></ul>
                    <div class="msg-final hidden" id="msg-final" role="status" aria-live="polite"></div>
                </div>
            </div>
        </div>
    </section>
</main>
<script type="application/json" id="ed-boot"><?= json_encode($boot, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
<script src="js/editor.js"></script>
<?php include 'includes/footer.php'; ?>
