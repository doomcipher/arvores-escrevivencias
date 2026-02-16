<?php

require_once __DIR__ . '/../Services/PostService.php';
require_once __DIR__ . '/../Helpers/CloudinaryHelper.php';

class PostController
{
    private $postService;



    public function __construct()
    {
        $this->postService = new PostService();
    }

    /**
     * POST /api/posts/criar
     * ⚠️ REQUER AUTENTICAÇÃO (só professor)
     */
    public function criar($data, $files)
    {
        try {
            // Verificar se está logado e é professor
            if (!Auth::isLogado()) {
                Response::json([
                    'status' => 'erro',
                    'msg'    => 'Você precisa estar logado para criar posts'
                ], 401);
                return;
            }

            if (Auth::getTipo() !== 'professor') {
                Response::json([
                    'status' => 'erro',
                    'msg'    => 'Apenas professores podem criar posts'
                ], 403);
                return;
            }

            // Adicionar ID do professor
            $data['id_professor'] = Auth::getUsuarioId();

            // 1) Criar post base (sem link_midia)
            $post        = $this->postService->criar($data);
            $id_postagem = $post['id'];
            $tipo        = $post['tipo_postagem']; // 'video' ou 'imagem'

            // Se não veio arquivo nenhum, só retorna o post base
            if (empty($files['arquivos']) || empty($files['arquivos']['name'][0])) {
                Response::json([
                    'status' => 'ok',
                    'data'   => $post
                ], 201);
                return;
            }

            // Pasta por post no Cloudinary (continua igual)
            $folder = 'posts/' . $id_postagem;

            if ($tipo === 'video') {
                // ===== POST DE VÍDEO: 1 arquivo, salvar link_midia como STRING =====
                $tmpPath = $files['arquivos']['tmp_name'][0];

                if (is_uploaded_file($tmpPath)) {
                    $url = CloudinaryHelper::upload($tmpPath, $folder);
                    if ($url) {
                        $this->postService->atualizar($id_postagem, [
                            'titulo'        => $post['titulo'],
                            'descricao'     => $post['descricao'],
                            'link_midia'    => $url,          // string simples
                            'tipo_postagem' => $post['tipo_postagem'],
                            'tema'          => $post['tema']
                        ]);

                        $post['link_midia'] = $url;
                    }
                }
            } else {
                // ===== POST DE IMAGEM: múltiplos arquivos, salvar como JSON ARRAY =====
                $urls  = [];
                $total = count($files['arquivos']['name']);

                for ($i = 0; $i < $total; $i++) {
                    $tmpPath = $files['arquivos']['tmp_name'][$i];

                    if (!is_uploaded_file($tmpPath)) {
                        continue;
                    }

                    $url = CloudinaryHelper::upload($tmpPath, $folder);
                    if ($url) {
                        $urls[] = $url;
                    }
                }

                if (!empty($urls)) {
                    $jsonUrls = json_encode($urls);

                    $this->postService->atualizar($id_postagem, [
                        'titulo'        => $post['titulo'],
                        'descricao'     => $post['descricao'],
                        'link_midia'    => $jsonUrls,      // ["url1","url2",...]
                        'tipo_postagem' => $post['tipo_postagem'],
                        'tema'          => $post['tema']
                    ]);

                    $post['link_midia'] = $jsonUrls;
                }
            }

            Response::json([
                'status' => 'ok',
                'data'   => $post
            ], 201);
        } catch (Exception $e) {
            Response::json([
                'status' => 'erro',
                'msg'    => $e->getMessage()
            ], 400);
        }
    }



    /**
     * GET /api/posts/listar
     * ✅ PÚBLICO - Qualquer um pode ler
     */
    public function listar($query = [])
    {
        try {
            $pagina = $query['pagina'] ?? 1;
            $posts = $this->postService->listar($pagina);

            Response::json([
                'status' => 'ok',
                'data'   => $posts
            ], 200);
        } catch (Exception $e) {
            Response::json([
                'status' => 'erro',
                'msg'    => $e->getMessage()
            ], 400);
        }
    }

    /**
     * GET /api/posts/get/:id
     * ✅ PÚBLICO - Qualquer um pode ler
     */
    public function obter($id)
    {
        try {
            $post = $this->postService->obter($id);
            Response::json([
                'status' => 'ok',
                'data'   => $post
            ], 200);
        } catch (Exception $e) {
            Response::json([
                'status' => 'erro',
                'msg'    => $e->getMessage()
            ], 400);
        }
    }

    /**
     * PUT /api/posts/update/:id
     * ⚠️ REQUER AUTENTICAÇÃO (só professor)
     */
    public function atualizar($id, $data)
    {
        try {
            if (!Auth::isLogado() || Auth::getTipo() !== 'professor') {
                Response::json([
                    'status' => 'erro',
                    'msg' => 'Apenas professores podem atualizar posts'
                ], 403);
                return;
            }

            $this->postService->atualizar($id, $data);
            Response::json([
                'status' => 'ok',
                'msg'    => 'Post atualizado com sucesso'
            ], 200);
        } catch (Exception $e) {
            Response::json([
                'status' => 'erro',
                'msg'    => $e->getMessage()
            ], 400);
        }
    }

    /**
     * DELETE /api/posts/delete/:id
     * ⚠️ REQUER AUTENTICAÇÃO (só professor)
     */
    public function deletar($id)
    {
        try {
            if (!Auth::isLogado() || Auth::getTipo() !== 'professor') {
                Response::json([
                    'status' => 'erro',
                    'msg' => 'Apenas professores podem deletar posts'
                ], 403);
                return;
            }

            $this->postService->deletar($id);
            Response::json([
                'status' => 'ok',
                'msg'    => 'Post deletado com sucesso'
            ], 200);
        } catch (Exception $e) {
            Response::json([
                'status' => 'erro',
                'msg'    => $e->getMessage()
            ], 400);
        }
    }

    /**
     * GET /api/posts/professor/:id
     * ✅ PÚBLICO - Qualquer um pode ler
     */
    public function obterPorProfessor($id, $query = [])
    {
        try {
            $pagina = $query['pagina'] ?? 1;
            $posts = $this->postService->obterPorProfessor($id, $pagina);

            Response::json([
                'status' => 'ok',
                'data'   => $posts
            ], 200);
        } catch (Exception $e) {
            Response::json([
                'status' => 'erro',
                'msg'    => $e->getMessage()
            ], 400);
        }
    }

    /**
     * GET /api/posts/tema/:tema
     * ✅ PÚBLICO - Qualquer um pode ler
     */
    public function obterPorTema($tema, $query)
    {
        try {
            $filtros = [];
            if (!empty($query['tipo_postagem'])) {
                $filtros['tipo_postagem'] = $query['tipo_postagem'];
            }

            $pagina = !empty($query['pagina']) ? (int)$query['pagina'] : 1;

            $posts = $this->postService->obterPorTema($tema, $filtros, $pagina);

            Response::json([
                'status' => 'ok',
                'data'   => $posts
            ], 200);
        } catch (Exception $e) {
            Response::json(['status' => 'erro', 'msg' => $e->getMessage()], 500);
        }
    }
}
