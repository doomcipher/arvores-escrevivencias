<?php

require __DIR__ . '/../src/Models/Aluno.php';
require __DIR__ . '/../src/Services/AlunoService.php';
require __DIR__ . '/../src/Controllers/AlunoController.php';
require_once __DIR__ . '/../src/Helpers/Response.php';
require __DIR__ . '/../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remover prefixo do projeto
$base = '';
if (strpos($uri, $base) === 0) {
    $uri = substr($uri, strlen($base)); // agora /api/alunos/criar
}

// NÃO removemos /api aqui, porque as comparações abaixo usam /api/...
error_log("ALUNOS DEBUG URI = " . $uri);

$controller = new AlunoController();

try {
    // POST /api/alunos/criar
    if ($method === 'POST' && $uri === '/api/alunos/criar') {
        $input = file_get_contents('php://input');
        $data  = json_decode($input, true);
        $controller->criar($data);
        exit;
    }

    // GET /api/alunos/get/:id
    else if ($method === 'GET' && preg_match('#^/api/alunos/get/(\d+)$#', $uri, $matches)) {
        $id = $matches[1];
        $controller->obter($id);
        exit;
    }

    else {
        Response::json(['erro' => 'Endpoint não encontrado'], 404);
    }

} catch (Exception $e) {
    Response::json(['erro' => 'Erro no servidor: ' . $e->getMessage()], 500);
}

