<?php

namespace Core;

use Core;

class Controller
{
    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getRequestData()
    {
        switch ($this->getMethod()) {
            case 'GET':
                return $_GET;
                break;
            case 'PUT':
            case 'DELETE':
                parse_str(file_get_contents('php://input'), $data);
                return (array) $data;
                break;
            case 'POST':
                $dados = json_decode(file_get_contents('php://input'));
                if (is_null($dados)) {
                    $dados = $_POST;
                }

                if (isset($_FILES['img']) && count($_FILES['img']) > 0) {
                    $dados = $this->montaArquivos($dados);
                }
                return (array) $dados;
                break;
        }
    }

    public function returnJson($array)
    {
        header("Content-Type: application/json");
        echo json_encode($array);
        exit;
    }

    private function montaArquivos($dados)
    {
        if (is_array($_FILES['img']['name'])) {
            foreach ($_FILES['img']['name'] as $key => $value) {
                $dados['arquivos'][$key]['name'] = $_FILES['img']['name'][$key];
                $dados['arquivos'][$key]['type'] = $_FILES['img']['type'][$key];
                $dados['arquivos'][$key]['tmp_name'] = $_FILES['img']['tmp_name'][$key];
                $dados['arquivos'][$key]['error'] = $_FILES['img']['error'][$key];
                $dados['arquivos'][$key]['size'] = $_FILES['img']['size'][$key];
            }
        } else {
            $dados['arquivos'][] = $_FILES['img'];
        }

        return $dados;
    }
    
    public function validaDadosRequiscaoGeral($params, $obrigatorio)
    {
        foreach ($params as $ind => $v) {
            if (\in_array($ind, $obrigatorio) && $v == '') {
                $params['error'] = 'O campo ' . $ind . ' é obrigatório.';
            }
        }

        return $params;
    }

    public function uploadImagem($imagem, $pasta)
    {
        try {
            $path = 'storage/' . $pasta;
            if (!is_dir($path)) {
                mkdir($path, true);
            }
            foreach ($imagem as $k => $val) {
                $servidor = preg_replace('/[\/\s\/]/', '_', $val['name']);
                $path2 =  $path . '/' . $servidor;
                move_uploaded_file($val['tmp_name'], $path2);
            }
        } catch (Exception $e) {
            return 'Ocorreu o erro ' . $e->getMenssage() . ' ao tentar cadastrar a imagem do Autor \n';
        }

        return true;
    }
}
