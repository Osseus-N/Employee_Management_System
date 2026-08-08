<?php

namespace employee;

use Database;

class employeeRepository
{
    private Database $db;
    private $conn;
    public function __construct(Database $db){
        $this->db = $db;
        $this->conn = $this->db->connect();
    }

    public function getEmployee($emp_id): false|array|null
    {

        $data = $this->db->select('employees' , "*", ['emp_id' => $emp_id]);
        return $data->fetch_assoc();

    }
    public function getAccountEmail($emp_id){

        $data = $this->db->select('accounts' , "*", ['emp_id' => $emp_id]);
        $result = $data->fetch_assoc();

        return $result['acc_email'] ?? null;
    }
    public function editEmployee($table, $data, $where){

        $data = $this->db->update($table, $data, $where);
        return $data;

    }
}