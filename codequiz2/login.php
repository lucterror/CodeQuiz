<?php
require 'includes/auth.php';
so_visitante();
$titulo = 'Entrar'; $pagina = 'auth';
$erro = ''; $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mb_strtolower(trim($_POST['email'] ?? ''));
    $senha = $_POST['senha'] ?? '';
    if (!csrf_ok()) {
        $erro = 'Sessão expirada. Tente novamente.';
    } elseif (login_bloqueado()) {
        $erro = 'Muitas tentativas. Aguarde alguns minutos e tente de novo.';
    } else {
        $st = db()->prepare('SELECT id, senha FROM usuarios WHERE email = ?');
        $st->execute([$email]);
        $u = $st->fetch();
        // Mensagem genérica: não revela se o e-mail existe.
        if ($u && password_verify($senha, $u['senha'])) {
            entrar((int) $u['id']);
            header('Location: quiz.php'); exit;
        }
        login_falhou($email);
        $erro = 'E-mail ou senha incorretos.';
    }
}
include 'includes/header.php';
?>
<main class="auth">
    <form class="card form" method="POST" novalidate>
        <img src="img/code-quiz.png" alt="CodeQuiz" class="auth-logo">
        <h1>Bem-vindo de volta</h1>
        <p class="sub">Entre para continuar de onde parou.</p>
        <?php if ($erro): ?><p class="alerta" role="alert"><?= $erro ?></p><?php endif; ?>
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

        <label for="email">E-mail</label>
        <input id="email" name="email" type="email" autocomplete="email" value="<?= h($email) ?>" required>
        <label for="senha">Senha</label>
        <input id="senha" name="senha" type="password" autocomplete="current-password" required>

        <button class="btn btn-p" type="submit">Entrar</button>
        <p class="troca">Ainda não tem conta? <a href="cadastro.php"><strong>Cadastre-se aqui</strong></a></p>
    </form>
</main>
<?php include 'includes/footer.php'; ?>
