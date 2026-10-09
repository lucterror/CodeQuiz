<?php
// Cursos: cada seção = [título, texto, código opcional]
return [
 'html' => [
  'nome' => 'HTML', 'cor' => '#f97316', 'titulo' => 'Introdução ao HTML',
  'sub' => 'A linguagem que dá estrutura a toda página da web.',
  'doc' => 'https://developer.mozilla.org/pt-BR/docs/Web/HTML',
  'proximo' => ['curso-css.php', 'Curso de CSS'],
  'secoes' => [
   ['O que significa HTML?', 'HTML vem de HyperText Markup Language, ou Linguagem de Marcação de Hipertexto. Ela organiza o conteúdo da página: títulos, textos, imagens, links e formulários.'],
   ['Como funciona?', 'O HTML usa tags para organizar os elementos. Uma tag normalmente tem abertura e fechamento. Juntas, com o conteúdo no meio, formam um elemento.', "<p>Olá, mundo!</p>\n\n<!-- <p>  = abertura -->\n<!-- </p> = fechamento -->\n<!-- Olá, mundo! = conteúdo -->"],
   ['Estrutura básica', 'Todo documento HTML segue um esqueleto: o head guarda informações da página e o body, o que aparece na tela.', "<!DOCTYPE html>\n<html lang=\"pt-br\">\n<head>\n  <meta charset=\"UTF-8\">\n  <title>Minha página</title>\n</head>\n<body>\n  <h1>Meu primeiro título</h1>\n</body>\n</html>"],
   ['Tags essenciais', 'Comece por estas: h1 a h6 (títulos), p (parágrafo), a (link), img (imagem), ul/ol/li (listas) e form (formulário).', "<a href=\"https://exemplo.com\">Visite</a>\n<img src=\"foto.png\" alt=\"Descrição\">\n<ul>\n  <li>Item 1</li>\n  <li>Item 2</li>\n</ul>"],
   ['Dica para iniciantes', 'Não tente aprender tudo de uma vez. Domine títulos, parágrafos, links e listas. Depois avance para formulários e semântica (header, main, footer). Pratique criando suas próprias páginas.'],
  ]],
 'css' => [
  'nome' => 'CSS', 'cor' => '#38bdf8', 'titulo' => 'Introdução ao CSS',
  'sub' => 'A linguagem que deixa suas páginas bonitas, organizadas e responsivas.',
  'doc' => 'https://developer.mozilla.org/pt-BR/docs/Web/CSS',
  'proximo' => ['curso-js.php', 'Curso de JavaScript'],
  'secoes' => [
   ['O que é CSS?', 'CSS vem de Cascading Style Sheets. Enquanto o HTML cria a estrutura, o CSS define cores, fontes, espaçamentos e a posição de cada elemento na tela.'],
   ['Regras e seletores', 'Uma regra tem um seletor (quem recebe o estilo) e um bloco com propriedades e valores. Use o nome da tag, .classe ou #id.', 'h1 {
  color: #7c5cff;
  font-size: 2rem;
}

.botao { background: #22d3ee; }
#topo  { margin-bottom: 24px; }'],
   ['Como ligar o CSS ao HTML', 'O jeito mais organizado é um arquivo separado, ligado no head da página. Assim o mesmo estilo vale para várias páginas.', '<link rel="stylesheet" href="css/estilo.css">'],
   ['Caixa: margin, border e padding', 'Todo elemento é uma caixa: conteúdo, padding (espaço interno), border (borda) e margin (espaço externo). Com box-sizing: border-box, a largura já inclui padding e borda.', '* { box-sizing: border-box; }

.card {
  padding: 20px;
  border: 2px solid #27305a;
  border-radius: 12px;
  margin: 16px 0;
}'],
   ['Flexbox', 'O Flexbox alinha itens em uma direção. Com display: flex você controla o espaçamento (gap), o alinhamento (align-items) e a distribuição (justify-content).', '.linha {
  display: flex;
  gap: 16px;
  align-items: center;
  justify-content: space-between;
}'],
   ['Grid', 'O Grid organiza em linhas e colunas. É ideal para galerias e layouts de página.', '.grade {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}'],
   ['Responsividade', 'Media queries mudam o estilo conforme a tela. Pense primeiro no celular e adapte para telas maiores.', '@media (max-width: 600px) {
  .grade { grid-template-columns: 1fr; }
}'],
   ['Dica para iniciantes', 'Use classes em vez de estilizar tudo pela tag, mantenha um padrão de cores e teste no celular. Depois resolva os desafios de CSS no editor.'],
  ]],
 'js' => [
  'nome' => 'JavaScript', 'cor' => '#facc15', 'titulo' => 'Introdução ao JavaScript',
  'sub' => 'A linguagem que dá vida às páginas: cliques, formulários, jogos e muito mais.',
  'doc' => 'https://developer.mozilla.org/pt-BR/docs/Web/JavaScript',
  'proximo' => ['curso-php.php', 'Curso de PHP'],
  'secoes' => [
   ['O que é JavaScript?', 'JavaScript é a linguagem de programação dos navegadores. Ela reage a ações da pessoa (como um clique), altera a página sem recarregar e conversa com servidores.'],
   ['Variáveis', 'Use const para valores que não serão reatribuídos e let para os que mudam. Evite var em código novo.', 'const nome = "Ana";
let pontos = 0;
pontos += 10;

console.log(nome, pontos);'],
   ['Tipos e comparações', 'Os tipos principais são string, number, boolean, null, undefined, array e objeto. Compare com === (valor e tipo) em vez de ==.', 'console.log(2 + "2");   // "22" (concatena)
console.log(2 === "2");  // false
console.log(typeof []);  // "object"'],
   ['Funções', 'Funções agrupam um trecho de código para reutilizar. As arrow functions são uma forma curta de escrevê-las.', 'function dobro(n) {
  return n * 2;
}
const triplo = n => n * 3;

console.log(dobro(4), triplo(4));'],
   ['Arrays e métodos', 'map transforma cada item, filter mantém só alguns e reduce junta tudo em um valor.', 'const nums = [1, 2, 3, 4];
const dobros = nums.map(n => n * 2);     // [2, 4, 6, 8]
const pares = nums.filter(n => n % 2 === 0); // [2, 4]
const soma = nums.reduce((a, b) => a + b, 0); // 10'],
   ['Mexendo na página (DOM)', 'O DOM é a página vista como objetos. Selecione elementos com querySelector e reaja a eventos com addEventListener.', 'const botao = document.querySelector(".botao");
botao.addEventListener("click", () => {
  botao.textContent = "Clicado!";
});'],
   ['Dica para iniciantes', 'Abra o console do navegador (F12) e experimente. Errar faz parte: leia a mensagem de erro com calma. Depois pratique nos desafios de JavaScript do editor.'],
  ]],
 'php' => [
  'nome' => 'PHP', 'cor' => '#a78bfa', 'titulo' => 'Introdução ao PHP',
  'sub' => 'Aprenda a programar no lado do servidor e criar sites dinâmicos.',
  'doc' => 'https://www.php.net/manual/pt_BR/',
  'proximo' => ['editor.php', 'Desafios no editor'],
  'secoes' => [
   ['O que é PHP?', 'PHP é uma linguagem muito usada para criar sites e sistemas com informações dinâmicas. Diferente de HTML e CSS, ela executa comandos no servidor e toma decisões de acordo com os dados recebidos: login, cadastro, formulários e páginas personalizadas.'],
   ['Como funciona?', 'Quando alguém acessa uma página PHP, o servidor executa o código e envia só o resultado ao navegador. Por isso o usuário nunca vê o PHP, apenas o que ele produziu.'],
   ['Variáveis', 'Variáveis guardam informações usadas durante o programa, como nome, idade ou pontuação em um quiz. No PHP, começam com o símbolo $.', "<?php\n\$nome = \"Maria\";     // string (texto)\n\$idade = 30;          // inteiro\n\$altura = 1.75;       // float (decimal)\n\$estudando = true;    // booleano\n\necho \"Nome: \$nome\";\necho \"Idade: \$idade anos\";\n?>"],
   ['Condições', 'Condições permitem que o programa tome decisões. Em um quiz, por exemplo, verificamos se a resposta está certa e mostramos uma mensagem para cada caso.', "<?php\nif (\$resposta === \$correta) {\n    echo \"Acertou!\";\n} else {\n    echo \"Tente de novo.\";\n}\n?>"],
   ['Repetições', 'Estruturas de repetição executam uma ação várias vezes, útil para percorrer listas sem repetir código.', "<?php\n\$techs = ['HTML', 'CSS', 'PHP'];\nforeach (\$techs as \$t) {\n    echo \"<li>\$t</li>\";\n}\n?>"],
   ['Por que aprender PHP?', 'É uma ótima porta de entrada para entender programação no servidor e funciona junto com HTML e CSS para criar sistemas completos.'],
   ['Dica para iniciantes', 'Comece por variáveis, condições e repetições. Depois avance para funções, formulários e banco de dados. Aprenda aos poucos e use projetos próprios para testar.'],
  ]],
];
