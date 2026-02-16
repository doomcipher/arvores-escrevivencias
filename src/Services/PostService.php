<?php

class PostService {
    private $postModel;

    public function __construct() {
        $this->postModel = new Post();
    }
    /**
     * Criar novo post (sem mídia; mídia será tratada no controller)
     */
    public function criar($data) {
        // Validar campos obrigatórios
        if (empty($data['titulo']) || empty($data['descricao'])) {
            throw new Exception('Título e descrição são obrigatórios');
        }

        if (empty($data['tipo_postagem']) || empty($data['tema'])) {
            throw new Exception('Tipo de postagem e tema são obrigatórios');
        }

        // Verificar se é professor
        if (!Auth::isProfessor()) {
            throw new Exception('Apenas professores podem criar posts');
        }

        $id_professor = Auth::getUsuarioId();

        // Validar tipo de postagem
        $tipos_validos = ['video', 'imagem'];
        if (!in_array($data['tipo_postagem'], $tipos_validos)) {
            throw new Exception('Tipo de postagem inválido');
        }

        // Aqui NÃO validamos link_midia nem URL, porque
        // o upload e as URLs serão tratados no PostController
        // depois de termos o id do post.

        // Criar o post inicial com link_midia vazio
        $id = $this->postModel->create(
            $id_professor,
            trim($data['titulo']),
            trim($data['descricao']),
            '', // será atualizado com JSON de URLs após o upload
            $data['tipo_postagem'],
            $data['tema']
        );

        // Retorna os dados básicos; o controller depois faz o upload,
        // monta o JSON com as URLs e dá update nesse post.
        return [
            'id'            => $id,
            'titulo'        => trim($data['titulo']),
            'descricao'     => trim($data['descricao']),
            'link_midia'    => '',
            'tipo_postagem' => $data['tipo_postagem'],
            'tema'          => $data['tema']
        ];
    }



    /**
     * Listar todos os posts com paginação
     */
    public function listar($pagina = 1, $por_pagina = 10) {
        $pagina = max(1, intval($pagina));
        $offset = ($pagina - 1) * $por_pagina;

        return $this->postModel->getAll($por_pagina, $offset);
    }

    /**
     * Obter um post específico
     */
    public function obter($id) {
        if (!is_numeric($id)) {
            throw new Exception('ID deve ser um número');
        }

        $post = $this->postModel->getById($id);

        if (!$post) {
            throw new Exception('Post não encontrado');
        }

        return $post;
    }

    /**
     * Atualizar post (só o dono pode)
     */
    public function atualizar($id_postagem, $data) {
        // Verificar se é professor
        if (!Auth::isProfessor()) {
            throw new Exception('Apenas professores podem editar posts');
        }

        // Obter o post
        $post = $this->postModel->getById($id_postagem);
        if (!$post) {
            throw new Exception('Post não encontrado');
        }

        // Verificar se é o dono
        if ($post['id_professor'] != Auth::getUsuarioId()) {
            throw new Exception('Você não pode editar este post');
        }

        // Validar dados
        if (empty($data['titulo']) || empty($data['descricao'])) {
            throw new Exception('Título e descrição são obrigatórios');
        }

        return $this->postModel->update(
            $id_postagem,
            trim($data['titulo']),
            trim($data['descricao']),
            $data['link_midia'] ?? $post['link_midia'],
            $data['tipo_postagem'] ?? $post['tipo_postagem'],
            $data['tema'] ?? $post['tema']
        );
    }

    /**
     * Deletar post (só o dono pode)
     */
    public function deletar($id_postagem) {
        // Verificar se é professor
        if (!Auth::isProfessor()) {
            throw new Exception('Apenas professores podem deletar posts');
        }

        $post = $this->postModel->getById($id_postagem);
        if (!$post) {
            throw new Exception('Post não encontrado');
        }

        if ($post['id_professor'] != Auth::getUsuarioId()) {
            throw new Exception('Você não pode deletar este post');
        }

        return $this->postModel->delete($id_postagem);
    }

    /**
     * Obter posts de um professor específico
     */
    public function obterPorProfessor($id_professor, $pagina = 1, $por_pagina = 10) {
        $pagina = max(1, intval($pagina));
        $offset = ($pagina - 1) * $por_pagina;

        return $this->postModel->getByProfessor($id_professor, $por_pagina, $offset);
    }

    /**
     * Obter posts de um tema específico
     */
    public function obterPorTema($tema, $filtros = [], $pagina = 1, $por_pagina = 10) {
    $pagina = max(1, intval($pagina));
    $offset = ($pagina - 1) * $por_pagina;

    $tipo = $filtros['tipo_postagem'] ?? null;

    return $this->postModel->getByTema($tema, $tipo, $por_pagina, $offset);
    }

}

?>
