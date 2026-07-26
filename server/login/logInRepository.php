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

    public function logIn($emp_id){

        $data = $this->db->select('employee' , "*", ['emp_id' => $emp_id]);
        return $data->fetch_assoc(MYSQLI_ASSOC);
    }

    public function isAccountExist($acc_email){

        $data = $this->db->select('accounts' , "*" , ['acc_email' => $acc_email]);

        if ($data && $data->num_rows > 0) {
            return $data->fetch_assoc(MYSQLI_ASSOC);
        }

        return null;
    }
}