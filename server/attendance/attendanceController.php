<?php

namespace attendance;

use service\SessionManager;

class attendanceController
{
    private AttendanceService $service;

    public function __construct(AttendanceService $attendanceService){

        $this->service = $attendanceService;

    }
    public function handleRequest(){

        header('Content-type: application/json');
        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'GET':
                $this->getMonthlyAttendance();
                break;
            case 'POST':
                $this->markAttendance();
                break;
            default:
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                break;
        }
    }
    public function getMonthlyAttendance(): void
    {

        header('Content-type: application/json');

        SessionManager::isLoggedIn();

        $empId = isset($_GET['emp_id']) ? (int)$_GET['emp_id'] : null;
        $month = isset($_GET['month']) ? (string)$_GET['month'] : null;
        $year = isset($_GET['year']) ? (string)$_GET['year'] : null;

        $attendanceDate=$this->service->getMonthlyAttendance($empId, $month, $year);

        echo json_encode(['success' => true, 'data' => $attendanceDate]);
    }
    public function markAttendance(): void
    {

        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"));

        SessionManager::isLoggedIn();

        if (!$data || !isset($data['emp_id'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid or missing parameters.'
            ]);
            exit;
        }

        $saved = $this->service->recordAttendance($data['emp_id'], $data['date'] , $data['status']);

        if ($saved) {
            echo json_encode([
                'success' => true,
                'message' => 'Attendance logged successfully',
                'data' => [
                    'emp_id' => $data['emp_id'],
                    'status' => $data['status']
                ]
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Database operation failed.'
            ]);
        }

    }
}