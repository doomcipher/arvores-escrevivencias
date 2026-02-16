<?php

class Aluno {
    private $db;

    public function __construct() {
        // Usa a conexão já pronta do config/database.php
        $this->db = Database::connect();
    }

    /**
     * Encontrar aluno por matrícula
     * @param string $matricula
     * @return array|false
     */
    public function findByMatricula($matricula) {
        $sql = "SELECT id_alunos, matricula, nome
                FROM alunos
                WHERE matricula = ?
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$matricula]);
        
        return $stmt->fetch();
    }

    /**
     * Criar novo aluno
     * @param string $matricula
     * @param string $nome
     * @return int ID do novo aluno
     */
    public function create($matricula, $nome) {
        $sql = "INSERT INTO alunos (matricula, nome)
                VALUES (?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$matricula, $nome]);
        
        return $this->db->lastInsertId();
    }
    
        /**
     * Obter aluno por ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $sql = "SELECT id_alunos, matricula, nome
                FROM alunos
                WHERE id_alunos = ?
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch();
    }

}

?>
