<?php

namespace payroll;

use database\Database;
use PDO;

class payrollRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query(
            "SELECT p.*, e.emp_firstname, e.emp_lastname
             FROM payroll p
             INNER JOIN employees e ON p.emp_id = e.emp_id
             ORDER BY p.pay_period_start DESC"
        );

        return $stmt->fetchAll();
    }

    public function findByEmployee(int $empId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM payroll WHERE emp_id = :id ORDER BY pay_period_start DESC"
        );
        $stmt->execute(['id' => $empId]);

        return $stmt->fetchAll();
    }

    public function sumHours(int $empId, string $start, string $end): float
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(att_total_hours), 0) AS total
             FROM attendance
             WHERE emp_id = :id AND att_work_date BETWEEN :start AND :end"
        );
        $stmt->execute(['id' => $empId, 'start' => $start, 'end' => $end]);

        return (float) $stmt->fetch()['total'];
    }

    public function getHourlyRate(int $empId): float
    {
        $stmt = $this->db->prepare("SELECT emp_hourly_rate FROM employees WHERE emp_id = :id");
        $stmt->execute(['id' => $empId]);
        $row = $stmt->fetch();

        return $row ? (float) $row['emp_hourly_rate'] : 0.0;
    }

    public function existsForPeriod(int $empId, string $start, string $end): bool
    {
        $stmt = $this->db->prepare(
            "SELECT pay_id FROM payroll
             WHERE emp_id = :id AND pay_period_start = :start AND pay_period_end = :end"
        );
        $stmt->execute(['id' => $empId, 'start' => $start, 'end' => $end]);

        return (bool) $stmt->fetch();
    }

    public function create(int $empId, string $start, string $end, float $hours, float $amount): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO payroll
                (emp_id, pay_period_start, pay_period_end, pay_total_hours, pay_status, pay_amount)
             VALUES
                (:id, :start, :end, :hours, 'Pending', :amount)"
        );
        $stmt->execute([
            'id' => $empId, 'start' => $start, 'end' => $end, 'hours' => $hours, 'amount' => $amount,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function markPaid(int $payId): bool
    {
        $stmt = $this->db->prepare("UPDATE payroll SET pay_status = 'Paid' WHERE pay_id = :id");
        return $stmt->execute(['id' => $payId]);
    }
}
