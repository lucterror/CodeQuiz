// CodeQuiz - lógica do quiz (sorteio, opções embaralhadas, cronômetro, 50/50, atalhos, confete e salvamento)
const BOOT = JSON.parse(document.getElementById('quiz-boot').textContent);
const DADOS = BOOT.dados, CFG = BOOT.cfg || { por_rodada: 8, tempo: 20 }, TEMPO = CFG.tempo;
const $ = id => document.getElementById(id);
let lang, qs, i, acertos, xp, seq, resp, rest, timer, dicaUsada, usouDica, erros;
const show = s => { ['menu', 'jogo', 'fim'].forEach(x => $(x).classList.toggle('hidden', x !== s)); scrollTo({ top: 0 }); };

// Fisher-Yates (o sort aleatório é enviesado)
function embaralhar(a) {
  a = [...a];
  for (let k = a.length - 1; k > 0; k--) { const j = Math.floor(Math.random() * (k + 1)); [a[k], a[j]] = [a[j], a[k]]; }
  return a;
}

// Recordes por linguagem (localStorage pode estar indisponível)
const lerRecordes = () => { try { return JSON.parse(localStorage.getItem('cq_recordes')) || {}; } catch { return {}; } };
function gravarRecorde(id, pct, pts) {
  const r = lerRecordes(), a = r[id];
  const novo = !a || pct > a.pct || (pct === a.pct && pts > a.xp);
  if (novo) { r[id] = { pct, xp: pts }; try { localStorage.setItem('cq_recordes', JSON.stringify(r)); } catch {} }
  return novo && !!a;
}
function mostrarRecordes() {
  const r = lerRecordes();
  document.querySelectorAll('[data-melhor]').forEach(el => {
    const m = r[el.dataset.melhor];
    el.textContent = m ? '🏅 Melhor: ' + m.pct + '% · ' + m.xp + ' XP' : '';
  });
}

function iniciar(id) {
  lang = id;
  qs = embaralhar(DADOS[id][3]).slice(0, CFG.por_rodada);
  i = 0; acertos = 0; xp = 0; seq = 0; erros = []; dicaUsada = false;
  show('jogo'); render();
}

function render() {
  const [p, codigo, ops] = qs[i];
  resp = false; usouDica = false;
  $('perg').textContent = p;
  $('cod').textContent = codigo; $('cod').classList.toggle('hidden', !codigo);
  $('num').textContent = (i + 1) + '/' + qs.length;
  setProgresso(i);
  $('seq').textContent = seq; $('xp').textContent = xp;
  $('fb').className = 'fb'; $('fb').textContent = ''; $('prox').className = 'btn btn-p next';
  $('dica').disabled = dicaUsada;
  $('dica').classList.toggle('usada', dicaUsada);
  $('card').style.animation = 'none'; $('card').offsetHeight; $('card').style.animation = '';
  // embaralha as alternativas guardando qual é a correta
  const certa = qs[i][3];
  const ordem = embaralhar(ops.map((t, k) => ({ t, ok: k === certa })));
  qs[i].ordem = ordem;
  $('ops').innerHTML = '';
  ordem.forEach((o, n) => {
    const b = document.createElement('button');
    b.type = 'button'; b.className = 'opt'; b.innerHTML = '<b>' + 'ABCD'[n] + '</b>';
    const s = document.createElement('span'); s.textContent = o.t; b.append(s);
    b.onclick = () => responder(n);
    $('ops').append(b);
  });
  rest = TEMPO; tick(true); clearInterval(timer);
  timer = setInterval(() => { if (document.hidden) return; rest--; tick(); if (rest <= 0) responder(-1); }, 1000);
}

function setProgresso(feitas) {
  const v = Math.round(feitas / qs.length * 100);
  $('prog').style.width = v + '%'; $('barra').setAttribute('aria-valuenow', v);
}

function tick(reset) {
  $('tempo').textContent = '⏱ ' + Math.max(rest, 0) + 's';
  $('tempo').classList.toggle('alerta', rest <= 5);
  const t = $('tbar');
  if (reset) { t.style.transition = 'none'; t.style.width = '100%'; t.offsetHeight; t.style.transition = ''; }
  else t.style.width = Math.max(rest, 0) / TEMPO * 100 + '%';
  t.classList.toggle('alerta', rest <= 5);
}

function dica() {
  if (resp || dicaUsada) return;
  dicaUsada = usouDica = true;
  const bs = [...$('ops').children];
  const erradas = embaralhar(qs[i].ordem.map((o, n) => n).filter(n => !qs[i].ordem[n].ok)).slice(0, 2);
  erradas.forEach(n => { bs[n].disabled = true; bs[n].classList.add('riscada'); });
  $('dica').disabled = true; $('dica').classList.add('usada');
}

function responder(n) {
  if (resp) return;
  if (n >= 0 && $('ops').children[n].disabled) return;
  resp = true; clearInterval(timer);
  const ordem = qs[i].ordem, exp = qs[i][4], bs = $('ops').children;
  const certa = ordem.findIndex(o => o.ok), ok = n === certa;
  [...bs].forEach((b, k) => { b.disabled = true; if (k === certa) b.classList.add('ok'); });
  if (ok) {
    acertos++; seq++;
    let ganho = 10 + (seq - 1) * 5 + Math.floor(rest / 5);
    if (usouDica) ganho = Math.floor(ganho / 2);
    xp += ganho;
    $('fb').dataset.ganho = '+' + ganho + ' XP';
    if (seq >= 3) toast('🔥 Sequência de ' + seq + '!');
  } else {
    if (n >= 0) bs[n].classList.add('no');
    seq = 0; $('fb').dataset.ganho = '';
    erros.push({ p: qs[i][0], codigo: qs[i][1], certa: ordem[certa].t, exp });
  }
  $('seq').textContent = seq; $('xp').textContent = xp;
  setProgresso(i + 1);
  // textContent: as explicações citam tags (<h1>, <ol>...) que não podem virar HTML
  const fb = $('fb');
  fb.textContent = '';
  const st = document.createElement('strong');
  st.textContent = (ok ? 'Correto! ' : n < 0 ? 'Tempo esgotado! ' : 'Quase! ');
  fb.append(st, document.createTextNode(exp));
  if (ok) { const g = document.createElement('span'); g.className = 'ganho'; g.textContent = fb.dataset.ganho; fb.append(g); }
  fb.classList.add('show');
  $('dica').disabled = true;
  $('prox').textContent = i === qs.length - 1 ? 'Ver resultado' : 'Próxima';
  $('prox').classList.add('show'); $('prox').focus();
}
$('prox').onclick = () => { ++i < qs.length ? render() : final(); };

function revisao() {
  const box = $('rev'); box.innerHTML = '';
  box.classList.toggle('hidden', !erros.length);
  if (!erros.length) return;
  const h = document.createElement('h3'); h.textContent = 'Para revisar'; box.append(h);
  erros.forEach(e => {
    const d = document.createElement('div'); d.className = 'rev-item';
    const q = document.createElement('strong'); q.textContent = e.p; d.append(q);
    if (e.codigo) { const c = document.createElement('pre'); c.textContent = e.codigo; d.append(c); }
    const r = document.createElement('div'); r.className = 'rev-certa'; r.textContent = 'Resposta: ' + e.certa; d.append(r);
    const x = document.createElement('small'); x.textContent = e.exp; d.append(x);
    box.append(d);
  });
}

async function final() {
  const pct = Math.round(acertos / qs.length * 100);
  const [m, t] = pct === 100 ? ['🏆', 'Perfeito! Você domina ' + DADOS[lang][0]] : pct >= 75 ? ['🥇', 'Mandou muito bem!'] : pct >= 50 ? ['🥈', 'Bom começo, continue!'] : ['🌱', 'Todo mundo começa assim. Tente de novo!'];
  $('medal').textContent = m; $('pct').textContent = pct + '%'; $('msg').textContent = t;
  $('det').textContent = acertos + ' de ' + qs.length + ' acertos · ' + xp + ' XP ganhos';
  const bateu = gravarRecorde(lang, pct, xp);
  $('recorde').textContent = bateu ? '🎉 Novo recorde pessoal em ' + DADOS[lang][0] + '!' : '';
  revisao();
  $('salvo').innerHTML = '';
  show('fim'); if (pct >= 75) confete();
  if (!BOOT.logado) { $('salvo').innerHTML = '<a href="cadastro.php">Crie uma conta</a> para salvar seu XP e entrar no ranking.'; return; }
  $('salvo').textContent = 'Salvando resultado…';
  try {
    const r = await fetch('api/salvar.php', { method: 'POST', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ csrf: BOOT.csrf, linguagem: lang, acertos, total: qs.length, xp }) });
    const d = await r.json(); if (!d.ok) throw 0;
    $('salvo').innerHTML = 'Resultado salvo! Seu total é <strong>' + d.xp_total + ' XP</strong>. <a href="ranking.php">Ver ranking</a>';
    toast('+' + d.xp_ganho + ' XP salvos');
    document.querySelectorAll('[data-xp]').forEach(el => el.textContent = d.xp_total);
    document.querySelectorAll('[data-nv]').forEach(el => el.textContent = d.nivel);
    if (d.subiu) { toast('⬆ Nível ' + d.nivel + ': ' + d.titulo + '!'); confete(); }
  } catch { $('salvo').textContent = 'Não foi possível salvar o resultado.'; toast('Erro ao salvar', 'erro'); }
}

document.addEventListener('keydown', e => {
  if ($('jogo').classList.contains('hidden') || e.ctrlKey || e.metaKey || e.altKey) return;
  const k = e.key.toLowerCase();
  if (k === 'h') { dica(); return; }
  const n = '1234'.indexOf(k) >= 0 ? '1234'.indexOf(k) : 'abcd'.indexOf(k);
  if (k.length === 1 && n >= 0 && n < $('ops').children.length) responder(n);
  else if (e.key === 'Enter' && resp && document.activeElement !== $('prox')) $('prox').click();
});
document.querySelectorAll('.lang').forEach(b => b.onclick = () => iniciar(b.dataset.id));
$('dica').onclick = dica;
$('sair').onclick = () => { clearInterval(timer); mostrarRecordes(); show('menu'); };
$('outra').onclick = () => { mostrarRecordes(); show('menu'); };
$('de-novo').onclick = () => iniciar(lang);
mostrarRecordes();
