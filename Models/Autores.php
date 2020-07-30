<?php

namespace Models;

use Core\Controller;
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
        $sqlQuery->bindValue(':nome', $dados['nome']);
        $sqlQuery->bindValue(':id_usuario', $dados['id_usuario']);
        $sqlQuery->bindValue(':tipo_aut', $dados['tipoAutor']);
        $salvar = $sqlQuery->execute();
        if ($salvar) {
            return $this->salvarImgAutor($dados['arquivos'], $dados, $this->db->lastInsertId());
        }
        return false;
    }

    private function salvarImgAutor($img, $dados, $idautor)
    {
        $servidor = preg_replace('/[\/\s\/]/', '_', $img[0]['name']);
        $path = 'storage/' . $dados['pasta'] . '/' . $servidor;
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
                :tipo,
                :tamanho,
                NOW(),
                :ativo
            )";
        $sqlQuery = $this->db->prepare($sql);
        $sqlQuery->bindValue(':id_usuario', $dados['id_usuario']);
        $sqlQuery->bindValue(':id_autor', $idautor);
        $sqlQuery->bindValue(':nome', $img[0]['name']);
        $sqlQuery->bindValue(':servidor', $path);
        $sqlQuery->bindValue(':tipo', $img[0]['type']);
        $sqlQuery->bindValue(':tamanho', $img[0]['size']);
        $sqlQuery->bindValue(':ativo', 'S');

        $imgSalva = $sqlQuery->execute();
        if ($imgSalva) {
            $controller = new Controller();
            return $controller->uploadImagem($img, $dados['pasta']);
        }
        return false;
    }

    public function listarAutores()
    {
        global $config;
        $autores = [];
        $sql = "SELECT
                a.idautor, a.idconta_aut, a.idusuario_aut, a.nome_aut, a.tipo_aut, i.servidor_aut_imag
            FROM
                autores as a
            INNER JOIN autores_imagens as i ON (a.idautor = i.idautor_aut_imag)
            WHERE a.ativo_aut = :ativo
            AND i.ativo_aut_imag = :ativo_img
            AND a.idconta_aut = :idconta";
        $sqlQuery = $this->db->prepare($sql);
        $sqlQuery->bindValue(':ativo', 'S');
        $sqlQuery->bindValue(':ativo_img', 'S');
        $sqlQuery->bindValue(':idconta', $config['idconta']);
        $sqlQuery->execute();

        if ($sqlQuery->rowCount() > 0) {
            $autores['retorno'] = $sqlQuery->fetchAll(\PDO::FETCH_ASSOC);
        }
        return $autores;
    }
}
