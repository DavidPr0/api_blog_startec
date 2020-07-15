<?php

namespace Controllers;

use \Core\Controller;
use \Models\Usuarios;

class ConteudosController extends Controller
{
    public function index()
    {
    }

    public function novoconteudo()
    {
        $array = ['error' => ''];
        $method = $this->getMethod();
        $data = $this->getRequestData();

        return $data;
    }
}
