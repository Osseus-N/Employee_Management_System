<?php

namespace login;

use database;

class logInRepository
{
    private $conn;
    private Database $db;
    public function __construct(Database $db)
    {
        $this->conn = $db->connect();
        $this->db = $db;
    }

    public function isAccountExist($acc_email){

        $data = $this->db->select('accounts' , "*" , ['acc_email' => $acc_email]);

        if ($data && $data->num_rows > 0) {
            return $data->fetch_assoc();
        }

        return null;
    }
}