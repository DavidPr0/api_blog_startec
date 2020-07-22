<?php

namespace Models;

use Core\Controller;
use Core\Model;

class Temas extends Model
{
    public function salvarTema($dados)
    {
        global $config;
        $servidor = \preg_replace('/[\/\s\/]/', '_', $dados['arquivos'][0]['name']);
        $path = 'storage/' . $dados['pasta'] . '/' . $servidor;
        $sql = "INSERT INTO conteudos_temas (
				idconta_tema,
				idusuario_tema,
				descricao_tema,
				servidor_imagem_tema,
				ativo_tema,
				data_cad_tema
			)
			VALUES (
				:idconta,
				:idusuario,
				:descricao,
				:servidor,
				:ativo,
				NOW()
			)";
        $sqlQuery = $this->db->prepare($sql);
        $sqlQuery->bindValue(':idconta', $config['idconta']);
        $sqlQuery->bindValue(':idusuario', $dados['id_usuario']);
        $sqlQuery->bindValue(':descricao', $dados['descricao']);
        $sqlQuery->bindValue(':servidor', $path);
        $sqlQuery->bindValue(':ativo', 'S');
        $salvo = $sqlQuery->execute();
        if ($salvo) {
            $controller = new Controller();
            return $controller->uploadImagem($dados['arquivos'], $dados['pasta']);
        }
        return false;
    }
}
