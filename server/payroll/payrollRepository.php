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
public function payEmployee(){

}
public function getMonthlyPayroll(){

}
}