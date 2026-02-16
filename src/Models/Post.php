<?php
require_once __DIR__ . '/../../config/db.php';
class Post
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Criar novo post
     */
    public function create($id_professor, $titulo, $descricao, $link_midia, $tipo_postagem, $tema)
    {
        $sql = "INSERT INTO postagem 
            (id_professor, titulo, data, descricao, link_midia, tipo_postagem, tema)
            VALUES 
            (?, ?, NOW(), ?, ?, ?, ?)";
        //      ^   ^          ^   ^   ^   ^
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $id_professor,
            $titulo,
            $descricao,
            $link_midia,
            $tipo_postagem,
            $tema
        ]);

        return $this->db->lastInsertId();
    }


    /**
     * Obter post por ID
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM postagem WHERE id_postagem = ? LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    /**
     * Obter todos os posts com paginação
     */
    public function getAll()
    {
        $sql = "SELECT 
                p.id_postagem,
                p.id_professor,
                p.titulo,
                p.data,
                p.descricao,
                p.tipo_postagem,
                p.tema,
                p.link_midia,
                pr.nome AS nome_professor
            FROM postagem p
            JOIN professores pr ON pr.id_professor = p.id_professor
            ORDER BY p.data DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    /**
     * Obter posts por tema
     */
    public function getByTema($tema, $tipo_postagem = null, $limit = 10, $offset = 0)
    {
        $sql = "SELECT * FROM postagem WHERE tema = :tema";
        $params = [':tema' => $tema];

        if ($tipo_postagem) {
            $sql .= " AND tipo_postagem = :tipo";
            $params[':tipo'] = $tipo_postagem; // 'video' ou 'imagem'
        }

        $sql .= " ORDER BY data DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':tema', $tema, PDO::PARAM_STR);
        if ($tipo_postagem) {
            $stmt->bindValue(':tipo', $tipo_postagem, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Obter posts por professor
     */
    public function getByProfessor($id_professor, $limit = 10, $offset = 0)
    {
        $sql = "SELECT * FROM postagem 
                WHERE id_professor = ? 
                ORDER BY data DESC 
                LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_professor, $limit, $offset]);

        return $stmt->fetchAll();
    }

    /**
     * Atualizar post
     */
    public function update($id_postagem, $titulo, $descricao, $link_midia, $tipo_postagem, $tema)
    {
        $sql = "UPDATE postagem 
                SET titulo = ?, descricao = ?, link_midia = ?, tipo_postagem = ?, tema = ?
                WHERE id_postagem = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$titulo, $descricao, $link_midia, $tipo_postagem, $tema, $id_postagem]);
    }

    /**
     * Deletar post
     */
    public function delete($id_postagem)
    {
        $sql = "DELETE FROM postagem WHERE id_postagem = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id_postagem]);
    }

    /**
     * Contar total de posts
     */
    public function count()
    {
        $sql = "SELECT COUNT(*) as total FROM postagem";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }
}
