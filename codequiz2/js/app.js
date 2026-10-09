// CodeQuiz - interações globais
(() => {
  const $ = (s, r = document) => r.querySelector(s), $$ = (s, r = document) => [...r.querySelectorAll(s)];
  const calmo = matchMedia('(prefers-reduced-motion: reduce)').matches;

  window.toast = (msg, tipo = 'ok') => {
    let box = $('#toasts') || document.body.appendChild(Object.assign(document.createElement('div'), { id: 'toasts', role: 'status' }));
    const t = Object.assign(document.createElement('div'), { className: 'toast ' + tipo, textContent: msg });
    box.append(t); setTimeout(() => t.classList.add('sai'), 3200); setTimeout(() => t.remove(), 3600);
  };

  window.confete = () => {
    if (calmo) return;
    const c = Object.assign(document.createElement('canvas'), { className: 'confete', width: innerWidth, height: innerHeight });
    document.body.append(c); const x = c.getContext('2d');
    const cores = ['#7c5cff', '#22d3ee', '#fbbf24', '#34d399', '#fb7185'];
    const ps = Array.from({ length: 140 }, () => ({ x: Math.random() * c.width, y: -20 - Math.random() * c.height * .4, vx: Math.random() * 4 - 2, vy: 3 + Math.random() * 4, s: 6 + Math.random() * 6, r: Math.random() * 6, c: cores[Math.random() * 5 | 0] }));
    const t0 = performance.now();
    (function f(t) {
      x.clearRect(0, 0, c.width, c.height);
      ps.forEach(p => { p.x += p.vx; p.y += p.vy; p.r += .12; x.save(); x.translate(p.x, p.y); x.rotate(p.r); x.fillStyle = p.c; x.fillRect(-p.s / 2, -p.s / 4, p.s, p.s / 2); x.restore(); });
      t - t0 < 3000 ? requestAnimationFrame(f) : c.remove();
    })(t0);
  };

  // Menu mobile
  const nav = $('.nav'), burger = $('.burger');
  burger?.addEventListener('click', () => burger.setAttribute('aria-expanded', nav.classList.toggle('open')));

  // Contadores animados
  const io = new IntersectionObserver(es => es.forEach(e => {
    if (!e.isIntersecting) return; io.unobserve(e.target);
    const el = e.target, to = +el.dataset.count, suf = el.dataset.suffix || '', t0 = performance.now();
    (function f(t) { const p = Math.min((t - t0) / 1100, 1); el.textContent = Math.round(to * (1 - (1 - p) ** 3)) + suf; if (p < 1) requestAnimationFrame(f); })(t0);
  }));
  $$('[data-count]').forEach(el => io.observe(el));

  // Mini-quiz da home
  $$('.demo-opt').forEach(b => b.addEventListener('click', () => {
    const ok = b.dataset.ok === '1';
    $$('.demo-opt').forEach(o => { o.disabled = true; if (o.dataset.ok === '1') o.classList.add('ok'); });
    if (!ok) b.classList.add('no'); else confete();
    $('#demo-fb').innerHTML = ok ? 'Correto! map devolve um novo array. <a href="quiz.php">Jogar o quiz completo</a>' : 'Quase! map aplica n * 2 a cada item. <a href="quiz.php">Tente o quiz</a>';
  }));

  // Cursos: copiar código, progresso de leitura e índice ativo
  $$('article .code').forEach(pre => {
    const b = Object.assign(document.createElement('button'), { className: 'copiar', type: 'button', textContent: 'Copiar' });
    b.onclick = () => navigator.clipboard.writeText(pre.textContent.replace(/Copiar$/, '')).then(() => { b.textContent = 'Copiado!'; toast('Código copiado'); setTimeout(() => b.textContent = 'Copiar', 1500); });
    pre.append(b);
  });
  if ($('.curso')) {
    const bar = document.body.appendChild(Object.assign(document.createElement('div'), { id: 'leitura' }));
    const links = $$('.toc a'), secs = links.map(a => $(a.getAttribute('href')));
    const upd = () => {
      bar.style.width = (scrollY / (document.documentElement.scrollHeight - innerHeight) * 100) + '%';
      let at = 0; secs.forEach((s, i) => { if (s.getBoundingClientRect().top < 140) at = i; });
      links.forEach((a, i) => a.classList.toggle('on', i === at));
    };
    addEventListener('scroll', upd, { passive: true }); upd();
  }

  // Senha: mostrar/ocultar, força e confirmação
  $$('.auth input[type=password]').forEach(inp => {
    const w = document.createElement('div'); w.className = 'pw'; inp.replaceWith(w); w.append(inp);
    const b = Object.assign(document.createElement('button'), { type: 'button', className: 'ver', textContent: 'Mostrar', ariaLabel: 'Mostrar senha' });
    b.onclick = () => { const s = inp.type === 'password'; inp.type = s ? 'text' : 'password'; b.textContent = s ? 'Ocultar' : 'Mostrar'; };
    w.append(b);
  });
  const nova = $('#senha[autocomplete=new-password]');
  if (nova) {
    const m = Object.assign(document.createElement('div'), { className: 'forca' }); m.innerHTML = '<i></i><span></span>';
    nova.closest('.pw').after(m);
    const conf = $('#confirmar'), niveis = ['Muito fraca', 'Fraca', 'Média', 'Boa', 'Forte'];
    nova.addEventListener('input', () => {
      const v = nova.value; const n = v ? Math.min(4, (v.length >= 8) + /[A-Z]/.test(v) + /\d/.test(v) + /[^\w]/.test(v) + (v.length >= 12) - 1 + (v.length < 8 ? 0 : 0)) : -1;
      m.dataset.n = n; m.lastChild.textContent = v ? niveis[Math.max(n, 0)] : '';
    });
    conf.addEventListener('input', () => conf.setCustomValidity(conf.value === nova.value ? '' : 'As senhas não coincidem'));
  }
})();
