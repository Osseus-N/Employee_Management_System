<?php

namespace attendance;

use response\responseController;
use session\SessionManager;

class attendanceController extends responseController
{
    private AttendanceService $service;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->service = $attendanceService;
    }

    public function getMonthlyAttendance(): void
    {
        SessionManager::isLoggedIn();

        $empId = $_SESSION["emp_id"];
        $month = date('m');
        $year  = date('Y');

        $monthlyAttendance = $this->service->getMonthlyAttendance($empId, $month, $year);

        if($monthlyAttendance === null){
            $this->error("Date is not valid" , 422);
        }

        $this->success(
            "Monthly Attendance successfully retrieved",
            ["month" => $month,
            "year" => $year,
            "monthlyAttendance" => $monthlyAttendance]
        );
    }

    public function markAttendance(): void
    {
        SessionManager::isLoggedIn();

        $empId = $_SESSION['emp_id'] ?? null;

        if (empty($empId)) {
            $this->error('Unauthorized.', 401);
        }
        $date = date('Y-m-d');
        $saved = $this->service->recordAttendance($empId, $date);

        if ($saved === true) {
            $this->success('Attendance logged successfully', [
                'emp_id' => $empId,
                'status' => 'success',
                'date'   => $date
            ], 200);
        }

        if (is_array($saved)) {
            $this->error(
                $saved['message'] ?? 'Failed to record attendance.',
                400
            );
        }

        $this->error('Failed to record attendance.', 500);
    }
}