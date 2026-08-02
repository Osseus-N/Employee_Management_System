<?php

namespace attendance;

use database\Database;
use PDO;

class attendanceRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query(
            "SELECT a.*, e.emp_firstname, e.emp_lastname
             FROM attendance a
             INNER JOIN employees e ON a.emp_id = e.emp_id
             ORDER BY a.att_work_date DESC"
        );

        return $stmt->fetchAll();
    }

    public function findByEmployee(int $empId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM attendance WHERE emp_id = :id ORDER BY att_work_date DESC"
        );
        $stmt->execute(['id' => $empId]);

        return $stmt->fetchAll();
    }

    public function findTodayRecord(int $empId, string $date): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM attendance WHERE emp_id = :id AND att_work_date = :date"
        );
        $stmt->execute(['id' => $empId, 'date' => $date]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function clockIn(int $empId, string $date, string $time): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO attendance (emp_id, att_work_date, att_clock_in)
             VALUES (:id, :date, :time)"
        );
        $stmt->execute(['id' => $empId, 'date' => $date, 'time' => $time]);

        return (int) $this->db->lastInsertId();
    }

    public function clockOut(int $attId, string $time, float $totalHours): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE attendance SET att_clock_out = :time, att_total_hours = :hours
             WHERE att_id = :id"
        );

        return $stmt->execute(['time' => $time, 'hours' => $totalHours, 'id' => $attId]);
    }
}
