<?php
require __DIR__ . '/env.php';

// ============================================
// Classe estática para conexão PDO
// ============================================

class Database
{
    // Variável estática: existe só uma por vez pra toda a aplicação
    private static $connection = null;

    // Método estático: você chama como Database::connect()
    public static function connect()
    {
        // Se já conectou antes, não conecta de novo
        if (self::$connection === null) {
            try {
                // Criar a conexão PDO
                self::$connection = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                    DB_USER,
                    DB_PASS,
                    [
                        // Se der erro, lança exception (não fica silencioso)
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        // Retorna dados como array associativo
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        // Timeout de 10 segundos
                        PDO::ATTR_TIMEOUT => 10
                    ]
                );
            } catch (PDOException $e) {
                // Se falhar, die (para a execução)
                die("❌ Erro ao conectar no banco: " . $e->getMessage());
            }
        }

        return self::$connection;
    }
}
