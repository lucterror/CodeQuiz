<?php
require_once 'includes/xp.php';
$usuario = exigir_login();
$titulo = 'Meu perfil'; $pagina = 'perfil';
$uid = (int) $usuario['id'];
$langs = require 'data/quiz-data.php';
$desafios = require 'data/desafios.php';
$db = db();

$ni = nivel_info(xp_total($uid));
$pos = posicao($uid);

$st = $db->prepare('SELECT linguagem, COUNT(*) n, MAX(ROUND(100.0 * acertos / total)) melhor, SUM(xp) xp FROM resultados WHERE usuario_id = ? GROUP BY linguagem');
$st->execute([$uid]);
$quiz = array_column($st->fetchAll(), null, 'linguagem');

$st = $db->prepare('SELECT desafio_id FROM desafios_resolvidos WHERE usuario_id = ?');
$st->execute([$uid]);
$resolvidos = array_column($st->fetchAll(), 'desafio_id');

$st = $db->prepare("SELECT 'Quiz' tipo, linguagem, xp, acertos || '/' || total detalhe, criado_em FROM resultados WHERE usuario_id = :u AND linguagem IN (" . in_langs() . ")
    UNION ALL SELECT 'Desafio', linguagem, xp, desafio_id, criado_em FROM desafios_resolvidos WHERE usuario_id = :u ORDER BY criado_em DESC LIMIT 8");
$st->execute([':u' => $uid]);
$recentes = $st->fetchAll();

$meds = medalhas($uid);
$ganhas = count(array_filter($meds, fn($m) => $m[3]));
$partidas = array_sum(array_column($quiz, 'n'));
include 'includes/header.php';
?>
<main class="wrap">
    <section class="perfil-topo card">
        <div class="avatar" aria-hidden="true"><?= h(mb_strtoupper(mb_substr($usuario['nome'], 0, 1))) ?></div>
        <div class="pt-info">
            <h1><?= h($usuario['nome']) ?></h1>
            <p class="mut">Nível <?= $ni['nivel'] ?> · <strong><?= h($ni['titulo']) ?></strong> · plano <?= h($usuario['plano']) ?></p>
            <div class="xpbar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= $ni['pct'] ?>" aria-label="Progresso para o próximo nível"><i style="width:<?= $ni['pct'] ?>%"></i></div>
            <p class="mut"><?= $ni['xp'] ?> XP · faltam <?= $ni['falta'] ?> XP para o nível <?= $ni['nivel'] + 1 ?></p>
        </div>
        <div class="pt-stats">
            <div><b><?= $pos ? $pos['pos'] . 'º' : '—' ?></b><span>no ranking</span></div>
            <div><b><?= $partidas ?></b><span>rodadas de quiz</span></div>
            <div><b><?= count($resolvidos) ?>/<?= count($desafios) ?></b><span>desafios</span></div>
        </div>
    </section>

    <section class="sec2">
        <h2>Progresso por linguagem</h2>
        <div class="langs-prog">
            <?php foreach ($langs as $id => $l):
                $tot = count(array_filter($desafios, fn($x) => $x['lang'] === $id));
                $feitos = count(array_filter($resolvidos, fn($r) => ($desafios[$r]['lang'] ?? '') === $id));
                $pctD = $tot ? round(100 * $feitos / $tot) : 0; ?>
            <div class="card lp" style="--c:<?= h($l[1]) ?>">
                <span class="tag">&lt;<?= h($l[0]) ?>/&gt;</span>
                <p>Melhor no quiz: <strong><?= isset($quiz[$id]) ? (int) $quiz[$id]['melhor'] . '%' : '—' ?></strong></p>
                <p>Desafios: <strong><?= $feitos ?>/<?= $tot ?></strong></p>
                <div class="xpbar sm"><i style="width:<?= $pctD ?>%"></i></div>
                <a href="editor.php?l=<?= h($id) ?>" class="lnk">Praticar no editor →</a>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="sec2">
        <h2>Medalhas <small class="mut">(<?= $ganhas ?>/<?= count($meds) ?>)</small></h2>
        <div class="medalhas">
            <?php foreach ($meds as [$ic, $nome, $desc, $ok]): ?>
            <div class="medalha <?= $ok ? 'ok' : '' ?>" title="<?= h($desc) ?>">
                <span class="ic"><?= $ok ? $ic : '🔒' ?></span><strong><?= h($nome) ?></strong><small><?= h($desc) ?></small>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="sec2">
        <h2>Atividade recente</h2>
        <?php if (!$recentes): ?>
            <div class="card vazio"><p>Nada por aqui ainda.</p><a href="quiz.php" class="btn btn-p">Jogar o primeiro quiz</a></div>
        <?php else: ?>
        <ul class="atividade">
            <?php foreach ($recentes as $r): ?>
            <li>
                <span class="tipo"><?= h($r['tipo']) ?></span>
                <span><?= h($langs[$r['linguagem']][0] ?? $r['linguagem']) ?> · <?= $r['tipo'] === 'Quiz' ? h($r['detalhe']) . ' acertos' : h($desafios[$r['detalhe']]['titulo'] ?? $r['detalhe']) ?></span>
                <strong>+<?= (int) $r['xp'] ?> XP</strong>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </section>
</main>
<?php include 'includes/footer.php'; ?>
