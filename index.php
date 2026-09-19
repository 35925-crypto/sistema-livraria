<?php

header('Content-Type: application/json; charset=utf-8');

$pastaArquivos = __DIR__ . DIRECTORY_SEPARATOR . 'arquivos';
$arquivoDados = $pastaArquivos . DIRECTORY_SEPARATOR . 'livros.json';

if (!is_dir($pastaArquivos)) {
    mkdir($pastaArquivos, 0777, true);
}

function lerLivros($arquivoDados) {
    if (!file_exists($arquivoDados)) {
        return [];
    }

    $conteudo = file_get_contents($arquivoDados);
    $livros = json_decode($conteudo, true);
    return is_array($livros) ? $livros : [];
}

function salvarLivros($arquivoDados, $livros) {
    file_put_contents(
        $arquivoDados,
        json_encode(array_values($livros), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    );
}

function responder($dados, $status = 200) {
    http_response_code($status);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit;
}

$metodo = $_SERVER['REQUEST_METHOD'];
$acao = $_GET['acao'] ?? '';

if ($acao === 'livros') {
    $livros = lerLivros($arquivoDados);

    if ($metodo === 'GET') {
        responder($livros);
    }

    $entrada = json_decode(file_get_contents('php://input'), true) ?? [];
    $titulo = trim($entrada['titulo'] ?? '');
    $autor = trim($entrada['autor'] ?? '');
    $ano = trim($entrada['ano'] ?? '');

    if ($metodo === 'POST') {
        if ($titulo === '' || $autor === '') {
            responder(['erro' => 'Informe o título e o autor.'], 422);
        }

        $livro = [
            'id' => uniqid(),
            'titulo' => $titulo,
            'autor' => $autor,
            'ano' => $ano,
            'status' => $entrada['status'] ?? 'Disponível'
        ];
        $livros[] = $livro;
        salvarLivros($arquivoDados, $livros);
        responder($livro, 201);
    }

    if ($metodo === 'PUT') {
        $id = $entrada['id'] ?? '';
        foreach ($livros as &$livro) {
            if ($livro['id'] === $id) {
                if ($titulo === '' || $autor === '') {
                    responder(['erro' => 'Informe o título e o autor.'], 422);
                }
                $livro['titulo'] = $titulo;
                $livro['autor'] = $autor;
                $livro['ano'] = $ano;
                $livro['status'] = $entrada['status'] ?? 'Disponível';
                salvarLivros($arquivoDados, $livros);
                responder($livro);
            }
        }
        responder(['erro' => 'Livro não encontrado.'], 404);
    }

    if ($metodo === 'DELETE') {
        $id = $entrada['id'] ?? '';
        $restantes = array_filter($livros, fn($livro) => $livro['id'] !== $id);
        if (count($restantes) === count($livros)) {
            responder(['erro' => 'Livro não encontrado.'], 404);
        }
        salvarLivros($arquivoDados, $restantes);
        responder(['mensagem' => 'Livro excluído com sucesso.']);
    }

    responder(['erro' => 'Método não permitido.'], 405);
}

header('Content-Type: text/html; charset=utf-8');
readfile(__DIR__ . DIRECTORY_SEPARATOR . 'frontend' . DIRECTORY_SEPARATOR . 'index.html');
