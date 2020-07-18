<?php

namespace Models;

use \Core\Model;

class Imagens extends Model
{
    public function salvarImagens($dados)
    {
        $sql = "INSERT INTO autores (
                nome_aut, idusuario_aut, tipo_aut, data_cad_aut
                )
                VALUES (
                    :nome,
                    :id_usuario,
                    :tipo_aut,
                    NOW()
                )";
        $sqlQuery = $this->db->prepare($sql);
        $sqlQuery->bindValue(':nome', $dados['nome']);
        $sqlQuery->bindValue(':id_usuario', $dados['id_usuario']);
        $sqlQuery->bindValue(':tipo_aut', $dados['tipoAutor']);
        $sqlQuery->execute();

        $path = 'storage/' . $dados['pasta'];
        if (!is_dir($path)) {
            mkdir($path, true);
        }
        $path2 =  $path . '/' . $dados['arquivos']['0']['name'];
        move_uploaded_file($dados['arquivos']['0']['tmp_name'], $path2);
        
        return $this->id_autor = $this->db->lastInsertId();
    }
}
