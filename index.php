<?php

// 1. Router & Base Setup
require_once __DIR__ . '/server/router.php';
require_once __DIR__ . '/server/database/database.php';
require_once __DIR__ . '/server/session/sessionManager.php';
require_once __DIR__ . '/server/response/responseController.php';

// 2. Models & Repositories
require_once __DIR__ . '/server/model/employee.php';
require_once __DIR__ . '/server/employee/employeeRepository.php';
require_once __DIR__ . '/server/payroll/payrollRepository.php';
require_once __DIR__ . '/server/attendance/attendanceRepository.php';
require_once __DIR__ . '/server/admin/adminRepository.php';
require_once __DIR__ . '/server/login/logInRepository.php';

// 3. Services
require_once __DIR__ . '/server/payroll/payrollService.php';
require_once __DIR__ . '/server/employee/employeeService.php';
require_once __DIR__ . '/server/admin/adminService.php';
require_once __DIR__ . '/server/login/logInService.php';
require_once __DIR__ . '/server/attendance/attendanceService.php';

// 4. Controllers
require_once __DIR__ . '/server/admin/adminController.php';
require_once __DIR__ . '/server/login/logInController.php';
require_once __DIR__ . '/server/employee/employeeController.php';
require_once __DIR__ . '/server/attendance/attendanceController.php';
require_once __DIR__ . '/server/payroll/payrollController.php';

$database = new Database();

// Repositories
$empRepo        = new \employee\employeeRepository($database);
$payrollRepo    = new \payroll\payrollRepository($database);
$attendanceRepo = new \attendance\attendanceRepository($database);
$adminRepo      = new \admin\adminRepository($database);
$loginRepo      = new \login\logInRepository($database);

// Services
$payrollService    = new \payroll\payrollService($payrollRepo, $attendanceRepo);
$employeeService   = new \employee\employeeService($empRepo);
$adminService      = new \admin\adminService($adminRepo);
$loginService      = new \login\logInService($loginRepo, $empRepo);
$attendanceService = new \attendance\attendanceService($attendanceRepo);

// Controllers
$attendanceController = new \attendance\attendanceController($attendanceService);
$payrollController     = new \payroll\payrollController($payrollService, $employeeService, $attendanceService);
$adminController       = new \admin\adminController($adminService, $payrollService, $employeeService);
$loginController       = new \login\logInController($loginService, $adminRepo, $adminService);
$employeeController    = new \employee\employeeController($employeeService, $attendanceController, $payrollController);

$router = new router();

$router->get('/login', function() use ($loginController) {
    $loginController->showLoginForm();
});

$router->get('/', function() use ($loginController) {
    $loginController->showLoginForm();
});

$router->post('/login', function() use ($loginController) {
    $loginController->login();
});

$router->get('/logout', function() use ($loginController) {
    $loginController->logout();
});

$router->get('/dashboard', function() {
    \session\SessionManager::redirectByRole();
});

$router->get('/employee', function() use ($employeeController) {
    $employeeController->showEmployeeDashBoard();
});

$router->get('/employee/data', function() use ($employeeController) {
    $employeeController->handleEmployees();
});

$router->put('/employee', function() use ($employeeController) {
    $employeeController->handleUpdate();
});

$router->get('/attendance/self', function() use ($attendanceController) {
    $attendanceController->getSelfAttendance(); // confirm actual method name
});

$router->get('/payroll/self', function() use ($payrollController) {
    $payrollController->getSelfPayroll(); // confirm actual method name
});

$router->get('/admin', function() use ($adminController) {
    $adminController->showDashboard(); // HTML shell only
});

$router->get('/admin/employees', function() use ($adminController) {
    if (!empty($_GET['id'])) {
        $adminController->getEmployeeById($_GET['id']);
    } elseif (isset($_GET['search']) && !empty(trim($_GET['search']))) {
        $adminController->searchEmployee($_GET['search']);
    } else {
        $adminController->getAllEmployee();
    }
});

$router->post('/admin/employees', function() use ($adminController) {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];

    if (isset($data['action']) && $data['action'] === 'pay') {
        $adminController->payEmployee($data);
    } else {
        $adminController->createEmployee($data);
    }
});

$router->put('/admin/employees', function() use ($adminController) {
    $adminController->editEmployee();
});

$router->delete('/admin/employees', function() use ($adminController) {
    $adminController->deleteEmployee();
});

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);