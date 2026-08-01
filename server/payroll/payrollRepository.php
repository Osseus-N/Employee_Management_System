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
public function payEmployee($emp_id, $data){
    try {
        return $this->db->insert('payroll', $data);
    }catch (\Exception $e){
        $this->conn->rollback();
        throw new ("Failed to pay employee and account: " . $e->getMessage());
    }
}
    public function getMonthlyPayroll($emp_id) {
        $result = $this->db->select('payroll', '*', ['emp_id' => $emp_id]);

        if (!$result) {
            return [];
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

}