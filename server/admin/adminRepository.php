<?php

namespace admin;

use Database;
use model\Employee;

class adminRepository
{
    private Database $db;
    private ?\mysqli $conn;
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
            $row['emp_address']?? null,
            $row['emp_contact_number'] ?? null,
            (int) ($row['emp_id'] ?? 0),
            $row['emp_status'] ?? 'Active',
            $row['emp_created_at'] ?? null
        );
    }
    public function getAllEmployees(): array
    {
        $employees = $this->db->select('employees', '');

        if (!$employees) {
            return [];
        }

        $rows = $employees->fetch_all(MYSQLI_ASSOC);

        $users = [];
        foreach ($rows as $row) {
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
                'emp_address'        => $employee->getAddress(),
                'emp_contact_number' => $employee->getEmpContactNumber(),
                'emp_position'       => $employee->getEmpPosition(),
                'emp_hourly_rate'    => $employee->getEmpHourlyRate(),
                'emp_status'         => $employee->getEmpStatus(),
            ];

            $empId = $this->db->insert('employees', $employeeData);

            $role = ($employeeData['emp_position']) === 'admin' ? 'admin' : 'employee';
            $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);

            $accountData = [
                'emp_id'   => $empId,
                'acc_email'    => $email,
                'acc_password' => $hashedPassword,
                'acc_role' => $role,
            ];

            $this->db->insert('accounts', $accountData);

            $this->conn->commit();
            return true;

        } catch (\Exception $e) {
            $this->conn->rollback();
            throw new \Exception("Failed to register employee and account: " . $e->getMessage());
        }
    }
    public function updateEmployee($data, $where, $table): ?bool
    {

        $data = $this->db->update($table, $data, $where);
        return $data;

    }
    public function deleteEmployee($emp_id): void
    {
        $data = $this->db->delete('employees', ['emp_id' => $emp_id]);
    }
    public function searchEmployee($search): array
    {
        try{
        $searchParam = "%" . $search . "%";

        $sql = "SELECT 
                e.*, 
                a.acc_email,
                CONCAT(e.emp_firstname, ' ', e.emp_lastname) AS full_name 
            FROM employees e
            INNER JOIN accounts a ON e.emp_id = a.emp_id
            WHERE CONCAT(e.emp_firstname, ' ', e.emp_lastname) LIKE ? 
               OR a.acc_email LIKE ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $searchParam, $searchParam);
        $stmt->execute();

        $result = $stmt->get_result();

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $this->rowToEmployee($row);
        }

        $stmt->close();
        return $users;
        }catch (\Exception $e){
            $this->conn->rollback();
            throw new \Exception("Failed to search employee: " . $e->getMessage());
        }
    }
    public function emailExists($email, $excludeEmployeeId = null)
    {
        $result = $this->db->select('accounts', 'emp_id', ['acc_email' => $email]);

        if ($result && $result->num_rows > 0) {
            if ($excludeEmployeeId) {
                $row = $result->fetch_assoc();
                return $row['emp_id'] != $excludeEmployeeId;
            }
            return true;
        }

        return false;
    }

}