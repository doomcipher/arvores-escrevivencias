<?php
// ============================================
// ROTEADOR CENTRAL DE APIs
// ============================================

// raiz do projeto (fora de public/)
if (!defined('BASE_PATH')) {
    define('BASE_PATH', realpath(__DIR__ . '/..'));
}

require_once BASE_PATH . '/src/Helpers/Response.php';
require_once BASE_PATH . '/src/Helpers/Auth.php';
require_once BASE_PATH . '/config/env.php';
require_once BASE_PATH . '/config/error_handler.php';

// se suas rotas usam sessão:
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove só o /api
$uri = preg_replace('#^/api#', '', $uri);

try {
    if (strpos($uri, '/auth') === 0) {
        require BASE_PATH . '/routes/auth.php';
    } elseif (strpos($uri, '/alunos') === 0) {
        require BASE_PATH . '/routes/alunos.php';
    } elseif (strpos($uri, '/posts') === 0) {
        require BASE_PATH . '/routes/posts.php';
    } elseif (strpos($uri, '/comentarios') === 0) {
        require BASE_PATH . '/routes/comentarios.php';
    } else {
        Response::json(['erro' => 'Endpoint não encontrado'], 404);
    }
} catch (Throwable $e) {
    Response::json([
        'erro' => 'Erro no servidor',
        'msg'  => $e->getMessage()
    ], 500);
}
