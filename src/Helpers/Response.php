<?php

/**
 * Helper para formatar respostas JSON
 * Padroniza todas as respostas da API
 */

class Response {

    /**
     * Enviar resposta JSON padronizada
     *
     * @param mixed $data   Dados a enviar
     * @param int   $status Código HTTP
     */
    public static function json($data, int $status = 200): void {

        // Se algum output escapou antes, limpa (evita JSON quebrado)
        if (ob_get_length()) {
            ob_clean();
        }

        // Só envia headers se ainda der
        if (!headers_sent()) {
            http_response_code($status);
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Pragma: no-cache');
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Resposta de sucesso padronizada
     */
    public static function sucesso(string $mensagem, $data = null, int $status = 200): void {
        self::json([
            'status' => 'ok',
            'msg'    => $mensagem,
            'data'   => $data
        ], $status);
    }

    /**
     * Resposta de erro padronizada
     */
    public static function erro(string $mensagem, int $status = 400): void {
        self::json([
            'status' => 'erro',
            'msg'    => $mensagem
        ], $status);
    }
}

?>
