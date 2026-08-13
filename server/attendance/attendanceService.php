<?php

namespace attendance;

use database;
use mysqli;

class attendanceService
{
    private AttendanceRepository $repo;

    public function __construct(AttendanceRepository $attendanceRepository){

        $this->repo = $attendanceRepository;

    }
    public function recordAttendance($emp_id, $date): array|bool
    {
        if ($this->isWeekend($date)) {
            return [
                "status" => "failed",
                "message" => "Weekend not allowed",
            ];
        }

        $existing = $this->repo->checkAttendance($emp_id, $date);

        if ($existing) {
            return [
                "status" => "failed",
                "message" => "Attendance already exists",
            ];
        }

        return $this->repo->insertAttendance($emp_id, $date);
    }

    private function isWeekend($date): bool
    {
        $dayOfWeek = date('N', strtotime($date));
        return $dayOfWeek >= 6;
    }

    public function getMonthlyAttendance($emp_id, $month, $year): false|array|null
    {
        $isDateValid = $this->isDateValid($month, $year);

        if($isDateValid === false){
            return false;
        }
        return $this->repo->getMonthlyAttendance($emp_id, $month, $year);
    }
    public function presentAttendance($emp_id, $payroll_start_date, $payroll_end_date): int
    {
        if (empty($emp_id) || empty($payroll_start_date) || empty($payroll_end_date)) {
            return 0;
        }

        if ($payroll_start_date > $payroll_end_date) {
            return 0;
        }

        return $this->repo->presentDays(
            $emp_id,
            $payroll_start_date,
            $payroll_end_date
        );
    }
    private function isDateValid($month, $year): bool{

        if($month > 12 || $month < 1){
            return false;
        }
        if($year > date("Y")){
            return false;
        }
        return true;
    }
}