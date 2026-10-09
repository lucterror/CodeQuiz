<?php
// Desafios do editor de código.
// testes   = [descrição, expressão JS] avaliadas no navegador (HTML, CSS e JS rodam de verdade, isolados em um iframe).
// padroes  = [descrição, regex, flags] conferidos no servidor antes de dar XP. Em PHP também são os testes do navegador,
//            porque o PHP roda no servidor e nunca executamos código enviado por usuários.
// Helpers nos testes: q(sel), qa(sel), txt(sel), cs(sel), regras(regex), saida (linhas do console.log), f (funções exportadas), codigo.
return [

 // ---------- HTML ----------
 'html-1' => ['lang' => 'html', 'nivel' => 1, 'xp' => 30, 'titulo' => 'Título e parágrafo',
  'enunciado' => 'Crie um título h1 com o texto "Olá, mundo!" e, logo abaixo, um parágrafo p com qualquer texto.',
  'inicial' => "<!-- escreva seu HTML aqui -->\n",
  'dica' => 'As tags têm abertura e fechamento: <h1>texto</h1>.',
  'testes' => [
   ['Existe um <h1>', '!!q("h1")'],
   ['O h1 tem o texto "Olá, mundo!"', 'txt("h1") === "Olá, mundo!"'],
   ['Existe um <p> com texto', 'txt("p").length > 0'],
   ['O <p> vem depois do <h1>', '!!(q("h1") && q("p") && (q("h1").compareDocumentPosition(q("p")) & 4))'],
  ],
  'padroes' => [['h1 com o texto pedido', '<h1[^>]*>\s*Olá, mundo!\s*</h1>', 'is'], ['parágrafo', '<p[\s>]', 'i']]],

 'html-2' => ['lang' => 'html', 'nivel' => 1, 'xp' => 30, 'titulo' => 'Lista e link',
  'enunciado' => 'Crie uma lista não ordenada (ul) com 3 itens (li) e um link (a) para https://developer.mozilla.org com o texto "MDN".',
  'inicial' => "<!-- lista com 3 itens e um link -->\n",
  'dica' => 'Cada item da lista é um <li> dentro de <ul>. O destino do link fica no atributo href.',
  'testes' => [
   ['Existe uma <ul>', '!!q("ul")'],
   ['A lista tem exatamente 3 <li>', 'qa("ul > li").length === 3'],
   ['O link aponta para https://developer.mozilla.org', 'q("a") && q("a").getAttribute("href") === "https://developer.mozilla.org"'],
   ['O link tem o texto "MDN"', 'txt("a") === "MDN"'],
  ],
  'padroes' => [['lista', '<ul[\s>]', 'i'], ['3 itens', '(<li[\s>][\s\S]*){3}', 'i'], ['link MDN', '<a[^>]+href\s*=\s*["\']https://developer\.mozilla\.org["\']', 'i']]],

 'html-3' => ['lang' => 'html', 'nivel' => 2, 'xp' => 50, 'titulo' => 'Página semântica',
  'enunciado' => 'Monte a estrutura: um header com um nav contendo 2 links, um main com uma imagem (img com src e alt descritivo) e um footer.',
  'inicial' => "<!-- header > nav > 2 links; main > img; footer -->\n",
  'dica' => 'Tags semânticas descrevem o papel de cada área. O alt da imagem não pode ficar vazio.',
  'testes' => [
   ['Existe header com nav dentro', '!!q("header nav")'],
   ['O nav tem pelo menos 2 links', 'qa("nav a").length >= 2'],
   ['O main tem uma img com src', '!!q("main img[src]")'],
   ['Toda img tem alt preenchido', 'qa("img").length > 0 && qa("img").every(i => i.alt.trim().length > 0)'],
   ['Existe um footer', '!!q("footer")'],
  ],
  'padroes' => [['header', '<header[\s>]', 'i'], ['nav', '<nav[\s>]', 'i'], ['main', '<main[\s>]', 'i'], ['img com alt', '<img[^>]+alt\s*=\s*["\'][^"\']+["\']', 'i'], ['footer', '<footer[\s>]', 'i']]],

 'html-4' => ['lang' => 'html', 'nivel' => 3, 'xp' => 80, 'titulo' => 'Formulário de login',
  'enunciado' => 'Crie um form com: um label ligado (for/id) a um input type="email" name="email" obrigatório (required); um input type="password" name="senha"; e um button de envio com o texto "Entrar".',
  'inicial' => "<form>\n  <!-- campos aqui -->\n</form>\n",
  'dica' => 'O for do label deve ser igual ao id do input. required é um atributo sem valor.',
  'testes' => [
   ['Existe um form', '!!q("form")'],
   ['Campo de e-mail com name="email" e required', '!!q("form input[type=email][name=email][required]")'],
   ['O label está ligado ao campo de e-mail (for = id)', '(() => { const i = q("input[type=email]"); return !!(i && i.id && q("label[for=\"" + i.id + "\"]")); })()'],
   ['Campo de senha com name="senha"', '!!q("form input[type=password][name=senha]")'],
   ['Botão de envio com o texto "Entrar"', 'qa("form button").some(b => b.type === "submit" && b.textContent.trim() === "Entrar")'],
  ],
  'padroes' => [['form', '<form[\s>]', 'i'], ['email obrigatório', '<input(?=[^>]*type\s*=\s*["\']email["\'])(?=[^>]*required)[^>]*>', 'i'], ['label for', '<label[^>]+for\s*=', 'i'], ['senha', 'type\s*=\s*["\']password["\']', 'i'], ['botão Entrar', '<button[^>]*>\s*Entrar\s*</button>', 'i']]],

 // ---------- CSS ----------
 'css-1' => ['lang' => 'css', 'nivel' => 1, 'xp' => 30, 'titulo' => 'Cor e tamanho',
  'html' => '<h1 class="titulo">CodeQuiz</h1><p>Aprenda jogando</p>',
  'enunciado' => 'Deixe o elemento .titulo com a cor #7c5cff e font-size de 32px.',
  'inicial' => ".titulo {\n  /* sua regra aqui */\n}\n",
  'dica' => 'Use as propriedades color e font-size dentro da regra .titulo.',
  'testes' => [
   ['A cor do .titulo é #7c5cff', 'cs(".titulo").color === "rgb(124, 92, 255)"'],
   ['O tamanho da fonte é 32px', 'cs(".titulo").fontSize === "32px"'],
  ],
  'padroes' => [['color', '\.titulo\s*\{[^}]*(?<![-\w])color\s*:\s*#7c5cff', 'i'], ['font-size', 'font-size\s*:\s*32px', 'i']]],

 'css-2' => ['lang' => 'css', 'nivel' => 1, 'xp' => 40, 'titulo' => 'Cartão estilizado',
  'html' => '<div class="card">Cartão</div>',
  'enunciado' => 'Estilize .card com padding de 20px, borda de 2px sólida, border-radius de 12px e background-color #161d38.',
  'inicial' => ".card {\n  \n}\n",
  'dica' => 'A borda pode ser escrita de uma vez: border: 2px solid cor;',
  'testes' => [
   ['padding de 20px', 'cs(".card").paddingTop === "20px"'],
   ['borda de 2px sólida', 'cs(".card").borderTopWidth === "2px" && cs(".card").borderTopStyle === "solid"'],
   ['cantos com 12px', 'cs(".card").borderTopLeftRadius === "12px"'],
   ['fundo #161d38', 'cs(".card").backgroundColor === "rgb(22, 29, 56)"'],
  ],
  'padroes' => [['padding', 'padding\s*:\s*20px', 'i'], ['borda', 'border(-width)?\s*:\s*2px', 'i'], ['radius', 'border-radius\s*:\s*12px', 'i'], ['fundo', 'background(-color)?\s*:\s*#161d38', 'i']]],

 'css-3' => ['lang' => 'css', 'nivel' => 2, 'xp' => 60, 'titulo' => 'Layout com Flexbox',
  'html' => '<div class="linha"><span>A</span><span>B</span><span>C</span></div>',
  'enunciado' => 'Faça .linha um container flex: itens com espaço de 16px entre eles (gap), centralizados na vertical (align-items) e distribuídos com space-between.',
  'inicial' => ".linha {\n  \n}\n",
  'dica' => 'Comece com display: flex; depois gap, align-items e justify-content.',
  'testes' => [
   ['display: flex', 'cs(".linha").display === "flex"'],
   ['gap de 16px', 'cs(".linha").columnGap === "16px"'],
   ['align-items: center', 'cs(".linha").alignItems === "center"'],
   ['justify-content: space-between', 'cs(".linha").justifyContent === "space-between"'],
  ],
  'padroes' => [['flex', 'display\s*:\s*flex', 'i'], ['gap', 'gap\s*:\s*16px', 'i'], ['align-items', 'align-items\s*:\s*center', 'i'], ['justify-content', 'justify-content\s*:\s*space-between', 'i']]],

 'css-4' => ['lang' => 'css', 'nivel' => 3, 'xp' => 80, 'titulo' => 'Grid e hover',
  'html' => '<ul class="grade"><li>1</li><li>2</li><li>3</li><li>4</li><li>5</li><li>6</li></ul><a class="btn" href="#">Clique</a>',
  'enunciado' => 'Faça .grade uma grade de 3 colunas iguais (repeat(3, 1fr)) e faça .btn mudar o background para #22d3ee quando o mouse passar por cima (:hover).',
  'inicial' => ".grade {\n  \n}\n\n.btn {\n  background: #7c5cff;\n}\n\n/* regra do hover aqui */\n",
  'dica' => 'display: grid; grid-template-columns: repeat(3, 1fr); e uma regra separada .btn:hover { ... }',
  'testes' => [
   ['.grade usa display: grid', 'cs(".grade").display === "grid"'],
   ['.grade tem 3 colunas iguais', '(() => { const c = cs(".grade").gridTemplateColumns.split(" ").map(parseFloat); return c.length === 3 && Math.max(...c) - Math.min(...c) < 1; })()'],
   ['.btn:hover muda o fundo para #22d3ee', 'regras(/\.btn:hover/).some(r => r.style.backgroundColor === "rgb(34, 211, 238)")'],
  ],
  'padroes' => [['grid', 'display\s*:\s*grid', 'i'], ['3 colunas', 'grid-template-columns\s*:\s*repeat\(\s*3\s*,\s*1fr\s*\)', 'i'], ['hover', '\.btn\s*:hover\s*\{[^}]*#22d3ee', 'i']]],

 // ---------- JavaScript ----------
 'js-1' => ['lang' => 'js', 'nivel' => 1, 'xp' => 30, 'titulo' => 'Olá com template literal',
  'enunciado' => 'Declare const nome = "Ana" e mostre no console a frase Olá, Ana! usando template literal (crases e ${nome}).',
  'inicial' => "// escreva seu JavaScript aqui\n",
  'dica' => 'console.log(`Olá, ${nome}!`);',
  'testes' => [
   ['Imprime exatamente "Olá, Ana!"', 'saida.includes("Olá, Ana!")'],
   ['Usa ${...} dentro de um template literal', '/\$\{\s*nome\s*\}/.test(codigo)'],
   ['Declara nome com const', '/const\s+nome\s*=/.test(codigo)'],
  ],
  'padroes' => [['const nome', 'const\s+nome\s*=\s*["\']Ana["\']', ''], ['console.log', 'console\.log\s*\(', '']]],

 'js-2' => ['lang' => 'js', 'nivel' => 1, 'xp' => 40, 'titulo' => 'Função dobro',
  'enunciado' => 'Crie a função dobro(n) que recebe um número e retorna o dobro dele.',
  'inicial' => "function dobro(n) {\n  // retorne o dobro de n\n}\n",
  'exporta' => ['dobro'],
  'dica' => 'Use return n * 2;',
  'testes' => [
   ['dobro é uma função', 'typeof f.dobro === "function"'],
   ['dobro(4) retorna 8', 'f.dobro(4) === 8'],
   ['dobro(-3) retorna -6', 'f.dobro(-3) === -6'],
   ['dobro(0) retorna 0', 'f.dobro(0) === 0'],
  ],
  'padroes' => [['função dobro', 'function\s+dobro\s*\(|dobro\s*=', ''], ['return', 'return|=>', '']]],

 'js-3' => ['lang' => 'js', 'nivel' => 2, 'xp' => 60, 'titulo' => 'Soma dos pares',
  'enunciado' => 'Crie a função somaPares(lista) que recebe um array de números e retorna a soma apenas dos números pares.',
  'inicial' => "function somaPares(lista) {\n  \n}\n",
  'exporta' => ['somaPares'],
  'dica' => 'lista.filter(n => n % 2 === 0).reduce((a, b) => a + b, 0)',
  'testes' => [
   ['somaPares([1, 2, 3, 4]) retorna 6', 'f.somaPares([1, 2, 3, 4]) === 6'],
   ['somaPares([]) retorna 0', 'f.somaPares([]) === 0'],
   ['somaPares([1, 3, 5]) retorna 0', 'f.somaPares([1, 3, 5]) === 0'],
   ['somaPares([2, 4, 6, 7]) retorna 12', 'f.somaPares([2, 4, 6, 7]) === 12'],
  ],
  'padroes' => [['função somaPares', 'somaPares', ''], ['return', 'return|=>', '']]],

 'js-4' => ['lang' => 'js', 'nivel' => 3, 'xp' => 80, 'titulo' => 'FizzBuzz',
  'enunciado' => 'Crie fizzbuzz(n) que retorna um array com os itens de 1 até n: "Fizz" para múltiplos de 3, "Buzz" para múltiplos de 5, "FizzBuzz" para múltiplos de ambos; nos demais casos o número como string.',
  'inicial' => "function fizzbuzz(n) {\n  const r = [];\n  \n  return r;\n}\n",
  'exporta' => ['fizzbuzz'],
  'dica' => 'Teste primeiro o múltiplo de 15 (3 e 5 ao mesmo tempo) usando o operador %.',
  'testes' => [
   ['fizzbuzz(5) retorna ["1","2","Fizz","4","Buzz"]', 'JSON.stringify(f.fizzbuzz(5)) === JSON.stringify(["1","2","Fizz","4","Buzz"])'],
   ['fizzbuzz(15) termina em "FizzBuzz"', 'f.fizzbuzz(15)[14] === "FizzBuzz"'],
   ['fizzbuzz(15) tem 15 itens', 'f.fizzbuzz(15).length === 15'],
   ['fizzbuzz(0) retorna lista vazia', 'f.fizzbuzz(0).length === 0'],
  ],
  'padroes' => [['função fizzbuzz', 'fizzbuzz', ''], ['usa %', '%', ''], ['Fizz e Buzz', 'Fizz[\s\S]*Buzz', '']]],

 // ---------- PHP (verificado por padrões no código) ----------
 'php-1' => ['lang' => 'php', 'nivel' => 1, 'xp' => 30, 'titulo' => 'Variável e echo',
  'enunciado' => 'Crie a variável $nome com o valor "Maria" e use echo para exibir Olá, Maria! concatenando com o operador ponto (.).',
  'inicial' => "<?php\n// seu código aqui\n",
  'dica' => 'echo "Olá, " . $nome . "!";',
  'testes' => [],
  'padroes' => [['Variável $nome com "Maria"', '\$nome\s*=\s*["\']Maria["\']\s*;', 's'], ['Usa echo', 'echo\s', 's'], ['Concatena com ponto (.)', 'echo[^;]*\.[^;]*\$nome|echo[^;]*\$nome[^;]*\.', 's'], ['Mostra o texto "Olá,"', 'Olá,', 's']]],

 'php-2' => ['lang' => 'php', 'nivel' => 1, 'xp' => 40, 'titulo' => 'Aprovado ou reprovado',
  'enunciado' => 'Dada a variável $nota, use if/else para exibir "Aprovado" quando $nota for maior ou igual a 6 e "Reprovado" caso contrário.',
  'inicial' => "<?php\n\$nota = 7;\n\n",
  'dica' => 'if ($nota >= 6) { ... } else { ... }',
  'testes' => [],
  'padroes' => [['Condição $nota >= 6', 'if\s*\(\s*\$nota\s*>=\s*6\s*\)', 's'], ['Tem um else', '\}?\s*else', 's'], ['Exibe "Aprovado"', 'echo[^;]*Aprovado', 's'], ['Exibe "Reprovado"', 'echo[^;]*Reprovado', 's']]],

 'php-3' => ['lang' => 'php', 'nivel' => 2, 'xp' => 60, 'titulo' => 'Lista com foreach',
  'enunciado' => 'Crie o array $techs com "HTML", "CSS", "JS" e "PHP" e use foreach para imprimir cada item dentro de uma tag <li>.',
  'inicial' => "<?php\n\n",
  'dica' => 'foreach ($techs as $t) { echo "<li>$t</li>"; }',
  'testes' => [],
  'padroes' => [['Array $techs', '\$techs\s*=\s*(\[|array\s*\()', 's'], ['Os 4 valores no array', '(?=[\s\S]*HTML)(?=[\s\S]*CSS)(?=[\s\S]*JS)(?=[\s\S]*PHP)', 's'], ['foreach sobre $techs', 'foreach\s*\(\s*\$techs\s+as\s+\$\w+\s*\)', 's'], ['Imprime dentro de <li>', '<li>', 'is']]],

 'php-4' => ['lang' => 'php', 'nivel' => 3, 'xp' => 80, 'titulo' => 'Função segura',
  'enunciado' => 'Crie a função saudacao(string $nome): string que retorna "Olá, " seguido do nome escapado com htmlspecialchars. Depois chame a função e exiba o resultado com echo.',
  'inicial' => "<?php\n\n",
  'dica' => 'return "Olá, " . htmlspecialchars($nome);  e depois  echo saudacao("Ana");',
  'testes' => [],
  'padroes' => [['Assinatura saudacao(string $nome): string', 'function\s+saudacao\s*\(\s*string\s+\$nome\s*\)\s*:\s*string', 's'], ['Usa return', 'return\s', 's'], ['Escapa com htmlspecialchars($nome)', 'htmlspecialchars\s*\(\s*\$nome', 's'], ['Chama e exibe com echo saudacao(...)', 'echo\s+saudacao\s*\(', 's']]],
];
