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
				nome_tema,
				descricao_tema,
				servidor_imagem_tema,
				ativo_tema,
				data_cad_tema
			)
			VALUES (
				:idconta,
				:idusuario,
				:nome
				:descricao,
				:servidor,
				:ativo,
				NOW()
			)";
        $sqlQuery = $this->db->prepare($sql);
        $sqlQuery->bindValue(':idconta', $config['idconta']);
        $sqlQuery->bindValue(':idusuario', $dados['id_usuario']);
        $sqlQuery->bindValue(':nome', $dados['nome']);
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

    public function listarTemas()
    {
        global $config;
        $temas = [];
        $sql = "SELECT 
				t.idtema, t.nome_tema, t.descricao_tema, t.servidor_imagem_tema, t.idusuario_tema, t.data_cad_tema
			FROM
				conteudos_temas as t
			WHERE t.ativo_tema = :ativo
			AND t.idconta_tema = :idconta";
        $sqlQuery = $this->db->prepare($sql);
        $sqlQuery->bindValue(':ativo', 'S');
        $sqlQuery->bindValue(':idconta', $config['idconta']);
        $sqlQuery->execute();
        
        if ($sqlQuery->rowCount() > 0) {
            $temas['retorno'] = $sqlQuery->fetchAll(\PDO::FETCH_ASSOC);
        }

        return $temas;
    }
}
