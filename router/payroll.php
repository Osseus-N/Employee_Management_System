<?php

use payroll\payrollController;
use  payroll\payrollService;
use payroll\payrollRepository;

require_once __DIR__ . '/../payroll/payrollController.php';
require_once __DIR__ . '/../payroll/payrollService.php';
require_once __DIR__ . '/../payroll/payrollRepository.php';

require_once __DIR__ . '/../response/responseController.php';
require_once __DIR__ . '/../database/database.php';
require_once __DIR__ . '/../session/sessionManager.php';

$database = new Database();
$payrollRepo = new \payroll\payrollRepository($database);
$attendanceRepo = new \attendance\attendanceRepository($database);
$employeeRepo = new \employee\employeeRepository($database);

$empService = new \employee\employeeService($employeeRepo);
$attService = new \attendance\attendanceService($attendanceRepo);

$payrollService    = new \payroll\payrollService($payrollRepo, $attendanceRepo);
$controller = new \payroll\payrollController($payrollService,$empService,$attService);

$controller->handleRequest();