<?php

/**
 * Helper para registrar erros e eventos
 */
class Logger {
    
    private static $logFile = __DIR__ . '/../../logs/system.log';

    /**
     * Registrar erro
     */
    public static function error($mensagem, $exception = null) {
        $timestamp = date('Y-m-d H:i:s');
        $log = "[$timestamp] ERROR: {$mensagem}";
        
        if ($exception) {
            $log .= " - " . $exception->getMessage();
        }
        
        $log .= "\n";
        
        error_log($log, 3, self::$logFile);
    }

    /**
     * Registrar info
     */
    public static function info($mensagem) {
        $timestamp = date('Y-m-d H:i:s');
        $log = "[$timestamp] INFO: {$mensagem}\n";
        error_log($log, 3, self::$logFile);
    }

    /**
     * Registrar warning
     */
    public static function warning($mensagem) {
        $timestamp = date('Y-m-d H:i:s');
        $log = "[$timestamp] WARNING: {$mensagem}\n";
        error_log($log, 3, self::$logFile);
    }
}

?>
