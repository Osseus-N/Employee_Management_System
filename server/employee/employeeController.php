<?php

namespace employee;

    use attendance\attendanceController;
    use payroll\payrollController;
    use response\responseController;
    use session\sessionManager;

class employeeController extends responseController
{
    private employeeService $service;
    private payrollController $payrollController;
    private attendanceController $attController;

    public function __construct(employeeService $service , attendanceController $attController,
                                payrollController $payController){
        $this->payrollController = $payController;
        $this->attController = $attController;
        $this->service = $service;
    }
    public function handleEmployees(){

        $emp_id = SessionManager::isLoggedIn();

        $user = $this->service->getEmployee($emp_id);

        if (!$user) {
            $this->error("Employee not found", 404);
            return;
        }

        $attendance = $this->attController->getMonthlyAttendance($emp_id);
        $payroll = $this->payrollController->getMonthlyPayroll($emp_id);

        $data =[
            'user'       => $user,
            'attendance' => $attendance,
            'payroll'    => $payroll
        ];

        $this->success("Employee data retrieved successfully", $data);
    }

    public function handleUpdate(){
        $emp_id = SessionManager::isLoggedIn();

        if (!$emp_id) {
            $this->error("Not logged in", 401);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        $user = $this->service->editEmployee($emp_id, $data);

        ($user) ? $this->success("Employee updated successfully", $user)
            : $this->error("Employee not found", 404);
        exit;
    }

    public function showEmployeeDashBoard()
    {
        $emp_id = SessionManager::isLoggedIn();

        header("Content-Type: text/html; charset=UTF-8");
        include __DIR__ . '/../../client/view/employee_view.html';
    }

}