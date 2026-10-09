<?php
require 'includes/auth.php';
so_visitante();
$titulo = 'Criar conta'; $pagina = 'auth';
$erros = []; $v = ['nome' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $v['nome']  = trim($_POST['nome'] ?? '');
    $v['email'] = mb_strtolower(trim($_POST['email'] ?? ''));
    $senha = $_POST['senha'] ?? ''; $conf = $_POST['confirmar'] ?? '';

    if (!csrf_ok()) $erros['geral'] = 'Sessão expirada. Tente novamente.';
    if (mb_strlen($v['nome']) < 2 || mb_strlen($v['nome']) > 60) $erros['nome'] = 'Informe um nome entre 2 e 60 caracteres.';
    if (!filter_var($v['email'], FILTER_VALIDATE_EMAIL)) $erros['email'] = 'Informe um e-mail válido.';
    if (strlen($senha) < 8) $erros['senha'] = 'A senha precisa ter ao menos 8 caracteres.';
    if ($senha !== $conf) $erros['confirmar'] = 'As senhas não coincidem.';

    if (!$erros) {
        $st = db()->prepare('SELECT 1 FROM usuarios WHERE email = ?');
        $st->execute([$v['email']]);
        if ($st->fetch()) {
            $erros['email'] = 'Este e-mail já está cadastrado. Tente entrar.';
        } else {
            $st = db()->prepare('INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)');
            $st->execute([$v['nome'], $v['email'], password_hash($senha, PASSWORD_DEFAULT)]);
            entrar((int) db()->lastInsertId());
            header('Location: quiz.php'); exit;
        }
    }
}
$e = fn($c) => isset($erros[$c]) ? '<small class="erro">' . $erros[$c] . '</small>' : '';
include 'includes/header.php';
?>
<main class="auth">
    <form class="card form" method="POST" novalidate>
        <img src="img/code-quiz.png" alt="CodeQuiz" class="auth-logo">
        <h1>Crie sua conta</h1>
        <p class="sub">Salve seu progresso e comece a ganhar XP.</p>
        <?php if (isset($erros['geral'])): ?><p class="alerta" role="alert"><?= $erros['geral'] ?></p><?php endif; ?>
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

        <label for="nome">Nome</label>
        <input id="nome" name="nome" autocomplete="name" value="<?= h($v['nome']) ?>" required><?= $e('nome') ?>
        <label for="email">E-mail</label>
        <input id="email" name="email" type="email" autocomplete="email" value="<?= h($v['email']) ?>" required><?= $e('email') ?>
        <label for="senha">Senha</label>
        <input id="senha" name="senha" type="password" autocomplete="new-password" minlength="8" required><?= $e('senha') ?>
        <label for="confirmar">Confirmar senha</label>
        <input id="confirmar" name="confirmar" type="password" autocomplete="new-password" required><?= $e('confirmar') ?>

        <button class="btn btn-p" type="submit">Criar conta grátis</button>
        <p class="troca">Já tem conta? <a href="login.php">Entrar</a></p>
    </form>
</main>
<?php include 'includes/footer.php'; ?>
