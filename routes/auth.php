<?php
header('Content-Type: application/json; charset=utf-8');
// ============================================
// AUTH ROUTER - SUAP OAuth (ÚNICO)
// ============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/Helpers/Response.php';

error_log("AUTH DEBUG: REQUEST_URI = " . $_SERVER['REQUEST_URI']);
error_log("AUTH DEBUG: METHOD = " . $_SERVER['REQUEST_METHOD']);


// ✅ CONNEXÃO PDO CENTRALIZADA
$pdo = Database::connect();

// Obter URI sem /api
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remover prefixo do projeto e /api
$base = '';
if (strpos($uri, $base) === 0) {
    $uri = substr($uri, strlen($base));
}
$uri = preg_replace('#^/api#', '', $uri);

// ============================================
// ROTA: POST /api/auth/callback
// Recebe dados do JavaScript (SUAP Client)
// ============================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($uri, '/auth/callback') === 0) {
    error_log("AUTH DEBUG: ENTROU NO CALLBACK");

    $input = file_get_contents('php://input');
    $data  = json_decode($input, true);

    $matricula = $data['matricula'] ?? null;
    $nome      = $data['nome']      ?? null;

    if (!$matricula || !$nome) {
        Response::json(['status' => 'erro', 'msg' => 'Dados incompletos'], 400);
    }

    try {
        // 1) tenta como PROFESSOR
        $stmt = $pdo->prepare("SELECT id_professor AS id FROM professores WHERE matricula = ? LIMIT 1");
        $stmt->execute([$matricula]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $_SESSION['user_id']        = $row['id'];
            $_SESSION['user_type']      = 'professor';
            $_SESSION['user_matricula'] = $matricula;
            $_SESSION['user_nome']      = $nome;

            Response::json([
                'status'    => 'ok',
                'tipo'      => 'professor',
                'id'        => $row['id'],
                'matricula' => $matricula,
                'nome'      => $nome
            ], 200);
        }

        // 2) se não achou professor, tenta como ALUNO
        $stmt = $pdo->prepare("SELECT id_alunos AS id FROM alunos WHERE matricula = ? LIMIT 1");
        $stmt->execute([$matricula]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $_SESSION['user_id']        = $row['id'];
            $_SESSION['user_type']      = 'aluno';
            $_SESSION['user_matricula'] = $matricula;
            $_SESSION['user_nome']      = $nome;

            Response::json([
                'status'    => 'ok',
                'tipo'      => 'aluno',
                'id'        => $row['id'],
                'matricula' => $matricula,
                'nome'      => $nome
            ], 200);
        }


        // 3) não está nem em alunos nem em professores
        Response::json([
            'status' => 'erro',
            'msg'    => 'Matrícula não encontrada no sistema'
        ], 404);
    } catch (PDOException $e) {
        Response::json(['status' => 'erro', 'msg' => 'Erro no servidor'], 500);
    }
}


// ============================================
// ROTA: POST /api/auth/logout
// Destrói a sessão
// ============================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($uri, '/auth/logout') === 0) {
    session_destroy();
    Response::json(['status' => 'ok', 'msg' => 'Logout realizado com sucesso'], 200);
    exit;
}

// ============================================
// ROTA: GET /api/auth/me
// Retorna dados do usuário logado
// ============================================

if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($uri, '/auth/me') === 0) {
    if (!isset($_SESSION['user_id'])) {
        Response::json(['status' => 'erro', 'msg' => 'Não está logado'], 401);
        exit;
    }

    Response::json([
        'status' => 'ok',
        'data' => [
            'id' => $_SESSION['user_id'],
            'matricula' => $_SESSION['user_matricula'],
            'nome' => $_SESSION['user_nome'],
            'type' => $_SESSION['user_type']
        ]
    ], 200);
    exit;
}

// ============================================
// Rota não encontrada
// ============================================

Response::json(['erro' => 'Endpoint de auth não encontrado'], 404);
