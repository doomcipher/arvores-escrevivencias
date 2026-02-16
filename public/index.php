<?php

// Caminho base da aplicação (raiz do projeto, fora de public/)
if (!defined('BASE_PATH')) {
    define('BASE_PATH', realpath(__DIR__ . '/..'));
}

// ============================================
// PORTEIRO PRINCIPAL - FRONT CONTROLLER
// ============================================

// ✅ INICIAR SESSÃO UMA VEZ SÓ
session_start();

// Carregar configurações e helpers (sempre a partir da raiz do projeto)
require_once BASE_PATH . '/config/env.php';
// require_once BASE_PATH . '/config/db.php'; // se ainda precisar
require_once BASE_PATH . '/src/Helpers/Auth.php';
require_once BASE_PATH . '/src/Helpers/Response.php';
require_once BASE_PATH . '/src/Helpers/Validator.php';
require_once BASE_PATH . '/config/error_handler.php';

// ============================================
// PASSO 1: Detectar tipo de requisição
// ============================================

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Remove o prefixo do projeto (se tiver)
$base_path = '/';
if (strpos($uri, $base_path) === 0) {
    $uri = substr($uri, strlen($base_path));
}

// Se ficar vazio, é a raiz
if (empty($uri)) {
    $uri = '/';
} else {
    // Adiciona a barra de volta pra ficar /auth/me
    $uri = '/' . trim($uri, '/');
}

// ============================================
// PASSO 2: API requests (retornam JSON)
// ============================================

if (strpos($uri, '/api/') === 0) {
    header('Content-Type: application/json; charset=utf-8');
    require __DIR__ . '/api.php'; // api.php está em public/
    exit;
}

// ============================================
// PASSO 3: Arquivos HTML estáticos
// ============================================

// Se termina com .html, servir direto da pasta public/
if (substr($uri, -5) === '.html') {
    $file = __DIR__ . $uri;
    if (is_file($file)) {
        readfile($file);
        exit;
    }
}

// ============================================
// PASSO 4: Raiz (/)
// ============================================

if ($uri === '/') {
    readfile(__DIR__ . '/index.html');
    exit;
}

// ============================================
// PASSO 5: Páginas que precisam autenticação
// ============================================

$protected_pages = [
    '/admin',
    '/admin.html',
    '/painel',
    '/dashboard',
];

if (in_array($uri, $protected_pages)) {
    if (!Auth::isLogado()) {
        header('Location: /');
        exit;
    }

    readfile(__DIR__ . '/admin.html');
    exit;
}

// ============================================
// PASSO 6: Assets (CSS, JS, Imagens)
// ============================================

if (is_file(__DIR__ . $uri)) {
    $file = __DIR__ . $uri;

    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $types = [
        'js' => 'application/javascript',
        'css' => 'text/css',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'svg' => 'image/svg+xml',
        'webp' => 'image/webp',
    ];
    if (isset($types[$ext])) header('Content-Type: ' . $types[$ext]);

    readfile($file);
    exit;
}

// ============================================
// PASSO 7: 404
// ============================================

http_response_code(404);
echo "
<!DOCTYPE html>
<html>
<head>
    <title>404 - Página não encontrada</title>
    <style>
        body { font-family: Arial; text-align: center; padding: 50px; }
        h1 { color: #e74c3c; }
    </style>
</head>
<body>
    <h1>404 - Página não encontrada</h1>
    <p>A página que você procura não existe.</p>
    <a href='/'>Voltar</a>
</body>
</html>
";
