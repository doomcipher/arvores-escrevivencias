<?php

class AlunoService {
    private $alunoModel;

    public function __construct() {
        $this->alunoModel = new Aluno();
    }

    /**
     * Criar ou encontrar aluno
     * Regra: Se já existe, retorna ele. Se não, cria novo.
     * 
     * @param array $data com 'matricula' e 'nome'
     * @return array com status, acao e dados
     * @throws Exception se dados inválidos
     */
    public function criarOuEncontrar($data) {
        // ============================================
        // VALIDAÇÃO (Regra de negócio)
        // ============================================
        if (empty($data['matricula']) || empty($data['nome'])) {
            throw new Exception('Matrícula e nome são obrigatórios');
        }

        $matricula = trim($data['matricula']);
        $nome = trim($data['nome']);

        // ============================================
        // CHECAR SE JÁ EXISTE (Regra de negócio)
        // ============================================
        $alunoExistente = $this->alunoModel->findByMatricula($matricula);

        if ($alunoExistente) {
            // Retorna os dados do aluno que já existia
            return [
                'acao'      => 'ja_existia',
                'id'        => $alunoExistente['id_alunos'],
                'matricula' => $alunoExistente['matricula'],
                'nome'      => $alunoExistente['nome']
            ];
        }

        // ============================================
        // CRIAR NOVO (chama o Model)
        // ============================================
        $novoId = $this->alunoModel->create($matricula, $nome);

        return [
            'acao'      => 'criado',
            'id'        => $novoId,
            'matricula' => $matricula,
            'nome'      => $nome
        ];
    }
}

?>
