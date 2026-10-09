<?php
require_once 'includes/xp.php';
$titulo = 'Ranking'; $pagina = 'ranking';
$langs = require 'data/quiz-data.php';
$usuario = usuario_atual();
$l = isset($langs[$_GET['lang'] ?? '']) ? $_GET['lang'] : null;
$semana = ($_GET['p'] ?? '') === 'semana';
$dias = $semana ? 7 : null;
$top = ranking($l, $dias, 10);
$eu = $usuario ? posicao((int) $usuario['id'], $l, $dias) : null;
$medalhas = ['🥇', '🥈', '🥉'];
$qs = fn(array $x) => '?' . http_build_query(array_filter($x + ['lang' => $l, 'p' => $semana ? 'semana' : null]));
include 'includes/header.php';
?>
<main class="wrap">
    <section class="ph"><h1>Ranking</h1><p>Os 10 jogadores com mais XP, somando quizzes e desafios do editor. Jogue logado para entrar na lista.</p></section>
    <div class="filtros" aria-label="Período">
        <a href="<?= $qs(['p' => null]) ?>" class="<?= $semana ? '' : 'on' ?>">Geral</a>
        <a href="<?= $qs(['p' => 'semana']) ?>" class="<?= $semana ? 'on' : '' ?>">Últimos 7 dias</a>
    </div>
    <div class="filtros" aria-label="Linguagem">
        <a href="<?= $qs(['lang' => null]) ?>" class="<?= $l ? '' : 'on' ?>">Todas</a>
        <?php foreach ($langs as $id => $x): ?><a href="<?= $qs(['lang' => $id]) ?>" class="<?= $l === $id ? 'on' : '' ?>"><?= h($x[0]) ?></a><?php endforeach; ?>
    </div>
    <?php if (!$top): ?>
        <div class="card vazio"><h3>Ninguém pontuou ainda</h3><p>Seja o primeiro do ranking.</p><a href="<?= $usuario ? 'quiz.php' : 'cadastro.php' ?>" class="btn btn-p"><?= $usuario ? 'Jogar agora' : 'Cadastre-se aqui' ?></a></div>
    <?php else: ?>
    <ol class="rank">
        <?php foreach ($top as $n => $r): $ni = nivel_info((int) $r['xp']); $taxa = $r['total'] > 0 ? round(100 * $r['acertos'] / $r['total']) : null; ?>
        <li class="<?= $usuario && $usuario['id'] == $r['id'] ? 'eu' : '' ?>">
            <span class="pos"><?= $medalhas[$n] ?? $n + 1 ?></span>
            <span class="nome"><?= h($r['nome']) ?> <small class="nvl">Nv <?= $ni['nivel'] ?> · <?= h($ni['titulo']) ?></small></span>
            <span class="meta"><?= (int) $r['partidas'] ?> partidas<?= $taxa !== null ? ' · ' . (int) $taxa . '% de acertos' : '' ?> · <?= (int) $r['desafios'] ?> desafios</span>
            <strong><?= (int) $r['xp'] ?> XP</strong>
        </li>
        <?php endforeach; ?>
    </ol>
    <?php endif; ?>
    <?php if ($usuario): ?>
    <p class="minha-pos card">
        <?php if ($eu): ?>Você está em <strong><?= $eu['pos'] ?>º lugar</strong> com <strong><?= $eu['xp'] ?> XP</strong>. <a href="perfil.php">Ver meu perfil</a>
        <?php else: ?>Você ainda não pontuou neste filtro. <a href="quiz.php">Jogue o quiz</a> ou <a href="editor.php">resolva um desafio</a>.<?php endif; ?>
    </p>
    <?php endif; ?>
</main>
<?php include 'includes/footer.php'; ?>
