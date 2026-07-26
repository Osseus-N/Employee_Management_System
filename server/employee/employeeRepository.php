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

    public function getEmployee($emp_id){

        $data = $this->db->select('employees' , "*", ['emp_id' => $emp_id]);
        return $data->fetch_assoc();

    }
    public function editEmployee($table, $data, $where){

        $data = $this->db->update($table, $data, $where);
        return $data;

    }
}