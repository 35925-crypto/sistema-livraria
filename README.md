# Minha Livraria

Projeto simples para gerenciar livros de uma livraria. Possui frontend, backend em PHP e um arquivo JSON usado como banco de dados.

## Como executar

No terminal, dentro da pasta do projeto:

```powershell
php -S localhost:8000
```

Depois, acesse `http://localhost:8000` no navegador.

## Funcionalidades

- Cadastrar livro
- Listar e pesquisar livros
- Editar livro
- Excluir livro
- Marcar como disponível ou emprestado

## Divisão sugerida para 4 pessoas

- `main`: versão final integrada
- `frontend`: tela, estilos e pesquisa
- `backend`: rotas PHP e validações
- `banco-dados`: arquivo JSON e persistência
- `arquivos`: README, `.gitignore` e documentação
