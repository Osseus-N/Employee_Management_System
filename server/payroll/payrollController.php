<?php

namespace payroll;

use attendance\attendanceController;
use attendance\attendanceService;
use employee\employeeController;
use employee\employeeService;
use response\responseController;
use service\SessionManager;

class payrollController extends responseController
{
    private payrollService $payrollService;
    private employeeService $employee;
    private attendanceService $attendance;

    public function __construct(PayrollService $payrollService, employeeService $employeeService,
    attendanceService $attService){
        
    $this->payrollService = $payrollService;
    $this->employee = $employeeService;
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
                $this->error('Invalid request',405);
                break;
        }
    }
    private function getMonthlyPayroll(){

        header("Content-type: application/json");

        SessionManager::isLoggedIn();

        $empId = isset($_GET['emp_id']) ? (int)$_GET['emp_id'] : null;

        $payroll = $this->payrollService->getMonthlyPayroll($empId);

        $this->success($payroll);
    }
    private function payEmployee(){

        header("Content-type: application/json");

        $data = json_decode(file_get_contents("php://input"));

        if (!$data || !isset($data['emp_id'])) {
            $this->error('Invalid request');
        }

        $employee = $this->employee->getEmployee($data['emp_id']);
        $presentDays = $this->attendance->presentAttendance($employee , $data['month'], $data['year']);

        $saved = $this->payrollService->payEmployee($data['emp_id'],$employee['hourly_rate'], $presentDays);

        ($saved) ? $this->success('Paid Employee Successfully',['data' => [
            'emp_id' => $data['emp_id'],
            'status' => $data['status']]])
            :$this->error('Could not found employee', 404);
    }
}