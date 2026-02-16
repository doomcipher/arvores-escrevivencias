<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../src/Models/Comentario.php';
require_once __DIR__ . '/../src/Services/ComentarioService.php';
require_once __DIR__ . '/../src/Controllers/ComentarioController.php';
require_once __DIR__ . '/../src/Helpers/Response.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$query  = $_GET ?? [];

$base = '';
if (strpos($uri, $base) === 0) {
    $uri = substr($uri, strlen($base));
}
$uri = preg_replace('#^/api#', '', $uri);

$controller = new ComentarioController();

try {

    if ($method === 'POST' && $uri === '/comentarios/criar') {

        $data = $_POST;
        $controller->criar($data);
    } elseif ($method === 'GET' && preg_match('#^/comentarios/post/(\d+)$#', $uri, $m)) {
        $controller->listarPorPost($m[1], $query);
    } elseif ($method === 'POST' && preg_match('#^/comentarios/aprovar/(\d+)$#', $uri, $m)) {
        $controller->aprovar($m[1]);
    } elseif ($method === 'DELETE' && preg_match('#^/comentarios/reprovar/(\d+)$#', $uri, $m)) {
        $controller->reprovar($m[1]);
    } elseif ($method === 'GET' && $uri === '/comentarios/pendentes') {
        $controller->listarPendentes();
    } else {
        Response::json(['erro' => 'Endpoint de comentários não encontrado', 'uri' => $uri], 404);
    }
} catch (Exception $e) {
    Response::json(['erro' => 'Erro no servidor: ' . $e->getMessage()], 500);
}
