<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../src/Models/Post.php';
require_once __DIR__ . '/../src/Services/PostService.php';
require_once __DIR__ . '/../src/Controllers/PostController.php';
require_once __DIR__ . '/../src/Helpers/Response.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$query  = $_GET ?? [];

error_log("POSTS DEBUG: METHOD = $method");
error_log("POSTS DEBUG: URI = '$uri'");

$base = '';
if (strpos($uri, $base) === 0) {
    $uri = substr($uri, strlen($base));
}
$uri = preg_replace('#^/api#', '', $uri);

$controller = new PostController();
error_log("POSTS DEBUG: METHOD = $method");
error_log("POSTS DEBUG: URI = '$uri'");

try {
    if ($method === 'POST' && $uri === '/posts/criar') {
    $data  = $_POST;
    $files = $_FILES;
    $controller->criar($data, $files);
    } elseif ($method === 'GET' && $uri === '/posts/listar') {
        $controller->listar($query);
    } elseif ($method === 'GET' && preg_match('#^/posts/get/(\d+)$#', $uri, $m)) {
        $controller->obter($m[1]);
    } elseif ($method === 'PUT' && preg_match('#^/posts/update/(\d+)$#', $uri, $m)) {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $controller->atualizar($m[1], $data);
    } elseif ($method === 'DELETE' && preg_match('#^/posts/delete/(\d+)$#', $uri, $m)) {
        $controller->deletar($m[1]);
    } elseif ($method === 'GET' && preg_match('#^/posts/professor/(\d+)$#', $uri, $m)) {
        $controller->obterPorProfessor($m[1], $query);
    } elseif ($method === 'GET' && preg_match('#^/posts/tema/(.+)$#', $uri, $m)) {
        $controller->obterPorTema($m[1], $query);
    } else {
        Response::json(['erro' => 'Endpoint de posts não encontrado', 'uri' => $uri], 404);
    }
} catch (Exception $e) {
    Response::json(['erro' => 'Erro no servidor: ' . $e->getMessage()], 500);
}
