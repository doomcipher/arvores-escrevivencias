<?php

class AlunoController {
    private $alunoService;

    public function __construct() {
        $this->alunoService = new AlunoService();
    }

    /**
     * POST /api/alunos/criar
     * Criar ou encontrar aluno
     */
    public function criar($data) {
        try {
            $resultado = $this->alunoService->criarOuEncontrar($data);
            Response::json([
                'status' => 'ok',
                'data'   => $resultado
            ], 201);
        } catch (Exception $e) {
            Response::json([
                'status' => 'erro',
                'msg'    => $e->getMessage()
            ], 400);
        }
    }

    /**
     * GET /api/alunos/get/:id
     * Obter um aluno específico
     */
    public function obter($id) {
        try {
            // Validar se ID é número
            if (!is_numeric($id)) {
                throw new Exception('ID deve ser um número');
            }

            // Criar novo aluno model
            $alunoModel = new Aluno();
            $aluno = $alunoModel->getById($id);

            if (!$aluno) {
                Response::json([
                    'status' => 'erro',
                    'msg'    => 'Aluno não encontrado'
                ], 404);
                return;
            }

            Response::json([
                'status' => 'ok',
                'data'   => $aluno
            ], 200);

        } catch (Exception $e) {
            Response::json([
                'status' => 'erro',
                'msg'    => $e->getMessage()
            ], 400);
        }
    }
}

?>
