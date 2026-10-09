<?php
// Banco de perguntas: [pergunta, código, [opções], índice da correta, explicação]
// Cada partida sorteia perguntas do banco e embaralha as opções (ver js/quiz.js).
$d = [
 'html'=>['HTML','#f97316','Estrutura de páginas web',[
  ['Qual tag cria o maior título da página?','',['<title>','<h1>','<head>','<header>'],1,'<h1> é o título de maior nível. <title> aparece na aba do navegador.'],
  ['Qual atributo define o destino de um link?','<a ___="pagina.php">Ir</a>',['src','link','href','url'],2,'href (hypertext reference) guarda o endereço do link.'],
  ['O que significa a sigla HTML?','',['HyperText Markup Language','High Tech Modern Language','Hyper Transfer Markup Link','Home Tool Markup Language'],0,'É a Linguagem de Marcação de Hipertexto.'],
  ['Qual tag cria uma lista numerada?','',['<ul>','<li>','<ol>','<dl>'],2,'<ol> é a lista ordenada; <ul> é sem numeração.'],
  ['Qual tag insere uma imagem na página?','',['<image>','<img>','<picture>','<src>'],1,'<img> é uma tag sem fechamento; o caminho do arquivo vai no atributo src.'],
  ['Qual atributo descreve a imagem para leitores de tela?','<img src="gato.png" ___="Gato dormindo">',['title','alt','name','caption'],1,'alt é o texto alternativo: aparece se a imagem falhar e é lido por tecnologias assistivas.'],
  ['Qual tag semântica é indicada para o menu de navegação?','',['<nav>','<menu>','<div>','<section>'],0,'<nav> marca blocos de links de navegação e ajuda leitores de tela e buscadores.'],
  ['Qual tag quebra a linha sem iniciar um novo parágrafo?','',['<lb>','<br>','<break>','<hr>'],1,'<br> quebra a linha. <hr> desenha uma linha horizontal.'],
  ['Qual tag guarda o conteúdo visível da página, como textos e imagens?','',['<head>','<body>','<meta>','<title>'],1,'<body> contém o que aparece na janela; <head> guarda metadados e links de recursos.'],
  ['Qual valor de type faz o navegador validar o formato de e-mail?','<input type="___" name="email">',['mail','email','text-email','address'],1,'type="email" valida o formato e mostra o teclado adequado no celular.'],
  ['Para que serve <!DOCTYPE html>?','',['Criar um comentário','Informar ao navegador que o documento é HTML5','Linkar o arquivo CSS','Definir o título da página'],1,'A declaração ativa o modo padrão do navegador e deve ser a primeira linha do arquivo.'],
  ['Qual tag cria uma célula de dados dentro de uma tabela?','',['<tr>','<td>','<th>','<tb>'],1,'<tr> é a linha, <td> a célula de dados e <th> a célula de cabeçalho.'],
  ['Qual atributo torna um campo de formulário obrigatório?','<input type="text" ___>',['mandatory','required','needed','validate'],1,'required impede o envio do formulário enquanto o campo estiver vazio.'],
  ['Qual tag marca o conteúdo principal e único da página?','',['<main>','<article>','<aside>','<div>'],0,'<main> marca o conteúdo central; deve haver apenas um visível por página.'],
  ['O que o atributo target="_blank" faz em um link?','<a href="x.html" target="_blank">',['Abre o link em uma nova aba','Baixa o arquivo','Esconde o link','Fecha a página atual'],0,'target="_blank" abre em nova aba; use também rel="noopener" por segurança.'],
  ['Qual elemento liga um texto a um campo de formulário e melhora a acessibilidade?','',['<label>','<legend>','<caption>','<span>'],0,'<label for="id"> associa o texto ao campo: clicar no texto foca o campo e leitores de tela leem o rótulo.']]],
 'css'=>['CSS','#38bdf8','Estilo e layout',[
  ['Qual propriedade muda a cor do texto?','',['font-color','text-color','color','foreground'],2,'color define a cor do texto; background-color define o fundo.'],
  ['Como selecionar um elemento com class="botao"?','',['#botao','.botao','botao','*botao'],1,'O ponto (.) seleciona classes; # seleciona ids.'],
  ['Qual valor de display coloca itens lado a lado de forma flexível?','',['block','inline','flex','table'],2,'display:flex ativa o Flexbox.'],
  ['O que faz margin?','',['Espaço interno','Espaço externo ao elemento','Cor da borda','Tamanho da fonte'],1,'margin é o espaço fora; padding é o espaço dentro.'],
  ['Qual propriedade arredonda os cantos de um elemento?','',['corner-radius','border-radius','round','border-curve'],1,'border-radius arredonda os cantos; 50% em um quadrado forma um círculo.'],
  ['Qual unidade equivale a 1% da largura da janela do navegador?','',['vh','vw','%','rem'],1,'vw = 1% da largura da viewport; vh = 1% da altura.'],
  ['Qual propriedade alinha itens no eixo principal de um container flex?','',['align-items','justify-content','text-align','place-self'],1,'justify-content atua no eixo principal; align-items, no eixo cruzado.'],
  ['Qual seletor tem maior especificidade?','',['p','.destaque','#titulo','div p'],2,'A ordem é: id > classe > tag.'],
  ['Qual valor de position mantém o elemento fixo na tela ao rolar a página?','',['absolute','relative','fixed','static'],2,'position:fixed posiciona o elemento em relação à janela.'],
  ['O que este seletor faz?','a:hover { color: red; }',['Estiliza links já visitados','Estiliza o link enquanto o mouse está sobre ele','Estiliza apenas o primeiro link','Esconde o link'],1,':hover é uma pseudo-classe aplicada quando o cursor está sobre o elemento.'],
  ['Como criar uma grade com 3 colunas iguais?','display: grid;\ngrid-template-columns: ___;',['3 1fr','repeat(3, 1fr)','columns(3)','1fr * 3'],1,'repeat(3, 1fr) cria 3 colunas que dividem o espaço igualmente.'],
  ['Qual propriedade controla a ordem de empilhamento de elementos sobrepostos?','',['layer','z-index','order-z','stack'],1,'z-index maior fica por cima; funciona em elementos posicionados e itens de flex/grid.'],
  ['O que faz box-sizing: border-box?','',['Inclui padding e borda na largura total do elemento','Remove a borda','Aumenta a margem','Torna o elemento circular'],0,'Com border-box, width inclui padding e borda, o que facilita calcular o layout.'],
  ['Qual propriedade cria espaço entre os itens de um Flexbox ou Grid?','',['gap','spacing','margin-between','space'],0,'gap define o espaçamento entre itens em Flexbox e Grid.'],
  ['Qual regra aplica estilos apenas em telas com até 600px de largura?','',['@media (max-width: 600px)','@screen (600px)','@responsive 600px','@width < 600px'],0,'Media queries aplicam estilos conforme características da tela, como a largura.'],
  ['Qual propriedade esconde o elemento mas mantém o espaço dele na página?','',['display: none','visibility: hidden','opacity: 1','hidden: true'],1,'visibility: hidden oculta o elemento e preserva o espaço; display: none o remove do layout.']]],
 'js'=>['JavaScript','#facc15','Interatividade na web',[
  ['Qual a saída?','console.log(2 + "2");',['4','22','NaN','Erro'],1,'Somar número com string concatena, resultando em 22.'],
  ['Qual palavra declara uma constante?','',['var','let','const','static'],2,'const não permite reatribuição.'],
  ['O que [1,2,3].map(n => n * 2) retorna?','',['[1,2,3]','[2,4,6]','6','undefined'],1,'map cria um novo array aplicando a função a cada item.'],
  ['Qual operador compara valor e tipo?','',['==','=','===','!='],2,'=== não faz conversão de tipo.'],
  ['Qual a saída?','console.log(typeof null);',['null','object','undefined','number'],1,'É um bug histórico da linguagem: typeof null devolve "object".'],
  ['Qual método adiciona um item ao final de um array?','',['add()','push()','append()','unshift()'],1,'push adiciona no fim; unshift adiciona no início.'],
  ['Qual a saída?','const nome = "Ana";\nconsole.log(`Olá, ${nome}!`);',['Olá, ${nome}!','Olá, Ana!','Olá, nome!','Erro'],1,'Template literals (crases) interpolam expressões dentro de ${ }.'],
  ['Qual a saída?','console.log(0.1 + 0.2 === 0.3);',['true','false','undefined','NaN'],1,'Números decimais usam ponto flutuante: 0.1 + 0.2 resulta em 0.30000000000000004.'],
  ['Qual método seleciona um elemento da página usando um seletor CSS?','',['getElementByCss()','querySelector()','selectElement()','findElement()'],1,'querySelector devolve o primeiro elemento que casa com o seletor.'],
  ['O que [1,2,3,4].filter(n => n % 2 === 0) retorna?','',['[2, 4]','[1, 3]','[false, true, false, true]','2'],0,'filter mantém apenas os itens para os quais a função devolve true.'],
  ['Qual a saída?','let x = 5;\nx += 3;\nconsole.log(x);',['53','8','x3','Erro'],1,'x += 3 equivale a x = x + 3.'],
  ['O que a função fetch() retorna?','',['Os dados diretamente','Uma Promise','Um array','Um objeto XML'],1,'fetch devolve uma Promise; use await ou .then() para obter a resposta.'],
  ['O que faz o método addEventListener?','',['Registra uma função para reagir a um evento','Cria um novo elemento','Remove um elemento da página','Converte texto em número'],0,'addEventListener liga uma função (callback) a um evento, como click ou input.'],
  ['Qual a saída?','console.log([10, 2, 1].sort());',['[1, 2, 10]','[1, 10, 2]','[10, 2, 1]','[2, 10, 1]'],1,'sort() sem função compara como texto: "1" < "10" < "2". Para números use sort((a, b) => a - b).'],
  ['Qual a saída?','console.log(null ?? "padrão");',['null','padrão','undefined','Erro'],1,'?? (nullish coalescing) devolve o valor da direita quando o da esquerda é null ou undefined.'],
  ['Qual a saída?','const o = { a: 1 };\nconst { a } = o;\nconsole.log(a);',['{ a: 1 }','1','undefined','Erro'],1,'A desestruturação extrai a propriedade a do objeto para uma variável.']]],
 'php'=>['PHP','#a78bfa','Programação no servidor',[
  ['Com qual símbolo começam as variáveis?','',['@','$','&','#'],1,'Toda variável PHP começa com $.'],
  ['Qual construção é a mais usada para exibir texto na tela?','',['echo','write','console.log','display'],0,'echo envia texto para a saída.'],
  ['Qual superglobal guarda dados enviados por POST?','',['$_GET','$_POST','$_SERVER','$_FILE'],1,'$_POST recebe dados de formulários method="POST".'],
  ['Como concatenar strings?','',['+','&','.','||'],2,'No PHP o ponto (.) concatena: "a" . "b".'],
  ['Qual função conta os elementos de um array?','',['length()','count()','size()','total()'],1,'count($array) devolve a quantidade de elementos.'],
  ['Qual função inicia uma sessão?','',['session_open()','session_start()','start_session()','new_session()'],1,'session_start() deve ser chamada antes de qualquer saída para o navegador.'],
  ['Qual a saída?','$a = "5";\n$b = 3;\necho $a + $b;',['53','8','Erro','5 3'],1,'O operador + converte strings numéricas; a concatenação seria feita com o ponto.'],
  ['Qual função escapa caracteres HTML para evitar ataques XSS?','',['strip_all()','htmlspecialchars()','addslashes()','escape_html()'],1,'htmlspecialchars converte <, >, & e aspas em entidades seguras.'],
  ['Qual tag abre um bloco de código PHP?','',['<?php','<php>','<%','<script php>'],0,'Os blocos PHP começam com <?php e terminam com ?>.'],
  ['Qual função verifica se uma variável existe e não é null?','',['defined()','isset()','exists()','is_set()'],1,'isset() devolve true se a variável foi definida e não é null.'],
  ['Qual é a forma correta de proteger consultas SQL contra injeção?','',['Concatenar as variáveis na query','Usar prepared statements (PDO)','Usar apenas aspas simples','Usar die() após a query'],1,'Prepared statements separam o SQL dos dados enviados pelo usuário.'],
  ['Qual o resultado?','$n = 10;\nvar_dump($n == "10");',['bool(true)','bool(false)','int(10)','string(2) "10"'],0,'== compara com conversão de tipo; com === o resultado seria false.'],
  ['Qual operador compara valor e tipo no PHP?','',['==','===','=','<=>'],1,'=== só é verdadeiro se valor e tipo forem iguais; <=> é o operador de comparação "nave espacial".'],
  ['O que o operador ?? faz?','echo $_GET["p"] ?? "inicio";',['Usa o valor da direita se a esquerda não existir ou for null','Compara duas strings','Concatena valores','Gera um erro'],0,'?? (null coalescing) evita avisos de índice inexistente e define um valor padrão.'],
  ['Qual função gera um hash seguro de senha?','',['md5()','password_hash()','sha1()','crypt_simple()'],1,'password_hash() usa algoritmos modernos com sal automático; confira depois com password_verify().'],
  ['Qual a saída?','$lista = [1, 2, 3];\necho count($lista) * 2;',['3','6','12','Erro'],1,'count devolve 3, e 3 * 2 = 6.']]],
];
// Os trechos de código usam \n literal em aspas simples: converte para quebra de linha real.
foreach ($d as &$l) {
    foreach ($l[3] as &$q) { $q[1] = str_replace('\n', "\n", $q[1]); }
    unset($q);
}
unset($l);
return $d;
