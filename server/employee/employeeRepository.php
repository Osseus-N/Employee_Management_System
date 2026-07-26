<?php

namespace employee;

require_once __DIR__ . "/../database/database.php";

class employeeRepository
{
    private \Database $database;

    public function __construct(\Database $database)
    {
        $this->database = $database;
        $this->database->connect();
    }

    // GET ALL EMPLOYEES
    public function getEmployees()
    {
        $result = $this->database->select("employees");

        $employees = [];

        while ($row = $result->fetch_assoc()) {
            $employees[] = $row;
        }

        return $employees;
    }

    // GET SINGLE EMPLOYEE
    public function getEmployeeById($id)
    {
        $result = $this->database->select(
            "employees",
            "*",
            ["emp_id" => $id]
        );

        return $result->fetch_assoc();
    }

    // INSERT EMPLOYEE
    public function addEmployee(array $employee)
    {
        return $this->database->insert(
            "employees",
            $employee
        );
    }

    // UPDATE EMPLOYEE
    public function updateEmployee($id, array $employee)
    {
        return $this->database->update(
            "employees",
            $employee,
            [
                "emp_id" => $id
            ]
        );
    }

    // DELETE EMPLOYEE
    public function deleteEmployee($id)
    {
        return $this->database->delete(
            "employees",
            [
                "emp_id" => $id
            ]
        );
    }
}