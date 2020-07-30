<?php

namespace Controllers;

use Core\Controller;
use Models\Jwt;
use Models\Autores;

class AutoresController extends Controller
{
    public function novoautor()
    {
        $array = ['error' => ''];
        $obrigadtorio = ['nome'];
        $method = $this->getMethod();
        $dados = $this->getRequestData();
        $info = $this->validaUsuarioToken($dados['token']);

        if ('POST' == $method) {
            $array = !empty($dados) ? $this->validaDadosRequiscaoGeral($dados, $obrigadtorio) :
            ['error' => 'Requisição sem parâmetro'];
            if (empty($array['error'])) {
                $autores = new Autores();
                $dados['pasta'] = 'imagens_autores';
                $dados['id_usuario'] = $info->id_usuario;
                $array = $autores->salvarAutor($dados);
                if ($array) {
                    $array = ['sucesso' => 'Autor cadastrado com sucesso'];
                }
            }
        }
        $this->returnJson($array);
    }

    private function validaUsuarioToken($token)
    {
        $jwt = new Jwt();
        return $jwt->validate($token);
    }

    public function listar()
    {
        $array = ['error' => ''];
        $method = $this->getMethod();
        $dados = $this->getRequestData();
        $info = $this->validaUsuarioToken($dados['token']);

        if ($method == 'GET') {
            $autoresObj = new Autores();
            $array = $autoresObj->listarAutores();
        }
        return $this->returnJson($array);
    }
}
