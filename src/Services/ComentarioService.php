<?php

class ComentarioService
{
    private $comentarioModel;

    public function __construct()
    {
        $this->comentarioModel = new Comentario();
    }

    // Criar comentário (aluno)
    public function criar($data)
    {
        // Permitir aluno OU professor comentar
        if (!Auth::isAluno() && !Auth::isProfessor()) {
            throw new Exception('Apenas usuários autenticados podem comentar');
        }

        if (empty($data['id_postagem']) || empty($data['comentarios'])) {
            throw new Exception('Post e texto do comentário são obrigatórios');
        }

        $id_postagem = (int)$data['id_postagem'];
        $texto       = trim($data['comentarios']);

        if ($texto === '') {
            throw new Exception('Comentário não pode ser vazio');
        }

        // Descobrir qual id_alunos usar para satisfazer a FK
        if (Auth::isAluno()) {
            // aluno normal: já é id_alunos
            $id_alunos = Auth::getUsuarioId();
        } else {
            // professor: pega aluno espelho pela mesma matrícula
            $matricula = Auth::getMatricula();
            $id_alunos = $this->comentarioModel->getAlunoIdByMatricula($matricula);

            if (!$id_alunos) {
                throw new Exception('Professor não está sincronizado na tabela de alunos.');
            }
        }

        $id = $this->comentarioModel->create($id_postagem, $id_alunos, $texto);

        return [
            'id_comentarios' => $id,
            'id_postagem'    => $id_postagem,
            'id_alunos'      => $id_alunos,
            'comentarios'    => $texto,
            'confirmador'    => 0,
        ];
    }




    // Listar comentários de um post (professor pode ver pendentes)
    public function listarPorPost($id_postagem, $somente_aprovados = true)
    {
        if (!is_numeric($id_postagem)) {
            throw new Exception('ID do post inválido');
        }

        // se não for professor, força somente aprovados
        if (!Auth::isProfessor()) {
            $somente_aprovados = true;
        }

        return $this->comentarioModel->getByPost(
            (int)$id_postagem,
            $somente_aprovados
        );
    }

    // Aprovar comentário (professor)
    public function aprovar($id_comentarios)
    {
        if (!Auth::isProfessor()) {
            throw new Exception('Apenas professores podem aprovar comentários');
        }
        if (!is_numeric($id_comentarios)) {
            throw new Exception('ID do comentário inválido');
        }

        return $this->comentarioModel->aprovar((int)$id_comentarios);
    }

    // Reprovar = deletar (professor)
    public function reprovar($id_comentarios)
    {
        if (!Auth::isProfessor()) {
            throw new Exception('Apenas professores podem reprovar comentários');
        }
        if (!is_numeric($id_comentarios)) {
            throw new Exception('ID do comentário inválido');
        }

        return $this->comentarioModel->delete((int)$id_comentarios);
    }

    // Listar todos comentários pendentes (professor)
    public function listarPendentes()
    {
        if (!Auth::isProfessor()) {
            throw new Exception('Apenas professores podem ver comentários pendentes');
        }

        return $this->comentarioModel->getPendentes();
    }
}
