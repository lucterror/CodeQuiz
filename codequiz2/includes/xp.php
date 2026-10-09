<?php
// XP, níveis, ranking e medalhas. Todo XP vem de duas fontes: quizzes (resultados) e desafios do editor.
require_once __DIR__ . '/auth.php';

function linguagens_validas(): array {
    static $l = null;
    return $l ??= array_keys(require __DIR__ . '/../data/quiz-data.php');
}

// Só conta linguagens que existem hoje (resultados antigos de linguagens removidas ficam guardados, mas fora do XP).
function in_langs(): string {
    return "'" . implode("','", linguagens_validas()) . "'";
}

function xp_total(int $uid): int {
    $st = db()->prepare('SELECT
        (SELECT COALESCE(SUM(xp), 0) FROM resultados WHERE usuario_id = :u AND linguagem IN (' . in_langs() . '))
      + (SELECT COALESCE(SUM(xp), 0) FROM desafios_resolvidos WHERE usuario_id = :u)');
    $st->execute([':u' => $uid]);
    return (int) $st->fetchColumn();
}

const NIVEIS = ['Curioso', 'Aprendiz', 'Estagiário', 'Dev Júnior', 'Dev Pleno', 'Dev Sênior', 'Arquiteto', 'Lenda'];

function nivel_info(int $xp): array {
    $n = (int) floor(sqrt($xp / 60)) + 1;
    $ini = 60 * ($n - 1) ** 2;
    $fim = 60 * $n ** 2;
    return [
        'nivel' => $n, 'titulo' => NIVEIS[min($n, count(NIVEIS)) - 1], 'xp' => $xp,
        'ini' => $ini, 'fim' => $fim, 'falta' => $fim - $xp,
        'pct' => (int) round(100 * ($xp - $ini) / ($fim - $ini)),
    ];
}

// Ranking geral ou por linguagem; $dias = só os últimos N dias (null = sempre).
function ranking(?string $lang = null, ?int $dias = null, int $limite = 10): array {
    $f = $dias ? " AND criado_em >= datetime('now', '-" . (int) $dias . " days')" : '';
    $sql = 'SELECT u.id, u.nome, SUM(t.xp) xp, SUM(t.partidas) partidas, SUM(t.acertos) acertos, SUM(t.total) total, SUM(t.desafios) desafios
        FROM (
            SELECT usuario_id, linguagem, xp, 1 partidas, acertos, total, 0 desafios FROM resultados WHERE linguagem IN (' . in_langs() . ')' . $f . '
            UNION ALL
            SELECT usuario_id, linguagem, xp, 0, 0, 0, 1 FROM desafios_resolvidos WHERE 1 = 1' . $f . '
        ) t JOIN usuarios u ON u.id = t.usuario_id '
        . ($lang ? 'WHERE t.linguagem = ? ' : '') . 'GROUP BY u.id HAVING SUM(t.xp) > 0 ORDER BY xp DESC, u.id ASC LIMIT ' . (int) $limite;
    $st = db()->prepare($sql);
    $st->execute($lang ? [$lang] : []);
    return $st->fetchAll();
}

function posicao(int $uid, ?string $lang = null, ?int $dias = null): ?array {
    foreach (ranking($lang, $dias, 5000) as $i => $r) {
        if ((int) $r['id'] === $uid) return ['pos' => $i + 1, 'xp' => (int) $r['xp']];
    }
    return null;
}

// Medalhas calculadas a partir do histórico real do usuário.
function medalhas(int $uid): array {
    $langs = require __DIR__ . '/../data/quiz-data.php';
    $desafios = require __DIR__ . '/../data/desafios.php';
    $db = db();

    $st = $db->prepare('SELECT linguagem, MAX(ROUND(100.0 * acertos / total)) melhor, COUNT(*) n FROM resultados WHERE usuario_id = ? GROUP BY linguagem');
    $st->execute([$uid]);
    $q = array_column($st->fetchAll(), null, 'linguagem');

    $st = $db->prepare('SELECT linguagem, COUNT(*) n FROM desafios_resolvidos WHERE usuario_id = ? GROUP BY linguagem');
    $st->execute([$uid]);
    $d = array_column($st->fetchAll(), 'n', 'linguagem');

    $totalQuiz = array_sum(array_column($q, 'n'));
    $totalDes = array_sum($d);
    $xp = xp_total($uid);

    $m = [
        ['🎯', 'Primeira rodada', 'Complete um quiz.', $totalQuiz >= 1],
        ['⌨️', 'Mão na massa', 'Resolva seu primeiro desafio no editor.', $totalDes >= 1],
        ['🔥', 'Pegando o ritmo', 'Complete 5 rodadas de quiz.', $totalQuiz >= 5],
        ['🧩', 'Resolvedor', 'Resolva 5 desafios no editor.', $totalDes >= 5],
    ];
    foreach ($langs as $id => $l) {
        $m[] = ['🏆', 'Nota 10 em ' . $l[0], 'Acerte 100% de uma rodada de ' . $l[0] . '.', ((int) ($q[$id]['melhor'] ?? 0)) >= 100];
        $tot = count(array_filter($desafios, fn($x) => $x['lang'] === $id));
        $m[] = ['🛠️', 'Mestre de ' . $l[0], 'Resolva todos os desafios de ' . $l[0] . '.', $tot > 0 && ($d[$id] ?? 0) >= $tot];
    }
    $m[] = ['⭐', '500 XP', 'Chegue a 500 XP.', $xp >= 500];
    $m[] = ['💎', '1000 XP', 'Chegue a 1000 XP.', $xp >= 1000];
    return $m;
}
