<?php

use admin\adminRepository;
use admin\adminService;
use admin\adminController;
use payroll\payrollRepository;
use payroll\payrollService;
use employee\employeeRepository;
use employee\employeeService;

require_once __DIR__ . '/../admin/adminRepository.php';
require_once __DIR__ . '/../admin/adminService.php';
require_once __DIR__ . '/../admin/adminController.php';

require_once __DIR__ . '/../payroll/payrollRepository.php';
require_once __DIR__ . '/../payroll/payrollService.php';

require_once __DIR__ . '/../employee/employeeRepository.php';
require_once __DIR__ . '/../employee/employeeService.php';

require_once __DIR__ . '/../response/responseController.php';
require_once __DIR__ . '/../database/database.php';
require_once __DIR__ . '/../session/sessionManager.php';

$database = new Database();

$adminRepo    = new \admin\adminRepository($database);
$payrollRepo  = new \payroll\payrollRepository($database);
$employeeRepo = new \employee\employeeRepository($database);
$attendanceRepo = new \attendance\attendanceRepository($database);

$adminService    = new \admin\adminService($adminRepo);
$payrollService  = new \payroll\payrollService($payrollRepo,$attendanceRepo);
$employeeService = new \employee\employeeService($employeeRepo);

$controller = new \admin\adminController(
    $adminService,
    $payrollService,
    $employeeService
);

$controller->handleRequest();