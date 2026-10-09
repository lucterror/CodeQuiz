<?php
require 'includes/auth.php';
// Só encerra a sessão por POST com token CSRF (evita logout forçado por links ou imagens de terceiros).
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_ok()) {
    $_SESSION = [];
    session_destroy();
}
header('Location: home.php');
exit;
