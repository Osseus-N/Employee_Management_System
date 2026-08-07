<?php

namespace payroll;

use attendance\attendanceService;
use employee\employeeService;
use response\responseController;
use session\SessionManager;

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
    public function getMonthlyPayroll($emp_id){

        header("Content-type: application/json");

        return $this->payrollService->getMonthlyPayroll($emp_id);
    }
    public function payEmployee(){

        header("Content-type: application/json");

        $data = json_decode(file_get_contents("php://input"));

        if (!$data || !isset($data['emp_id'])) {
            $this->error('Invalid request');
        }

        $employee = $this->employee->getEmployee($data['emp_id']);
        $presentDays = $this->attendance->presentAttendance($employee , $data['month'], $data['year']);

        $saved = $this->payrollService->payEmployee($data['emp_id'],$employee['hourly_rate'], $presentDays['data']);

        ($saved) ? $this->success('Paid Employee Successfully',['data' => [
            'emp_id' => $data['emp_id'],
            'status' => $data['status']]])
            :$this->error('Could not found employee', 404);
    }
}