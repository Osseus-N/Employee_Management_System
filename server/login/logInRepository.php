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

        $data = $this->db->select('accounts' , '', ['emp_id' => $emp_id]);
        return $data->fetch_assoc(MYSQLI_ASSOC);
    }

    public function isAccountExist($emp_id){

        $data = $this->db->select('accounts' , '', ['emp_id' => $emp_id]);
        return $data->fetch_assoc(MYSQLI_ASSOC);
    }
}