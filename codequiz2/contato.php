<?php
require 'includes/auth.php';
$titulo = 'Contato'; $pagina = 'contato';
$erros = []; $enviado = false;
$v = ['nome' => '', 'email' => '', 'assunto' => trim((string) ($_GET['assunto'] ?? '')), 'mensagem' => ''];
$u = usuario_atual();
if ($u && $v['nome'] === '') { $v['nome'] = $u['nome']; $v['email'] = $u['email']; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['nome', 'email', 'assunto', 'mensagem'] as $k) { $v[$k] = trim((string) ($_POST[$k] ?? '')); }
    if (!csrf_ok()) $erros['geral'] = 'Sessão expirada. Recarregue a página e tente novamente.';
    elseif (!empty($_POST['site'])) { $enviado = true; $v = array_map(fn() => '', $v); }   // honeypot: robôs preenchem
    elseif (time() - ($_SESSION['ultima_msg'] ?? 0) < 30) $erros['geral'] = 'Aguarde alguns segundos antes de enviar outra mensagem.';
    else {
        if (mb_strlen($v['nome']) < 2 || mb_strlen($v['nome']) > 80) $erros['nome'] = 'Informe seu nome.';
        if (!filter_var($v['email'], FILTER_VALIDATE_EMAIL)) $erros['email'] = 'Informe um e-mail válido.';
        if ($v['assunto'] === '' || mb_strlen($v['assunto']) > 120) $erros['assunto'] = 'Informe o assunto (até 120 caracteres).';
        if (mb_strlen($v['mensagem']) < 10) $erros['mensagem'] = 'Escreva ao menos 10 caracteres.';
        if (mb_strlen($v['mensagem']) > 3000) $erros['mensagem'] = 'A mensagem pode ter até 3000 caracteres.';
        if (!$erros) {
            db()->prepare('INSERT INTO mensagens (nome, email, assunto, mensagem, usuario_id) VALUES (?,?,?,?,?)')
                ->execute([$v['nome'], $v['email'], $v['assunto'], $v['mensagem'], $u['id'] ?? null]);
            $_SESSION['ultima_msg'] = time();
            $enviado = true; $v = array_map(fn() => '', $v);
        }
    }
}
$e = fn($c) => isset($erros[$c]) ? '<small class="erro" id="e-' . $c . '">' . h($erros[$c]) . '</small>' : '';
$val = fn($c) => h($v[$c]);
include 'includes/header.php';
?>
<main class="wrap">
    <section class="ph"><h1>Entre em contato</h1><p>Dúvida, sugestão ou problema? Fale com a equipe CodeQuiz.</p></section>
    <div class="cols">
        <div class="info">
            <div class="card"><strong>📧 E-mail</strong><span>contato@codequiz.com</span></div>
            <div class="card"><strong>💬 Suporte</strong><span>Respondemos o mais rápido possível.</span></div>
            <div class="card"><strong>💡 Sugestões</strong><span>Envie ideias de novos quizzes e desafios para o CodeQuiz.</span></div>
        </div>
        <form class="card form" method="POST" novalidate>
            <h2>Envie uma mensagem</h2>
            <?php if ($enviado): ?><p class="ok" role="status">Mensagem enviada! Responderemos em breve.</p><?php endif; ?>
            <?php if (isset($erros['geral'])): ?><p class="alerta" role="alert"><?= h($erros['geral']) ?></p><?php endif; ?>
            <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
            <div class="hp" aria-hidden="true"><label for="site">Não preencha este campo</label><input id="site" name="site" tabindex="-1" autocomplete="off"></div>
            <label for="nome">Nome</label><input id="nome" name="nome" value="<?= $val('nome') ?>" placeholder="Seu nome" autocomplete="name" maxlength="80"><?= $e('nome') ?>
            <label for="email">E-mail</label><input id="email" name="email" type="email" value="<?= $val('email') ?>" placeholder="voce@email.com" autocomplete="email"><?= $e('email') ?>
            <label for="assunto">Assunto</label><input id="assunto" name="assunto" value="<?= $val('assunto') ?>" placeholder="Sobre o que quer falar?" maxlength="120"><?= $e('assunto') ?>
            <label for="mensagem">Mensagem</label><textarea id="mensagem" name="mensagem" rows="5" maxlength="3000" placeholder="Escreva sua mensagem"><?= $val('mensagem') ?></textarea><?= $e('mensagem') ?>
            <button class="btn btn-p" type="submit">Enviar mensagem</button>
        </form>
    </div>
</main>
<?php include 'includes/footer.php'; ?>
