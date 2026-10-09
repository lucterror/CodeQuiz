<?php
// Registra a resolução de um desafio do editor e concede o XP (uma única vez por desafio).
require_once __DIR__ . '/../includes/xp.php';
header('Content-Type: application/json; charset=utf-8');
function fim(int $c, array $d = []): never {
    http_response_code($c);
    echo json_encode(['ok' => $c === 200] + $d, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') fim(405);
$u = usuario_atual();
if (!$u) fim(401, ['erro' => 'Entre na sua conta para ganhar XP.']);
$d = json_decode(file_get_contents('php://input'), true) ?: [];
if (!hash_equals($_SESSION['csrf'] ?? '', (string) ($d['csrf'] ?? ''))) fim(403, ['erro' => 'Sessão expirada. Recarregue a página.']);

$desafios = require __DIR__ . '/../data/desafios.php';
$id = (string) ($d['id'] ?? '');
$codigo = (string) ($d['codigo'] ?? '');
if (!isset($desafios[$id])) fim(404, ['erro' => 'Desafio não encontrado.']);
if ($codigo === '' || strlen($codigo) > 8000) fim(422, ['erro' => 'Código vazio ou grande demais.']);
$ds = $desafios[$id];

// Conferência no servidor (o navegador roda os testes de verdade; aqui checamos o essencial antes de pagar o XP).
// O código enviado é só texto: nunca é executado no servidor.
foreach ($ds['padroes'] as [$desc, $re, $flags]) {
    if (!preg_match('~' . $re . '~' . $flags, $codigo)) fim(422, ['erro' => 'Ainda falta: ' . $desc . '.']);
}

$ni0 = nivel_info(xp_total((int) $u['id']));
$st = db()->prepare('INSERT OR IGNORE INTO desafios_resolvidos (usuario_id, desafio_id, linguagem, xp, codigo) VALUES (?,?,?,?,?)');
$st->execute([$u['id'], $id, $ds['lang'], $ds['xp'], $codigo]);
$novo = $st->rowCount() > 0;
if (!$novo) {   // já resolvido: apenas atualiza a solução salva
    db()->prepare('UPDATE desafios_resolvidos SET codigo = ? WHERE usuario_id = ? AND desafio_id = ?')->execute([$codigo, $u['id'], $id]);
}
$total = xp_total((int) $u['id']);
$ni = nivel_info($total);
fim(200, ['xp_ganho' => $novo ? $ds['xp'] : 0, 'repetido' => !$novo, 'xp_total' => $total, 'nivel' => $ni['nivel'], 'titulo' => $ni['titulo'], 'subiu' => $ni['nivel'] > $ni0['nivel']]);
