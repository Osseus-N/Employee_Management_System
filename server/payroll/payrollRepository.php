<?php

namespace payroll;

use database;

class payrollRepository
{
    private Database $db;
    private $conn;
public function __construct(Database $db){
    $this->db = $db;
    $this->conn = $this->db->connect();
}
public function payEmployee($data){

    return $this->db->insert('payroll', $data);

}
public function getMonthlyPayroll($emp_id){

    $data = $this->db->select('payroll','*',  ['emp_id' => $emp_id]);
    return $data->fetch_assoc();
}
}