<?php

namespace admin;

use Database;
use model\Employee;

class adminRepository
{
    private Database $db;
    private $conn;
    public function __construct(Database $db){
    $this->db = $db;
    $this->conn = $this->db->connect();
    }

    protected function rowToEmployee(array $row): Employee
    {
        return new Employee(
            $row['emp_firstname'],
            $row['emp_lastname'],
            $row['emp_gender'],
            $row['emp_position'],
            (float) ($row['emp_hourly_rate'] ?? 0.00),
            $row['emp_date_of_birth'] ?? null,
            $row['emp_contact_number'] ?? null,
            (int) ($row['emp_id'] ?? 0),
            $row['emp_status'] ?? 'Active',
            $row['emp_created_at'] ?? null
        );
    }
    public function getAllEmployee(){
        $employees = $this->db->select('employees', '*');

        $result = $employees->fetch_assoc();

        $users = [];

        foreach ($result as $row) {
            $users[] = $this->rowToEmployee($row);
        }
        return $users;
    }
    public function createEmployee(Employee $employee, string $email, string $rawPassword): bool
    {
        try {
            $this->conn->begin_transaction();

            $employeeData = [
                'emp_firstname'      => $employee->getEmpFirstname(),
                'emp_lastname'       => $employee->getEmpLastname(),
                'emp_gender'         => $employee->getEmpGender(),
                'emp_date_of_birth'  => $employee->getEmpDateOfBirth(),
                'emp_contact_number' => $employee->getEmpContactNumber(),
                'emp_position'       => $employee->getEmpPosition(),
                'emp_hourly_rate'    => $employee->getEmpHourlyRate(),
                'emp_status'         => $employee->getEmpStatus(),
            ];

            $this->db->insert('employees', $employeeData);

            $empId = $this->conn->insert_id;

            $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);

            $accountData = [
                'emp_id'   => $empId,
                'email'    => $email,
                'password' => $hashedPassword,
            ];

            $this->db->insert('accounts', $accountData);

            $this->conn->commit();
            return true;

        } catch (\Exception $e) {
            $this->conn->rollback();
            throw new \Exception("Failed to register employee and account: " . $e->getMessage());
        }
    }
    public function updateEmployee($data, $where){
        $data = $this->editEmployee($table, $data, $where);

        if ($data && $data->num_rows > 0) {
            return $data->fetch_assoc();
        }

        return null;
    }
    public function deleteEmployee($emp_id){

    }
    public function searchEmployee($search){

    }

}