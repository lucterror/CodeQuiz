<?php
require_once __DIR__ . '/xp.php';
$usuario = usuario_atual();
$pagina = $pagina ?? '';
$titulo = $titulo ?? 'CodeQuiz';
$descricao = $descricao ?? 'Aprenda HTML, CSS, JavaScript e PHP com quizzes, desafios no editor de código, XP e ranking.';
$nv = $usuario ? nivel_info(xp_total((int) $usuario['id'])) : null;
$menu = ['home.php' => ['Início', ['home']], 'services.php' => ['Cursos', ['services', 'curso']],
         'quiz.php' => ['Quiz', ['quiz']], 'editor.php' => ['Editor', ['editor']], 'ranking.php' => ['Ranking', ['ranking']],
         'assinaturas.php' => ['Planos', ['assinaturas']], 'sobre.php' => ['Sobre', ['sobre']], 'contato.php' => ['Contato', ['contato']]];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= h($descricao) ?>">
    <meta name="theme-color" content="#0b0f1e">
    <title><?= h($titulo) ?> | CodeQuiz</title>
    <link rel="icon" type="image/png" href="img/code-quiz.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="css/base.css">
    <?php if ($pagina && file_exists(__DIR__ . "/../css/pages/{$pagina}.css")): ?>
    <link rel="stylesheet" href="css/pages/<?= h($pagina) ?>.css">
    <?php endif; ?>
</head>
<body>
<a class="skip" href="#conteudo">Pular para o conteúdo</a>
<nav class="nav" aria-label="Principal">
    <a href="home.php" class="logo"><img src="img/code-quiz.png" alt="CodeQuiz"></a>
    <button class="burger" aria-label="Abrir menu" aria-expanded="false" aria-controls="menu-links">☰</button>
    <div class="nav-links" id="menu-links">
        <?php foreach ($menu as $url => [$nome, $ativos]): ?>
        <a href="<?= $url ?>" class="<?= in_array($pagina, $ativos) ? 'on' : '' ?>"<?= in_array($pagina, $ativos) ? ' aria-current="page"' : '' ?>><?= $nome ?></a>
        <?php endforeach; ?>
    </div>
    <div class="nav-user">
        <?php if ($usuario): ?>
            <a href="perfil.php" class="xp-chip" title="Ver meu perfil" id="xp-chip"><b>Nv <span data-nv><?= $nv['nivel'] ?></span></b> · <span data-xp><?= $nv['xp'] ?></span> XP</a>
            <form method="POST" action="logout.php" class="inline">
                <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
                <button class="btn btn-o btn-sm" type="submit">Sair</button>
            </form>
        <?php else: ?>
            <a href="login.php" class="login">Entrar</a>
            <a href="cadastro.php" class="btn btn-p btn-sm">Cadastre-se aqui</a>
        <?php endif; ?>
    </div>
</nav>
<div id="conteudo" tabindex="-1">
