// CodeQuiz - executor isolado dos desafios (roda dentro de runner.html, em iframe com sandbox sem allow-same-origin).
// Recebe {cq:1, tipo, codigo, html, testes, exporta} e devolve {cq:1, tipo:'resultado', saida, erro, testes}.
(() => {
  const resp = o => parent.postMessage(Object.assign({ cq: 1 }, o), '*');
  const msg = e => String((e && e.message) || e);

  // Executa uma lista de testes [descrição, expressão] com os helpers informados.
  const avaliar = (testes, nomes, valores) => testes.map(([desc, expr]) => {
    try { return { desc, ok: !!new Function(...nomes, 'return (' + expr + ');')(...valores) }; }
    catch (e) { return { desc, ok: false, erro: msg(e) }; }
  });

  // ---------- HTML e CSS: monta o resultado na própria página do executor (já isolada) e consulta o DOM ----------
  // innerHTML não executa <script>, então o HTML do usuário não roda código aqui.
  function rodarDom(job) {
    const alvo = document.getElementById('alvo');
    document.querySelectorAll('style[data-user]').forEach(s => s.remove());
    alvo.innerHTML = job.tipo === 'css' ? job.html : job.codigo;
    if (job.tipo === 'css') {
      const st = document.createElement('style'); st.dataset.user = '1'; st.textContent = job.codigo; document.head.append(st);
    }
    const doc = document, win = window;
    const q = s => alvo.querySelector(s), qa = s => [...alvo.querySelectorAll(s)];
    const txt = s => ((q(s) || {}).textContent || '').trim();
    const cs = s => { const el = q(s); if (!el) throw new Error('Elemento ' + s + ' não encontrado'); return win.getComputedStyle(el); };
    const regras = re => [...doc.styleSheets].flatMap(sh => { try { return [...sh.cssRules]; } catch { return []; } })
      .filter(r => r.selectorText && re.test(r.selectorText));
    return { saida: [], erro: null, testes: avaliar(job.testes, ['q', 'qa', 'txt', 'cs', 'regras', 'codigo', 'doc', 'win'], [q, qa, txt, cs, regras, job.codigo, doc, win]) };
  }

  // ---------- JavaScript: roda em Worker com limite de tempo (laço infinito não trava a página) ----------
  const SRC_WORKER = `
    const saida = [];
    const fmt = a => { if (typeof a === 'string') return a; try { return (typeof a === 'object' && a !== null) ? JSON.stringify(a) : String(a); } catch { return String(a); } };
    const con = {}; ['log', 'info', 'warn', 'error'].forEach(k => con[k] = (...a) => { saida.push(a.map(fmt).join(' ')); });
    onmessage = e => {
      const { codigo, exporta, testes } = e.data; let erro = null, f = {};
      try {
        const corpo = codigo + '\\n;return {' + exporta.map(n => n + ': typeof ' + n + ' === "undefined" ? undefined : ' + n).join(',') + '};';
        f = new Function('console', corpo)(con) || {};
      } catch (err) { erro = String((err && err.message) || err); }
      const res = testes.map(([desc, expr]) => {
        try { return { desc, ok: !!new Function('saida', 'f', 'codigo', 'erro', 'return (' + expr + ');')(saida, f, codigo, erro) }; }
        catch (err) { return { desc, ok: false, erro: String((err && err.message) || err) }; }
      });
      postMessage({ saida, erro, testes: res });
    };`;

  function rodarJs(job) {
    const dados = { codigo: job.codigo, exporta: job.exporta || [], testes: job.testes };
    return new Promise(ok => {
      let w, url;
      const falhou = erro => ok({ saida: [], erro, testes: job.testes.map(([desc]) => ({ desc, ok: false })) });
      try { url = URL.createObjectURL(new Blob([SRC_WORKER], { type: 'text/javascript' })); w = new Worker(url); }
      catch { return ok(rodarJsDireto(dados)); }   // sem suporte a Worker: roda aqui mesmo
      const t = setTimeout(() => { w.terminate(); URL.revokeObjectURL(url); falhou('Tempo esgotado: o código demorou demais (laço infinito?).'); }, 2500);
      w.onmessage = e => { clearTimeout(t); w.terminate(); URL.revokeObjectURL(url); ok(e.data); };
      w.onerror = e => { clearTimeout(t); w.terminate(); URL.revokeObjectURL(url); falhou(e.message || 'Erro ao executar o código.'); };
      w.postMessage(dados);
    });
  }
  function rodarJsDireto({ codigo, exporta, testes }) {
    const saida = [], con = { log: (...a) => saida.push(a.join(' ')) }; con.info = con.warn = con.error = con.log;
    let erro = null, f = {};
    try { f = new Function('console', codigo + '\n;return {' + exporta.map(n => n + ': typeof ' + n + ' === "undefined" ? undefined : ' + n).join(',') + '};')(con) || {}; }
    catch (e) { erro = msg(e); }
    return { saida, erro, testes: avaliar(testes, ['saida', 'f', 'codigo', 'erro'], [saida, f, codigo, erro]) };
  }

  addEventListener('message', async e => {
    const job = e.data;
    if (!job || job.cq !== 1 || e.source !== parent) return;
    let r;
    try { r = job.tipo === 'js' ? await rodarJs(job) : rodarDom(job); }
    catch (err) { r = { saida: [], erro: msg(err), testes: job.testes.map(([desc]) => ({ desc, ok: false })) }; }
    resp(Object.assign({ tipo: 'resultado', id: job.id }, r));
  });
  resp({ tipo: 'pronto' });
})();
