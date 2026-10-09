// CodeQuiz - editor de código com desafios (realce de sintaxe, execução isolada, testes e XP)
(() => {
  const B = JSON.parse(document.getElementById('ed-boot').textContent);
  const $ = id => document.getElementById(id);
  const ta = $('cod'), hl = $('hl'), nums = $('nums'), runner = $('runner'), consoleEl = $('console');
  const ARQ = { html: 'index.html', css: 'estilo.css', js: 'script.js', php: 'index.php' };
  const ehPhp = B.lang === 'php';
  let resolvido = B.resolvido, enviando = false, execId = 0;

  // ---------- Realce de sintaxe (leve, feito à mão) ----------
  const esc = s => s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  // Cada grupo da regex vira uma classe (ou função) na mesma ordem.
  function lex(code, re, classes) {
    let out = '', i = 0, m; re.lastIndex = 0;
    while ((m = re.exec(code))) {
      if (m[0] === '') { re.lastIndex++; continue; }
      out += esc(code.slice(i, m.index));
      const g = classes.findIndex((_, k) => m[k + 1] !== undefined), c = classes[g];
      out += typeof c === 'function' ? c(m[0]) : '<span class="tk-' + c + '">' + esc(m[0]) + '</span>';
      i = m.index + m[0].length;
    }
    return out + esc(code.slice(i));
  }
  const KW_JS = 'const|let|var|function|return|if|else|for|while|do|switch|case|break|continue|new|class|extends|import|export|from|async|await|try|catch|finally|throw|typeof|instanceof|in|of|this|null|undefined|true|false';
  const KW_PHP = 'function|return|if|else|elseif|foreach|for|while|do|switch|case|break|continue|echo|print|as|class|new|public|private|protected|static|use|namespace|require|require_once|include|include_once|try|catch|finally|throw|array|string|int|float|bool|void|null|true|false|isset|empty';
  const RE = {
    js: new RegExp('(\\/\\/[^\\n]*|\\/\\*[\\s\\S]*?\\*\\/)|("(?:\\\\.|[^"\\\\\\n])*"|\'(?:\\\\.|[^\'\\\\\\n])*\'|`(?:\\\\.|[^`\\\\])*`)|\\b(' + KW_JS + ')\\b|(\\b\\d+(?:\\.\\d+)?\\b)|(\\b[A-Za-z_$][\\w$]*)(?=\\s*\\()', 'g'),
    php: new RegExp('(\\/\\/[^\\n]*|#[^\\n]*|\\/\\*[\\s\\S]*?\\*\\/)|("(?:\\\\.|[^"\\\\])*"|\'(?:\\\\.|[^\'\\\\])*\')|(<\\?php|<\\?=|\\?>)|(\\$[A-Za-z_]\\w*)|\\b(' + KW_PHP + ')\\b|(\\b\\d+(?:\\.\\d+)?\\b)|(\\b[A-Za-z_]\\w*)(?=\\s*\\()', 'g'),
    css: /(\/\*[\s\S]*?\*\/)|("[^"\n]*"|'[^'\n]*')|(@[\w-]+)|(#[0-9a-fA-F]{3,8}\b)|([a-z-]+)(?=\s*:[^;{}]*[;}])|([.#][A-Za-z_][\w-]*)|(-?\d*\.?\d+(?:px|em|rem|%|vh|vw|fr|s|ms|deg)?\b)/g,
    html: /(<!--[\s\S]*?-->)|(<\/?[A-Za-z!][^>]*>?)/g,
    tag: /(<\/?)([\w-]+)|(\s)([\w:-]+)(?==)|("[^"]*"|'[^']*')/g,
  };
  const lexTag = t => {
    let out = '', i = 0, m; RE.tag.lastIndex = 0;
    while ((m = RE.tag.exec(t))) {
      out += esc(t.slice(i, m.index));
      if (m[2] !== undefined) out += esc(m[1]) + '<span class="tk-t">' + esc(m[2]) + '</span>';
      else if (m[4] !== undefined) out += esc(m[3]) + '<span class="tk-a">' + esc(m[4]) + '</span>';
      else out += '<span class="tk-s">' + esc(m[5]) + '</span>';
      i = m.index + m[0].length;
    }
    return out + esc(t.slice(i));
  };
  const realcar = code => {
    switch (B.lang) {
      case 'js': return lex(code, RE.js, ['c', 's', 'k', 'n', 'f']);
      case 'php': return lex(code, RE.php, ['c', 's', 'p', 'v', 'k', 'n', 'f']);
      case 'css': return lex(code, RE.css, ['c', 's', 'p', 'n', 'a', 'k', 'n']);
      default: return lex(code, RE.html, ['c', lexTag]);
    }
  };
  function atualizar() {
    hl.innerHTML = realcar(ta.value) + '\n';
    const n = ta.value.split('\n').length;
    if (nums.childElementCount !== n) nums.innerHTML = Array.from({ length: n }, (_, k) => '<span>' + (k + 1) + '</span>').join('');
    sincronizar();
  }
  function sincronizar() { hl.scrollTop = ta.scrollTop; hl.scrollLeft = ta.scrollLeft; nums.scrollTop = ta.scrollTop; }

  // ---------- Comportamento do editor: Tab, Enter com indentação, Ctrl+Enter ----------
  let soltarTab = false;
  function inserir(txt, ini = ta.selectionStart, fim = ta.selectionEnd) {
    ta.focus(); ta.setSelectionRange(ini, fim);
    if (!document.execCommand('insertText', false, txt)) ta.setRangeText(txt, ini, fim, 'end');   // execCommand preserva o desfazer (Ctrl+Z)
    atualizar(); salvarRascunho();
  }
  ta.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') { e.preventDefault(); rodar(); return; }
    if (e.key === 'Escape') { soltarTab = true; return; }
    if (e.key === 'Tab' && !soltarTab) {
      e.preventDefault();
      const v = ta.value, a = ta.selectionStart, b = ta.selectionEnd;
      if (a === b && !e.shiftKey) { inserir('  '); return; }
      const ini = v.lastIndexOf('\n', a - 1) + 1, bloco = v.slice(ini, b), linhas = bloco.split('\n');
      const novo = e.shiftKey ? linhas.map(l => l.replace(/^ {1,2}/, '')).join('\n') : linhas.map(l => '  ' + l).join('\n');
      inserir(novo, ini, b); ta.setSelectionRange(ini, ini + novo.length);
      return;
    }
    soltarTab = false;
    if (e.key === 'Enter' && !e.shiftKey && !e.ctrlKey && !e.metaKey) {
      e.preventDefault();
      const v = ta.value, a = ta.selectionStart, ini = v.lastIndexOf('\n', a - 1) + 1;
      const atual = v.slice(ini, a), ind = atual.match(/^\s*/)[0];
      const extra = /[{[(]\s*$/.test(atual) ? '  ' : '';
      inserir('\n' + ind + extra);
    }
  });
  ta.addEventListener('input', () => { atualizar(); salvarRascunho(); });
  ta.addEventListener('scroll', sincronizar);

  // ---------- Rascunho (localStorage pode estar indisponível) ----------
  const CH = 'cq_rascunho_' + B.id;
  function salvarRascunho() { try { localStorage.setItem(CH, ta.value); } catch {} }
  function lerRascunho() { try { return localStorage.getItem(CH); } catch { return null; } }

  // ---------- Testes ----------
  function testesDoDesafio() {
    if (!ehPhp) return B.testes;
    return B.padroes.map(([desc]) => [desc, '']);
  }
  function mostrarTestes(lista, ativo = true) {
    const ul = $('testes'); ul.innerHTML = '';
    lista.forEach(t => {
      const li = document.createElement('li');
      li.className = !ativo ? '' : t.ok ? 'ok' : 'no';
      const ic = document.createElement('span'); ic.className = 'ic'; ic.textContent = !ativo ? '○' : t.ok ? '✓' : '✗';
      const tx = document.createElement('span'); tx.textContent = t.desc;
      li.append(ic, tx);
      if (t.erro) { const s = document.createElement('small'); s.textContent = t.erro; li.append(s); }
      ul.append(li);
    });
    const ok = lista.filter(t => t.ok).length;
    $('placar').textContent = ativo ? ok + '/' + lista.length : '';
    return ok === lista.length && lista.length > 0;
  }
  function conferirPhp(codigo) {
    return B.padroes.map(([desc, src, flags]) => { let ok = false; try { ok = new RegExp(src, flags).test(codigo); } catch {} return { desc, ok }; });
  }

  // ---------- Execução no iframe isolado ----------
  let pronto = false, esperando = null;
  window.addEventListener('message', e => {
    if (e.source !== runner.contentWindow) return;
    const d = e.data; if (!d || d.cq !== 1) return;
    if (d.tipo === 'pronto') pronto = true;
    if (d.tipo === 'resultado' && esperando && d.id === esperando.id) { clearTimeout(esperando.t); esperando.ok(d); esperando = null; }
  });
  function enviar(job) {
    return new Promise((ok, falha) => {
      const id = ++execId;
      const t = setTimeout(() => { esperando = null; falha(new Error('O executor não respondeu. Recarregue a página.')); }, 7000);
      esperando = { id, ok, t };
      const go = () => runner.contentWindow.postMessage(Object.assign({ cq: 1, id }, job), '*');
      if (pronto) return go();
      const w = setInterval(() => { if (pronto) { clearInterval(w); go(); } }, 50);   // aguarda o runner carregar
    });
  }

  async function rodar() {
    if (enviando) return;
    const codigo = ta.value, btn = $('rodar');
    btn.disabled = true; $('msg-final').classList.add('hidden');
    try {
      let aprovado;
      if (ehPhp) {
        aprovado = mostrarTestes(conferirPhp(codigo));
      } else {
        const r = await enviar({ tipo: B.lang, codigo, html: B.html, testes: B.testes, exporta: B.exporta });
        if (B.lang === 'js') consoleEl.textContent = (r.saida.length ? r.saida.join('\n') : '(nada foi impresso)') + (r.erro ? '\n\n⚠ ' + r.erro : '');
        aprovado = mostrarTestes(r.testes);
      }
      if (aprovado) await concluir(codigo);
      else mensagem('Quase lá! Veja quais testes ainda falharam e tente de novo.', 'dica');
    } catch (err) {
      mensagem(err.message || 'Erro ao executar.', 'erro');
    } finally { btn.disabled = false; }
  }

  function mensagem(txt, tipo) {
    const m = $('msg-final'); m.className = 'msg-final ' + tipo; m.textContent = txt;
  }

  // ---------- Conclusão: pede o XP ao servidor ----------
  async function concluir(codigo) {
    if (!B.logado) {
      mensagem('Todos os testes passaram! 🎉 Crie uma conta ou entre para ganhar ' + B.xp + ' XP e aparecer no ranking.', 'ok');
      if (window.confete) confete();
      return;
    }
    enviando = true; mensagem('Todos os testes passaram! Registrando seu XP…', 'ok');
    try {
      const r = await fetch('api/desafio.php', { method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ csrf: B.csrf, id: B.id, codigo }) });
      const d = await r.json();
      if (!d.ok) { mensagem(d.erro || 'Não foi possível registrar o XP.', 'erro'); return; }
      marcarResolvido();
      if (d.repetido) mensagem('Correto de novo! Você já tinha ganhado o XP deste desafio. Total: ' + d.xp_total + ' XP.', 'ok');
      else {
        mensagem('', 'ok');
        const m = $('msg-final');
        m.append('🎉 +' + d.xp_ganho + ' XP! Seu total agora é ' + d.xp_total + ' XP. ');
        const a = document.createElement('a'); a.href = 'ranking.php'; a.textContent = 'Ver ranking'; m.append(a);
        if (window.toast) toast('+' + d.xp_ganho + ' XP');
        if (d.subiu && window.toast) toast('⬆ Nível ' + d.nivel + ': ' + d.titulo + '!');
        if (window.confete) confete();
      }
      document.querySelectorAll('[data-xp]').forEach(el => el.textContent = d.xp_total);
      document.querySelectorAll('[data-nv]').forEach(el => el.textContent = d.nivel);
      const prox = proximo();
      if (prox) { const m = $('msg-final'); m.append(' · '); const n = document.createElement('a'); n.href = prox; n.textContent = 'Próximo desafio →'; m.append(n); }
    } catch { mensagem('Falha de conexão ao registrar o XP. Tente executar de novo.', 'erro'); }
    finally { enviando = false; }
  }
  function marcarResolvido() {
    resolvido = true; $('feito').classList.remove('hidden');
    const it = document.querySelector('.item-d[data-id="' + B.id + '"] .chk'); if (it) it.textContent = '✓';
  }
  function proximo() {
    const itens = [...document.querySelectorAll('.item-d')], i = itens.findIndex(a => a.dataset.id === B.id);
    const depois = itens.slice(i + 1).concat(itens.slice(0, i)).find(a => !a.querySelector('.chk').textContent);
    return depois ? depois.getAttribute('href') : null;
  }

  // ---------- Inicialização ----------
  $('arq').textContent = ARQ[B.lang];
  $('atalho-txt').textContent = ehPhp ? 'verificar' : 'executar';
  $('rodar').textContent = ehPhp ? '✔ Verificar código' : '▶ Executar e testar';
  ta.value = B.salvo ?? lerRascunho() ?? B.inicial;
  if (ehPhp) {
    runner.classList.add('hidden'); $('nota-php').classList.remove('hidden');
    mostrarTestes(testesDoDesafio(), false);
  } else {
    if (B.lang === 'js') { runner.classList.add('hidden'); consoleEl.classList.remove('hidden'); consoleEl.textContent = 'A saída do console.log aparece aqui.'; }
    mostrarTestes(B.testes.map(([desc]) => ({ desc })), false);
    runner.setAttribute('src', 'runner.html?v=' + Date.now());   // garante que o executor carregue depois dos listeners
  }
  atualizar();

  $('rodar').onclick = rodar;
  $('dica-btn').onclick = () => { const b = $('dica-box'); b.textContent = '💡 ' + B.dica; b.classList.remove('hidden'); };
  $('reset').onclick = () => {
    if (ta.value !== B.inicial && !confirm('Voltar ao código inicial? O que você escreveu será perdido.')) return;
    ta.value = B.inicial; atualizar(); salvarRascunho(); $('msg-final').classList.add('hidden');
    mostrarTestes(testesDoDesafio().map(([desc]) => ({ desc })), false); ta.focus();
  };
})();
