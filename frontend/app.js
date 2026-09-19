const api = '?acao=livros';
let livros = [];
const $ = (seletor) => document.querySelector(seletor);

function mostrarMensagem(texto = '', erro = true) {
  const mensagem = $('#mensagem');
  mensagem.textContent = texto;
  mensagem.style.color = erro ? '#b42318' : '#147a3d';
}

async function carregarLivros() {
  const resposta = await fetch(api);
  livros = await resposta.json();
  renderizar();
}

function renderizar() {
  const termo = $('#busca').value.toLowerCase();
  const filtrados = livros.filter(l => `${l.titulo} ${l.autor}`.toLowerCase().includes(termo));
  const lista = $('#lista-livros');
  if (!filtrados.length) {
    lista.innerHTML = '<p>Nenhum livro encontrado.</p>';
    return;
  }
  lista.innerHTML = filtrados.map(l => `
    <article class="livro">
      <div><h3>${escapar(l.titulo)} <span class="tag ${l.status === 'Emprestado' ? 'emprestado' : ''}">${escapar(l.status)}</span></h3>
      <p>${escapar(l.autor)}${l.ano ? ` · ${escapar(l.ano)}` : ''}</p></div>
      <div class="botoes"><button onclick="editar('${l.id}')">Editar</button><button class="perigo" onclick="excluir('${l.id}')">Excluir</button></div>
    </article>`).join('');
}

function escapar(valor) { const el = document.createElement('span'); el.textContent = valor || ''; return el.innerHTML; }

$('#form-livro').addEventListener('submit', async (evento) => {
  evento.preventDefault();
  const id = $('#id').value;
  const dados = { id, titulo: $('#titulo').value, autor: $('#autor').value, ano: $('#ano').value, status: $('#status').value };
  const resposta = await fetch(api, { method: id ? 'PUT' : 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(dados) });
  const retorno = await resposta.json();
  if (!resposta.ok) return mostrarMensagem(retorno.erro);
  limparFormulario(); mostrarMensagem(id ? 'Livro atualizado!' : 'Livro cadastrado!', false); carregarLivros();
});

function editar(id) { const l = livros.find(livro => livro.id === id); $('#id').value = l.id; $('#titulo').value = l.titulo; $('#autor').value = l.autor; $('#ano').value = l.ano; $('#status').value = l.status; $('#botao-salvar').textContent = 'Atualizar livro'; $('#cancelar').hidden = false; window.scrollTo({ top: 0, behavior: 'smooth' }); }
async function excluir(id) { if (!confirm('Deseja excluir este livro?')) return; await fetch(api, { method: 'DELETE', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id }) }); mostrarMensagem('Livro excluído!', false); carregarLivros(); }
function limparFormulario() { $('#form-livro').reset(); $('#id').value = ''; $('#botao-salvar').textContent = 'Salvar livro'; $('#cancelar').hidden = true; }
$('#cancelar').addEventListener('click', limparFormulario);
$('#busca').addEventListener('input', renderizar);
carregarLivros().catch(() => mostrarMensagem('Não foi possível carregar os livros.'));
