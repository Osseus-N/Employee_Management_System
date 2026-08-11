<?php

namespace payroll;

use Database;
use mysql_xdevapi\Exception;

class payrollRepository
{
    private Database $db;
    private ?\mysqli $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->connect();
        $this->db = $database;
    }

    public function getMonthlyPayroll($emp_id): array
    {
        $result = $this->db->select('payroll','*',['emp_id' => $emp_id]);

        if (!$result) {
            return [];
        }

        $payroll = $result->fetch_all(MYSQLI_ASSOC);

        foreach ($payroll as &$row) {
            $scheduleId = $row['schedule_id'];
            $schedule = $this->getScheduleById($scheduleId);

            if ($schedule) {
                $row['payroll_start_date'] = $schedule['payroll_start_date'];
                $row['payroll_end_date'] = $schedule['payroll_end_date'];
            }
        }

        return $payroll;
    }

    public function createSchedule(string $startDate,string $endDate): bool {

        try {
            $data = [
                'payroll_start_date' => $startDate,
                'payroll_end_date' => $endDate,
                'status' => 'Open'
            ];
            return (bool)$this->db->insert('payroll_schedule', $data);
        }catch (\Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function getAllUnpaidSchedules(): array{

        $result = $this->db->select(
            'payroll_schedule',
            '*',
            ['status' => 'Open']
        );

        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getScheduleById(int $scheduleId): ?array
    {
        $result = $this->db->select(
            'payroll_schedule',
            '*',
            ['schedule_id' => $scheduleId]
        );

        if (!$result || $result->num_rows === 0) {
            return null;
        }

        return $result->fetch_assoc();
    }

    public function getLastEndDate()
    {
        $result = $this->db->select(
            'payroll_schedule',
            'MAX(payroll_end_date) AS pay_end_date');

        $row = $result->fetch_assoc();
        return $row['pay_end_date'] ?? null;
    }
    public function getActiveEmployees(): array
    {
        $result = $this->db->select(
            'employees',
            '*',
            ['emp_status' => 'Active']
        );

        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function createPayroll(array $data): bool
    {
        return $this->db->insert('payroll', $data);
    }


    public function markScheduleProcessed(int $scheduleId): bool
    {
        return $this->db->update(
            'payroll_schedule',
            ['status' => 'Processed'],
            ['schedule_id' => $scheduleId]
        );
    }

    public function updateSchedule(int $scheduleId,string $startDate,string $endDate
    ): bool {
        return $this->db->update(
            'payroll_schedule',
            [
                'payroll_start_date' => $startDate,
                'payroll_end_date'   => $endDate
            ],
            [
                'schedule_id' => $scheduleId,
                'status'      => 'Open'
            ]
        );
    }

    public function deleteSchedule(int $scheduleId): bool
    {
        return $this->db->delete(
            'payroll_schedule',
            [
                'schedule_id' => $scheduleId,
                'status'      => 'Open'
            ]
        );
    }
}