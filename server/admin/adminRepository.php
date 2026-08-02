<?php

namespace admin;

use database\Database;
use PDO;

class adminRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getDashboardCounts(): array
    {
        $stmt = $this->db->query("SELECT emp_status, COUNT(*) as total FROM employees GROUP BY emp_status");

        $counts = ['Active' => 0, 'Inactive' => 0, 'Terminated' => 0];
        foreach ($stmt->fetchAll() as $row) {
            $counts[$row['emp_status']] = (int) $row['total'];
        }
        $counts['Total'] = $counts['Active'] + $counts['Inactive'] + $counts['Terminated'];

        return $counts;
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare("SELECT acc_id FROM accounts WHERE acc_email = :email");
        $stmt->execute(['email' => $email]);

        return (bool) $stmt->fetch();
    }

    public function createAccount(int $empId, string $email, string $password, string $role): bool
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare(
            "INSERT INTO accounts (emp_id, acc_email, acc_role, acc_password)
             VALUES (:emp_id, :email, :role, :password)"
        );

        return $stmt->execute([
            'emp_id'   => $empId,
            'email'    => $email,
            'role'     => $role,
            'password' => $hash,
        ]);
    }

    public function deleteAccountForEmployee(int $empId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM accounts WHERE emp_id = :emp_id");
        return $stmt->execute(['emp_id' => $empId]);
    }
}
