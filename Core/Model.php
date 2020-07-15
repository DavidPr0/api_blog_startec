<?php

namespace Core;
use PDO;

class Model
{
    protected $db;

    public function __construct()
    {
        global $config;
        $this->db = new PDO(
            "mysql:dbname=" . $config['dbname'] .
            ";host=" . $config['dbhost'],
            $config['dbusuario'],
            $config['dbpassword']
        );
    }
}
