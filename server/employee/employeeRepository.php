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

    public function getEmployee(int $empId): ?array
    {
        $result = $this->db->select(
            "employees",
            "*",
            ["emp_id" => $empId]
        );

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }

    public function updateEmployee(
        int $empId,
        array $data
    ): bool
    {
        return $this->db->update(
            "employees",
            $data,
            ["emp_id"=>$empId]
        );
    }
}