<?php

/**
 * Helper para autenticação
 * Verifica se usuário está logado, qual tipo, etc
 */
class Auth {

    public static function isLogado() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public static function isProfessor() {
        return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'professor';
    }

    public static function isAluno() {
        return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'aluno';
    }

    public static function getUsuarioId() {
        return $_SESSION['user_id'] ?? null;
    }

    public static function getTipo() {
        return $_SESSION['user_type'] ?? null;
    }

    public static function getMatricula() {
        return $_SESSION['user_matricula'] ?? null;
    }

    public static function getNome() {
        return $_SESSION['user_nome'] ?? null;
    }

    public static function logout() {
        session_destroy();
        header('Location: /');
        exit;
    }
}


?>
