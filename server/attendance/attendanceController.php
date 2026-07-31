<?php

namespace attendance;

use service\SessionManager;
use response\responseController; // Extend or import your response handler class

class attendanceController extends responseController
{
    private AttendanceService $service;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->service = $attendanceService;
    }

    public function handleRequest(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'GET':
                $this->getMonthlyAttendance();
                break;
            case 'POST':
                $this->markAttendance();
                break;
            default:
                $this->error('Method not allowed', 405);
                break;
        }
    }

    private function getMonthlyAttendance(): void
    {
        SessionManager::isLoggedIn();

        $empId = isset($_GET['emp_id']) ? (int)$_GET['emp_id'] : null;
        $month = isset($_GET['month']) ? (string)$_GET['month'] : null;
        $year  = isset($_GET['year']) ? (string)$_GET['year'] : null;

        $attendanceData = $this->service->getMonthlyAttendance($empId, $month, $year);

        $this->success('Monthly attendance retrieved successfully', $attendanceData);
    }

    private function markAttendance(): void
    {
        SessionManager::isLoggedIn();

        $data = json_decode(file_get_contents("php://input"), true) ?? [];

        if (empty($data['emp_id']) || empty($data['status']) || empty($data['date'])) {
            $this->error('Invalid or missing parameters (emp_id, date, status required).', 400);
        }

        $saved = $this->service->recordAttendance(
            $data['emp_id'],
            $data['date'],
            $data['status']
        );

        if ($saved) {
            $this->success('Attendance logged successfully', [
                'emp_id' => $data['emp_id'],
                'status' => $data['status'],
                'date'   => $data['date']
            ], 200);
        }

        $this->error('Database operation failed.', 500);
    }
}