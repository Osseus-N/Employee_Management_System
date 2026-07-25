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
    public function recordAttendance($emp_id, $date ,$status): array
    {

        $existing = $this->repo->checkAttendance($emp_id, $date);

        if($existing){
            return [
                'success' => false,
                'message' => 'Already marked as present'
            ];
        }

        $saves = $this->repo->insertAttendance($emp_id, $date, $status);
        return [
            'success' => $saves,
            'message' => 'Attendance Marked Successfully'
        ];
    }

    public function getMonthlyAttendance($emp_id, $month, $year): array
    {
        $this->isDateValid($month, $year);

        $data = $this->repo->getMonthlyAttendance($emp_id, $month, $year);

        return [
            'success' => true,
            'data' => $data
        ];
    }
    public function presentAttendance($emp_id, $month, $year): array{

        $this->isDateValid($month, $year);

        $data = $this->repo->presentDays($emp_id, $month, $year);

        return [
            'success' => true,
            'data' => $data
        ];
    }
    private function isDateValid($month, $year){
        if ($month < 1 || $month > 12) {
            return [
                'success' => false,
                'message' => 'Invalid month provided. Month must be between 1 and 12.'
            ];
        }

        $currentYear = (int)date('Y');
        if ($year < 2000 || $year > ($currentYear + 1)) {
            return [
                'success' => false,
                'message' => 'Invalid year provided.'
            ];
        }
    }
}