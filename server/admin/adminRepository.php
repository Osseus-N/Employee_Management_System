<?php

namespace admin;

use Database;
use model\Employee;

class adminRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->db->connect();
    }

    /*
    |--------------------------------------------------------------------------
    | Get All Employees
    |--------------------------------------------------------------------------
    */

    public function getAllEmployees(): array
    {
        $result = $this->db->select("employees");

        $employees = [];

        while ($row = $result->fetch_assoc()) {
            $employees[] = $row;
        }

        return $employees;
    }

    /*
    |--------------------------------------------------------------------------
    | Get One Employee
    |--------------------------------------------------------------------------
    */

    public function getEmployeeById(int $empId): ?array
    {
        $result = $this->db->select(
            "employees",
            "*",
            [
                "emp_id" => $empId
            ]
        );

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Create Employee
    |--------------------------------------------------------------------------
    */

    public function createEmployee(Employee $employee): bool
    {
        return $this->db->insert(
            "employees",
            [
                "emp_firstname"      => $employee->getEmpFirstname(),
                "emp_lastname"       => $employee->getEmpLastname(),
                "emp_gender"         => $employee->getEmpGender(),
                "emp_date_of_birth"  => $employee->getEmpDateOfBirth(),
                "emp_contact_number" => $employee->getEmpContactNumber(),
                "emp_position"       => $employee->getEmpPosition(),
                "emp_hourly_rate"    => $employee->getEmpHourlyRate(),
                "emp_status"         => $employee->getEmpStatus()
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Employee
    |--------------------------------------------------------------------------
    */

    public function updateEmployee(
        int $empId,
        array $data
    ): bool {

        return $this->db->update(
            "employees",
            $data,
            [
                "emp_id" => $empId
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Employee
    |--------------------------------------------------------------------------
    */

    public function deleteEmployee(
        int $empId
    ): bool {

        return $this->db->delete(
            "employees",
            [
                "emp_id" => $empId
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Search Employee
    |--------------------------------------------------------------------------
    */

    public function searchEmployee(
        string $keyword
    ): array {

        $employees = $this->getAllEmployees();

        if (empty($keyword)) {
            return $employees;
        }

        $keyword = strtolower(trim($keyword));

        return array_values(array_filter(
            $employees,
            function ($employee) use ($keyword) {

                return
                    str_contains(
                        strtolower($employee["emp_firstname"]),
                        $keyword
                    ) ||

                    str_contains(
                        strtolower($employee["emp_lastname"]),
                        $keyword
                    ) ||

                    str_contains(
                        strtolower($employee["emp_position"]),
                        $keyword
                    ) ||

                    str_contains(
                        strtolower($employee["emp_status"]),
                        $keyword
                    );
            }
        ));
    }
}