<?php

require_once __DIR__ . '/../../config/db.php';

class Comentario
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    // criar comentário (pendente)
    public function create($id_postagem, $id_alunos, $comentarios)
    {
        $sql = "INSERT INTO comentarios
                (id_postagem, id_alunos, comentarios, data, confirmador)
                VALUES
                (:id_postagem, :id_alunos, :comentarios, NOW(), 0)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_postagem', $id_postagem, PDO::PARAM_INT);
        $stmt->bindValue(':id_alunos',   $id_alunos,   PDO::PARAM_INT);
        $stmt->bindValue(':comentarios', $comentarios, PDO::PARAM_STR);
        $stmt->execute();

        return $this->db->lastInsertId();
    }

    // listar comentários de um post (opção de só aprovados)
    public function getByPost($id_postagem, $somente_aprovados = true)
    {
        $sql = "SELECT
                c.id_comentarios,
                c.id_postagem,
                c.id_alunos,
                c.comentarios,
                c.data,
                c.confirmador,
                c.slide_index,
                a.nome      AS nome,
                a.matricula AS matricula
            FROM comentarios c
            JOIN alunos a ON a.id_alunos = c.id_alunos
            WHERE c.id_postagem = :id_postagem";

        if ($somente_aprovados) {
            $sql .= " AND c.confirmador = 1";
        }

        $sql .= " ORDER BY c.data ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_postagem', $id_postagem, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAlunoIdByMatricula($matricula)
    {
        $sql = "SELECT id_alunos FROM alunos WHERE matricula = :matricula LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':matricula', $matricula, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['id_alunos'] ?? null;
    }

    // aprovar comentário
    public function aprovar($id_comentarios)
    {
        $sql = "UPDATE comentarios SET confirmador = 1 WHERE id_comentarios = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id_comentarios, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // excluir (para reprovar)
    public function delete($id_comentarios)
    {
        $sql = "DELETE FROM comentarios WHERE id_comentarios = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id_comentarios, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getPendentes()
    {
        $sql = "SELECT
                c.id_comentarios,
                c.id_postagem,
                c.id_alunos,
                c.comentarios,
                c.data,
                c.confirmador,
                p.titulo AS titulo_post,
                a.nome  AS nome_aluno
            FROM comentarios c
            JOIN postagem p ON p.id_postagem = c.id_postagem
            JOIN alunos   a ON a.id_alunos   = c.id_alunos
            WHERE c.confirmador = 0
            ORDER BY c.data ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
