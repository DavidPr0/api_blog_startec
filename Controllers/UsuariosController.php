<?php

namespace Controllers;

use Core\Controller;
use Models\Usuarios;

class UsuariosController extends Controller
{
    public function index()
    {
    }

    public function login()
    {
        $array = ['error' => ''];
        $method = $this->getMethod();
        $data = $this->getRequestData();
        if ($method == 'POST') {
            $array = !empty($data) ? $this->validaDadosRequisicao($data) : ['error' => 'Sem parâmetros!'];
            if (empty($array['error']['email']) && empty($array['error']['senha'])) {
                $usuarios = new Usuarios();
                unset($array['error']);
                if ($usuarios->checkCredeciais($data['email'], $data['senha'])) {
                    $array['jwt'] = $usuarios->criarJwt();
                } else {
                    $array['error'] = 'Acesso negado';
                    // $array['hash'] = password_hash(123456, PASSWORD_BCRYPT);
                }
            }
        } else {
            $array['error'] = 'Método não permitido para esse tipo de requisição.';
        }

        $this->returnJson($array);
    }

    public function novousuario()
    {
        $array = ['error' => ''];
        $method = $this->getMethod();
        $data = $this->getRequestData();
        if ($method == 'POST') {
            $array = !empty($data) ? $this->validaDadosRequisicao($data) : ['error' => 'Sem parâmetros!'];
            if (empty($array['error']['email']) && empty($array['error']['senha']) && empty($array['error']['nome'])) {
                if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $usuarios = new Usuarios();
                    unset($array['error']);
                    if ($usuarios->criar($data['nome'], $data['email'], $data['senha'])) {
                        $array['jwt'] = $usuarios->criarJwt();
                    } else {
                        $array['error'] = 'E-mail já existe.';
                    }
                } else {
                    $array['error'] = 'E-mail inválido';
                }
            }
        } else {
            $array['error'] = 'Método não permitido para esse tipo de requisição.';
        }
        
        $this->returnJson($array);
    }

    private function validaDadosRequisicao($dados)
    {
        foreach ($dados as $key => $val) {
            switch ($key) {
                case 'email':
                    $array['error']['email'] = empty($val) ? 'E-mail não preenchido.' : '';
                    break;
                case 'senha':
                    $array['error']['senha'] = empty($val) ? 'Senha não preenchida.' : '';
                    break;
                case 'nome':
                    $array['error']['nome'] = empty($val) ? 'Nome não preenchido.' : '';

                    break;
                default:
                    $array['error'] =  'Falta parâmetros.';
                    break;
            }
        }
        return $array;
    }

    public function view($id)
    {
        $array = ['error' => '', 'logged' => false];
        $method = $this->getMethod();
        $dados = $this->getRequestData();
        $usuarios = new Usuarios();
        if (!empty($dados['jwt']) && $usuarios->validaJwt($dados['jwt'])) {
            $array['logged'] = true;
            $array['is_me'] = false;
            if ($id == $usuarios->getId()) {
                $array['is_me'] = true;
            }

            switch ($method) {
                case 'GET':
                    $array['dados'] = $usuarios->getUsuario($id);

                    if (count($array['dados']) === 0) {
                        $array['error'] = 'Usuário não existe.';
                    }

                    break;
                case 'PUT':
                    $array['error'] = $usuarios->editUsuario($id, $dados);

                    break;
                case 'DELETE':
                    $array['error'] = $usuarios->deleteUsuario($id);
                    break;
                default:
                    $array['error'] = 'Método ' . $method . ' não diponível';

                    break;
            }
        } else {
            $array['error'] = 'Acesso negado!';
        }

        $this->returnJson($array);
    }
    
    public function feed($id)
    {
        $array = ['error' => '', 'logged' => false];
        $method = $this->getMethod();
        $dados = $this->getRequestData();
        $usuarios = new Usuarios();
        if (!empty($dados['jwt']) && $usuarios->validaJwt($dados['jwt'])) {
            $array['logged'] = true;

            if ($method == 'GET') {
                $offset = 0;
                if (!empty($dados['offset'])) {
                    $offset = \intval($dados['offset']);
                }
                $per_page = 10;
                if (!empty($dados['per_page'])) {
                    $per_page = \intval($dados['per_page']);
                }
                $array['dados'] = $usuarios->getFeed($offset, $per_page);
            } else {
                $array['error'] = 'Método ' . $method . ' não diponível';
            }
        } else {
            $array['error'] = 'Acesso negado!';
        }

        $this->returnJson($array);
    }
}
