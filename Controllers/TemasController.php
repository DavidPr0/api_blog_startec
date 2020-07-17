<?php

namespace Controllers;

use Core\Controller;
use Models\Temas;
use Models\Jwt;

class TemasController extends Controller
{
    public function index()
    {
    }

    public function novotema()
    {
        $array = ['error' => ''];
        $obrigatorios = ['nome'];
        $method = $this->getMethod();
        $dados = $this->getRequestData();
        $jwt = new Jwt();
        $info = $jwt->validate($dados['token']);
        if ($method == 'POST') {
            $array = !empty($dados) ?
                    $this->validaDadosRequiscaoGeral($dados, $obrigatorios) :
                    ['error' => 'Requisição sem parâmetro'];
            if (empty($array['error'])) {
                $temas = new Temas();
                $dados['pasta'] = 'imagem_temas';
                $dados['id_usuario'] = $info->id_usuario;
                $array = $temas->salvarTema($dados);
                if ($array) {
                    $array = ['sucesso' => 'Tema cadastrado com sucesso!'];
                }
            }
        }

        $this->returnJson($array);
    }

    private function validaDadosRequisicao()
    {
        
    }

    public function listar()
    {
        $array = ['erros' => ''];
        $method = $this->getMethod();
        $dados = $this->getRequestData();
        $jwt = new Jwt();
        $info = $jwt->validate($dados['token']);

        if ($method == 'GET') {
            $temas = new Temas();
            $retorno = $temas->listarTemas();
        }
        $this->returnJson($retorno);
    }

}
