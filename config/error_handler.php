<?php
// ============================================
// ERROR HANDLER - Sistema de Log (robusto)
// ============================================

define('LOG_FILE', __DIR__ . '/../logs/system.log');

function log_error(string $msg): void {
    $time  = date('Y-m-d H:i:s');
    $entry = "[$time] $msg" . PHP_EOL;

    $dir = dirname(LOG_FILE);

    // garante pasta (sem quebrar caso falhe)
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }

    // tenta escrever (sem warning na saída)
    @file_put_contents(LOG_FILE, $entry, FILE_APPEND);

    // rotação simples: mantém só os últimos 300 registros
    $lines = @file(LOG_FILE, FILE_IGNORE_NEW_LINES);
    if (is_array($lines) && count($lines) > 300) {
        $last300 = array_slice($lines, -300);
        @file_put_contents(LOG_FILE, implode(PHP_EOL, $last300) . PHP_EOL);
    }
}

// ============================================
// CAPTURA DE ERROS PADRÃO
// ============================================
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    log_error("Erro PHP: $errstr | Arquivo: $errfile | Linha: $errline");
    return false; // deixa o PHP seguir o fluxo padrão também (opcional)
});

// ============================================
// CAPTURA EXCEÇÕES (try/catch)
// ============================================
set_exception_handler(function($exception) {
    log_error(
        "Exceção: " . $exception->getMessage() .
        " | Arquivo: " . $exception->getFile() .
        " | Linha: " . $exception->getLine()
    );
});

// ============================================
// LOG DE FATAL ERRORS
// ============================================
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null) {
        log_error("Erro Fatal: {$error['message']} | Arquivo: {$error['file']} | Linha: {$error['line']}");
    }
});
