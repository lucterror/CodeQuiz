<?php
// Sessão, banco SQLite e utilitários de autenticação.
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src https://fonts.gstatic.com; img-src 'self' data:; connect-src 'self'; frame-src 'self'; form-action 'self'; base-uri 'none'; frame-ancestors 'self'");
}
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS'])]);
    session_start();
}

function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    $pdo = new PDO('sqlite:' . __DIR__ . '/../storage/codequiz.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');
    $pdo->exec('CREATE TABLE IF NOT EXISTS usuarios (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        senha TEXT NOT NULL,
        plano TEXT NOT NULL DEFAULT "gratis",
        criado_em TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)');
    $pdo->exec('CREATE TABLE IF NOT EXISTS resultados (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        usuario_id INTEGER NOT NULL REFERENCES usuarios(id),
        linguagem TEXT NOT NULL, acertos INTEGER NOT NULL, total INTEGER NOT NULL, xp INTEGER NOT NULL,
        criado_em TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)');
    // Desafios do editor de código: cada desafio dá XP uma única vez por usuário.
    $pdo->exec('CREATE TABLE IF NOT EXISTS desafios_resolvidos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        usuario_id INTEGER NOT NULL REFERENCES usuarios(id),
        desafio_id TEXT NOT NULL, linguagem TEXT NOT NULL, xp INTEGER NOT NULL, codigo TEXT NOT NULL DEFAULT "",
        criado_em TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE (usuario_id, desafio_id))');
    $pdo->exec('CREATE TABLE IF NOT EXISTS mensagens (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL, email TEXT NOT NULL, assunto TEXT NOT NULL, mensagem TEXT NOT NULL,
        usuario_id INTEGER, criado_em TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)');
    $pdo->exec('CREATE TABLE IF NOT EXISTS login_falhas (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ip TEXT NOT NULL, email TEXT NOT NULL, criado_em TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)');
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_resultados_usuario ON resultados(usuario_id, linguagem)');
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_desafios_usuario ON desafios_resolvidos(usuario_id)');
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_falhas ON login_falhas(ip, criado_em)');
    return $pdo;
}

function usuario_atual(): ?array {
    static $cache = false;
    if ($cache !== false && ($cache['id'] ?? null) == ($_SESSION['uid'] ?? null)) return $cache;
    if (empty($_SESSION['uid'])) return $cache = null;
    $st = db()->prepare('SELECT id, nome, email, plano FROM usuarios WHERE id = ?');
    $st->execute([$_SESSION['uid']]);
    return $cache = ($st->fetch() ?: null);
}

function entrar(int $id): void {
    session_regenerate_id(true);
    $_SESSION['uid'] = $id;
}

function csrf_token(): string {
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}
function csrf_ok(): bool {
    return hash_equals($_SESSION['csrf'] ?? '', (string) ($_POST['csrf'] ?? ''));
}
function so_visitante(): void {
    if (!empty($_SESSION['uid'])) { header('Location: quiz.php'); exit; }
}
function exigir_login(): array {
    $u = usuario_atual();
    if (!$u) { header('Location: login.php'); exit; }
    return $u;
}
function h(?string $s): string {
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Limite de tentativas de login por IP (8 falhas em 15 minutos bloqueiam por um tempo).
function login_bloqueado(): bool {
    $st = db()->prepare("SELECT COUNT(*) FROM login_falhas WHERE ip = ? AND criado_em >= datetime('now', '-15 minutes')");
    $st->execute([$_SERVER['REMOTE_ADDR'] ?? '']);
    return (int) $st->fetchColumn() >= 8;
}
function login_falhou(string $email): void {
    db()->prepare('INSERT INTO login_falhas (ip, email) VALUES (?, ?)')->execute([$_SERVER['REMOTE_ADDR'] ?? '', $email]);
}
