<?php

namespace employee;

    use attendance\attendanceController;
    use attendance\attendanceService;
    use payroll\payrollController;
    use payroll\payrollService;
    use response\responseController;
    use session\sessionManager;

class employeeController extends responseController
{
    private employeeService $service;
    private payrollService $payrollService;
    private attendanceService $attendanceService;

    public function __construct(employeeService $service , attendanceService $attendanceService,
                payrollService $payrollService){
        $this->service = $service;
        $this->payrollService = $payrollService;
        $this->attendanceService = $attendanceService;
    }
    public function handleEmployees(){

        $emp_id = SessionManager::isLoggedIn();

        if (!$emp_id) {
            $this->error("Not logged in", 401);
            return;
        }

        $user = $this->service->getEmployee($emp_id);

        if (!$user) {
            $this->error("Employee not found", 404);
            return;
        }

        SessionManager::isLoggedIn();

        $empId = $user['emp_id'];
        $month = date('m');
        $year  = date('Y');

        $attendance = $this->attendanceService->getMonthlyAttendance($empId, $month, $year);
        $payroll = $this->payrollService->getMonthlyPayroll($emp_id);

        $data =[
            'user'       => $user,
            'attendance' => $attendance,
            'payroll'    => $payroll
        ];

        $this->success("Employee data retrieved successfully", $data);
    }

    public function handleUpdate(): void
    {
        $emp_id = SessionManager::isLoggedIn();

        if (!$emp_id) {
            $this->error("Not logged in", 401);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data) || !is_array($data)) {
            $this->error("Invalid or missing data", 400);
            return;
        }
        $user = $this->service->editEmployee($emp_id, $data);

        if ($user) {
            $this->success("Employee updated successfully", $user);
            return;
        }

        $this->error("Employee not found", 404);
    }
    public function showEmployeeDashBoard()
    {
        $emp_id = SessionManager::isLoggedIn();

        if (!$emp_id) {
            header("Location: /employee_management_system/login");
            exit;
        }

        header("Content-Type: text/html; charset=UTF-8");
        include __DIR__ . '/../../client/view/employee_view.html';
    }

}