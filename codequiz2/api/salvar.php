<?php
// Salva o resultado de uma rodada do quiz (JSON).
require_once __DIR__ . '/../includes/xp.php';
header('Content-Type: application/json; charset=utf-8');
function fim(int $c, array $d = []): never {
    http_response_code($c);
    echo json_encode(['ok' => $c === 200] + $d);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') fim(405);
$u = usuario_atual();
if (!$u) fim(401);
$d = json_decode(file_get_contents('php://input'), true) ?: [];
if (!hash_equals($_SESSION['csrf'] ?? '', (string) ($d['csrf'] ?? ''))) fim(403);

$langs = require __DIR__ . '/../data/quiz-data.php';
$cfg = require __DIR__ . '/../data/quiz-config.php';
$l = (string) ($d['linguagem'] ?? ''); $a = (int) ($d['acertos'] ?? -1); $t = (int) ($d['total'] ?? 0);
$esperado = isset($langs[$l]) ? min($cfg['por_rodada'], count($langs[$l][3])) : 0;
if (!isset($langs[$l]) || $t !== $esperado || $a < 0 || $a > $t) fim(422);
// Teto de XP possível para esse número de acertos: base + bônus de sequência + bônus de tempo.
$maxXp = 10 * $a + intdiv(5 * $a * ($a - 1), 2) + intdiv($cfg['tempo'], 5) * $a;
$xp = max(0, min((int) ($d['xp'] ?? 0), $maxXp));

$antes = nivel_info(xp_total((int) $u['id']))['nivel'];
db()->prepare('INSERT INTO resultados (usuario_id, linguagem, acertos, total, xp) VALUES (?,?,?,?,?)')->execute([$u['id'], $l, $a, $t, $xp]);
$total = xp_total((int) $u['id']);
$ni = nivel_info($total);
fim(200, ['xp_total' => $total, 'xp_ganho' => $xp, 'nivel' => $ni['nivel'], 'titulo' => $ni['titulo'], 'subiu' => $ni['nivel'] > $antes]);
