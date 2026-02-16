<?php

require_once __DIR__ . '/../Models/Comentario.php';
require_once __DIR__ . '/../Services/ComentarioService.php';
require_once __DIR__ . '/../Helpers/Response.php';

class ComentarioController
{
    private $service;

    public function __construct()
    {
        $this->service = new ComentarioService();
    }

    // POST /comentarios/criar
    public function criar($data)
    {
        try {
            $comentario = $this->service->criar($data);

            Response::json([
                'status' => 'ok',
                'data'   => $comentario
            ], 201);
        } catch (Exception $e) {
            Response::json([
                'status' => 'erro',
                'msg'    => $e->getMessage()
            ], 400);
        }
    }

    // GET /comentarios/post/{id}?status=aprovado|pendente|todos
    public function listarPorPost($id_postagem, $query)
    {
        try {
            $status = $query['status'] ?? 'aprovado';
            $somenteAprovados = true;

            if ($status === 'pendente') {
                // pendentes = apenas para professor (service já valida)
                $somenteAprovados = false;
            } elseif ($status === 'todos') {
                // professor vê todos; aluno ainda verá só aprovados
                $somenteAprovados = false;
            }

            $comentarios = $this->service->listarPorPost($id_postagem, $somenteAprovados);

            Response::json([
                'status' => 'ok',
                'data'   => $comentarios
            ], 200);
        } catch (Exception $e) {
            Response::json([
                'status' => 'erro',
                'msg'    => $e->getMessage()
            ], 400);
        }
    }

    // POST /comentarios/aprovar/{id}
    public function aprovar($id_comentarios)
    {
        try {
            $ok = $this->service->aprovar($id_comentarios);

            Response::json([
                'status' => $ok ? 'ok' : 'erro',
                'msg'    => $ok ? 'Comentário aprovado' : 'Falha ao aprovar'
            ], $ok ? 200 : 400);
        } catch (Exception $e) {
            Response::json([
                'status' => 'erro',
                'msg'    => $e->getMessage()
            ], 400);
        }
    }

    // DELETE /comentarios/reprovar/{id}
    public function reprovar($id_comentarios)
    {
        try {
            $ok = $this->service->reprovar($id_comentarios);

            Response::json([
                'status' => $ok ? 'ok' : 'erro',
                'msg'    => $ok ? 'Comentário reprovado e excluído' : 'Falha ao reprovar'
            ], $ok ? 200 : 400);
        } catch (Exception $e) {
            Response::json([
                'status' => 'erro',
                'msg'    => $e->getMessage()
            ], 400);
        }
    }

    // GET /comentarios/pendentes
    public function listarPendentes()
    {
        try {
            $comentarios = $this->service->listarPendentes();

            Response::json([
                'status' => 'ok',
                'data'   => $comentarios
            ], 200);
        } catch (Exception $e) {
            Response::json([
                'status' => 'erro',
                'msg'    => $e->getMessage()
            ], 400);
        }
    }
}
