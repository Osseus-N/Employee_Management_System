<?php

namespace attendance;

class attendanceService
{
    private attendanceRepository $attendanceRepository;

    public function __construct(attendanceRepository $attendanceRepository)
    {
        $this->attendanceRepository = $attendanceRepository;
    }

    /*
    |--------------------------------------------------------------------------
    | Get All Attendance
    |--------------------------------------------------------------------------
    */

    public function getAllAttendance(): array
    {
        return $this->attendanceRepository->getAllAttendance();
    }

    /*
    |--------------------------------------------------------------------------
    | Get Attendance of One Employee
    |--------------------------------------------------------------------------
    */

    public function getAttendanceByEmployee(int $empId): array
    {
        return $this->attendanceRepository->getAttendanceByEmployee($empId);
    }

    /*
    |--------------------------------------------------------------------------
    | Time In
    |--------------------------------------------------------------------------
    */

    public function timeIn(int $empId): bool
    {
        $today = date("Y-m-d");

        $existing = $this->attendanceRepository
            ->getAttendanceByDate($empId, $today);

        if ($existing) {
            return false;
        }

        return $this->attendanceRepository
            ->createAttendance([
                "emp_id" => $empId,
                "att_work_date" => $today,
                "att_clock_in" => date("Y-m-d H:i:s")
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Time Out
    |--------------------------------------------------------------------------
    */

    public function timeOut(int $empId): bool
    {
        $today = date("Y-m-d");

        $attendance = $this->attendanceRepository
            ->getAttendanceByDate($empId, $today);

        if (!$attendance) {
            return false;
        }

        if (!empty($attendance["att_clock_out"])) {
            return false;
        }

        $clockIn = strtotime($attendance["att_clock_in"]);
        $clockOut = time();

        $hours = round(
            ($clockOut - $clockIn) / 3600,
            2
        );

        return $this->attendanceRepository
            ->updateAttendance(
                [
                    "att_clock_out" => date("Y-m-d H:i:s"),
                    "att_total_hours" => $hours
                ],
                [
                    "att_id" => $attendance["att_id"]
                ]
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Attendance
    |--------------------------------------------------------------------------
    */

    public function deleteAttendance(int $attId): bool
    {
        return $this->attendanceRepository
            ->deleteAttendance($attId);
    }
}