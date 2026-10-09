# CodeQuiz — o que mudou

- **Linguagens:** agora só HTML, CSS, JavaScript e PHP (Python e SQL saíram; resultados antigos continuam no banco, mas não contam XP).
- **Editor de código** (`editor.php`): 16 desafios (4 por linguagem) com testes. HTML, CSS e JS rodam de verdade num iframe isolado (sandbox); PHP é verificado por padrões, porque código enviado nunca é executado no servidor.
- **XP no ranking:** cada desafio dá XP uma única vez; o servidor reconfere o código (`api/desafio.php`) antes de pagar. Ranking soma quiz + desafios, com filtro por linguagem e "últimos 7 dias".
- **Níveis, perfil e medalhas** (`perfil.php`), XP/nível no topo do site.
- **Cursos novos:** CSS e JavaScript. Quiz: 16 perguntas por linguagem (eram 12).
- **Segurança:** logout por POST+CSRF, limite de tentativas de login, CSP e cabeçalhos, formulário de contato salvo no banco (tabela `mensagens`) com CSRF e anti-robô.
- **Correção:** trechos de código do quiz mostravam `\n` literal; agora quebram linha.

Requisitos: PHP 8.1+ com PDO SQLite. As tabelas novas são criadas automaticamente no primeiro acesso.
Para adicionar desafios, edite `data/desafios.php`.
