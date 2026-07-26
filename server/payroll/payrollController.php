<?php

namespace payroll;

use attendance\attendanceController;
use attendance\attendanceService;
use employee\employeeController;
use service\SessionManager;

class payrollController
{
    private payrollService $payrollService;
    private employeeController $employee;
    private attendanceService $attendance;

    public function __construct(PayrollService $payrollService, employeeController $employeeController,
    attendanceService $attService){
        
    $this->payrollService = $payrollService;
    $this->employee = $employeeController;
    $this->attendance = $attService;

    }
    public function handleRequest(){

        header("Content-type: application/json");
        $method = $_SERVER["REQUEST_METHOD"];

        switch ($method) {
            case 'GET':
                $this->getMonthlyPayroll();
                break;
            case 'POST':
                $this->payEmployee();
                break;
            default:
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                break;
        }
    }
    private function getMonthlyPayroll(){

        header("Content-type: application/json");

        SessionManager::isLoggedIn();

        $empId = isset($_GET['emp_id']) ? (int)$_GET['emp_id'] : null;

        $payroll = $this->service->getMonthlyPayroll($empId);

        echo json_encode($payroll);
    }
    private function payEmployee(){

        header("Content-type: application/json");

        $data = json_decode(file_get_contents("php://input"));

        if (!$data || !isset($data['emp_id'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid or missing parameters.'
            ]);
            exit;
        }

        $employee = $this->employee->getEmployee($data['emp_id']);
        $presentDays = $this->attendance->presentAttendance($employee , $data['month'], $data['year']);

        $saved = $this->payrollService->payEmployee($data['emp_id'],$employee['hourly_rate'], $presentDays);

        if ($saved) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Paid Employee Successfully',
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