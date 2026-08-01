<?php

namespace employee;

use Database;

class employeeRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->db->connect();
    }

    public function getEmployee($emp_id)
    {
        $result = $this->db->select(
            "employees",
            "*",
            ["emp_id" => $emp_id]
        );

        return $result->fetch_assoc();
    }

    public function createEmployee(array $data)
    {
        return $this->db->insert(
            "employees",
            $data
        );
    }

    public function editEmployee(array $data, array $where)
    {
        return $this->db->update(
            "employees",
            $data,
            $where
        );
    }

    public function deleteEmployee($emp_id)
    {
        return $this->db->delete(
            "employees",
            ["emp_id" => $emp_id]
        );
    }
}