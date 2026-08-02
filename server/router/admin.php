<?php

require_once __DIR__ . '/../database/database.php';
require_once __DIR__ . '/../session/sessionManager.php';
require_once __DIR__ . '/../response/responseController.php';
require_once __DIR__ . '/../model/employee.php';
require_once __DIR__ . '/../employee/employeeRepository.php';
require_once __DIR__ . '/../employee/employeeService.php';
require_once __DIR__ . '/../admin/adminRepository.php';
require_once __DIR__ . '/../admin/adminService.php';
require_once __DIR__ . '/../admin/adminController.php';

use admin\adminController;
use admin\adminRepository;
use admin\adminService;
use employee\employeeRepository;
use employee\employeeService;

$employeeRepository = new employeeRepository();
$employeeService    = new employeeService($employeeRepository);

$adminRepository = new adminRepository();
$adminService    = new adminService($adminRepository);

$controller = new adminController($adminService, $employeeService);
$controller->handleRequest();
