<?php

namespace employee;

use Database;
use Exception;

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
    public function editEmployee($data, $accData,$emp_id): bool
    {
        try {
            $this->conn->begin_transaction();

            $employee = $this->db->select('employees', "*",['emp_id' => $emp_id]);

            if (!$employee) {
                $this->conn->rollBack();
                return false;
            }

            $accountUpdated = $this->db->update('accounts', $accData, ['emp_id' => $emp_id]);

            if (!$accountUpdated) {
                $this->conn->rollBack();
                return false;
            }

            $employeeUpdated = $this->db->update(
                'employees',
                $data,
                ['emp_id' => $emp_id]
            );

            if (!$employeeUpdated) {
                $this->conn->rollBack();
                return false;
            }

            $this->conn->commit();

            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}