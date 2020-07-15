<?php

namespace Models;

use Core\Model;
use Models\Jwt;
use Models\Imagens;

class Usuarios extends Model
{
    private $id_usuario;

    public function criar($nome, $email, $senha)
    {
        if (!$this->emailExiste($email)) {
            $hash = \password_hash($senha, PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios_adm (nome_usua, email_usua, senha_usua)
                    VALUES (
                        :nome,
                        :email,
                        :senha
                    )";
            $sqlQuery = $this->db->prepare($sql);
            $sqlQuery->bindValue(':nome', $nome);
            $sqlQuery->bindValue(':email', $email);
            $sqlQuery->bindValue(':senha', $hash);
            $sqlQuery->execute();

            $this->id_usuario = $this->db->lastInsertId();

            return true;
        } else {
            return false;
        }
    }

    public function checkCredeciais($email, $senha)
    {
        $sql = "SELECT 
                    idusuario_usua,
                    senha_usua
                FROM
                    usuarios_adm
                WHERE email_usua = :email";
        $sqlQuery = $this->db->prepare($sql);
        $sqlQuery->bindValue(':email', $email);
        $sqlQuery->execute();

        if ($sqlQuery->rowCount() > 0) {
            $linha = $sqlQuery->fetch();

            if (password_verify($senha, $linha['senha_usua'])) {
                $this->id_usuario = $linha['idusuario_usua'];
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function criarJwt()
    {
        $jwt = new Jwt();
        return $jwt->criarJwt(array('id_usuario' => $this->id_usuario));
    }

    private function emailExiste($email)
    {
        $sql = "SELECT 
                    idusuario_usua
                FROM 
                    usuarios_adm
                WHERE email_usua = :email";
        
        $sqlQuery = $this->db->prepare($sql);
        $sqlQuery->bindValue(':email', $email);
        $sqlQuery->execute();

        if ($sqlQuery->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function validaJwt($token)
    {
        $jwt = new Jwt();

        $info = $jwt->validate($token);

        if (isset($info->id_usuario)) {
            $this->id_usuario = $info->id_usuario;
            return true;
        } else {
            return false;
        }
    }

    public function getId()
    {
        return $this->id_usuario;
    }

    public function getUsuario($id)
    {
        $array = [];

        $sql = "SELECT 
                    idusuario_usua,
                    nome_usua,
                    email_usua,
                    servidor_avatar_usua
                FROM
                    usuarios_adm
                WHERE ativo_usua = :ativo
                AND idusuario_usua = :id";

        $sqlQuery = $this->db->prepare($sql);
        $sqlQuery->bindValue(':id', $id);
        $sqlQuery->bindValue(':ativo', 'S');
        $sqlQuery->execute();

        if ($sqlQuery->rowCount() > 0) {
            $array = $sqlQuery->fetch(\PDO::FETCH_ASSOC);

            $array['servidor_avatar_usua'] = BASE_URL . 'media/avatar/default.jpg';
            if (!empty($array['servidor_avatar_usua'])) {
                $array['servidor_avatar_usua'] = BASE_URL . 'media/avatar/' . $array['servidor_avatar_usua'];
            }
        }

        return $array;
    }

    public function editUsuario($id, $dados)
    {
        if ($id === $this->getId()) {
            $modifica = [];
            if (isset($dados['email']) && !empty($dados['email'])) {
                $emailValido = \filter_var($dados['email'], FILTER_VALIDATE_EMAIL);
                $emailExiste = $this->emailExiste($dados['email']);
                if (!$emailValido) {
                    return 'E-mail inválido';
                }
                if ($emailExiste) {
                    return 'E-mail novo já existe!';
                }
            }
            if (!empty($dados['senha'])) {
                $dados['senha'] = \password_hash($dados['senha'], PASSWORD_DEFAULT);
            }
            unset($dados['jwt']);
            foreach ($dados as $ind => $val) {
                $modifica[$ind . '_usua'] = $val;
            }
            if (count($modifica) > 0) {
                $fiedrs = [];
                foreach ($modifica as $ind => $vl) {
                    $fiedrs[] = $ind . ' = :' . $ind;
                }
                $sql = "UPDATE usuarios_adm SET " . implode(',', $fiedrs) . " WHERE idusuario_usua = :id";
                $sqlQuery = $this->db->prepare($sql);
                $sqlQuery->bindValue(':id', $id);
                foreach ($modifica as $ind => $v) {
                    $sqlQuery->bindValue(':' . $ind, $v);
                }
                $sqlQuery->execute();
                return '';
            } else {
                return 'Preencha os dados corretamente';
            }
        } else {
            return 'Não é permitido editar outro usuário!';
        }
    }

    public function deleteUsuario($id)
    {
        if ($id === $this->getId()) {
            return 'Foi';
        } else {
            return 'Não é permitido excluir outro usuário';
        }
    }

    public function getFeed($offset = 0, $per_page = 10)
    {
        // Fazr depois
    }
}
