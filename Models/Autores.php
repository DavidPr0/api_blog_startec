<?php

namespace Models;

use Core\Model;

class Autores extends Model
{
    public function salvarAutor($dados)
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
        $salvar = $sqlQuery->bindValue(':nome', $dados['nome'])
                ->bindValue(':id_usuario', $dados['id_usuario'])
                ->bindValue(':tipo_aut', $dados['tipoAutor'])
                ->execute();
        if ($salvar) {
            $img = $this->salvarImgAutor($dados['arquivos'], $dados['id_usuario']);
        }
        return $salvar;
    }

    private function salvarImgAutor($img, $id_usuario)
    {
        $sql = "INSERT INTO autores_imagens (
                idusuario_aut_imag,
                idautor_aut_imag,
                nome_aut_imag,
                servidor_aut_imag,
                tipo_aut_imag,
                tamanho_aut_imag,
                data_cad_aut_imag,
                ativo_aut_imag
            )
            VALUES
            (
                :id_usuario,
                :id_autor,
                :nome,
                :servidor,
                :tamanho,
                NOW(),
                :ativo
            )";
        $sqlQuery = $this->db->prepare($sql);
    }
}
