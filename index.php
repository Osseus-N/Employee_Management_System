<?php

use login\logInRepository;
use login\logInService;
use login\logInController;


require_once __DIR__ . '/server/model/employee.php';
require_once __DIR__ . '/server/response/responseController.php';
require_once __DIR__ . '/server/login/logInRepository.php';
require_once __DIR__ . '/server/login/logInService.php';
require_once __DIR__ . '/server/login/logInController.php';

require_once __DIR__ . '/server/database/database.php';
require_once __DIR__ . '/server/session/sessionManager.php';
require_once __DIR__ . '/server/admin/adminService.php';
require_once __DIR__ . '/server/admin/adminRepository.php';
require_once __DIR__ . '/server/employee/employeeRepository.php';

$database = new Database();
$employeeRepo = new \employee\employeeRepository($database);
$loginRepo = new \login\loginRepository($database);
$adminRepo = new \admin\adminRepository($database);
$adminService = new \admin\adminService($adminRepo);

$service = new \login\logInService($loginRepo, $employeeRepo);
$controller = new \login\logInController($service,$adminRepo, $adminService);

$controller->handleRequest();



