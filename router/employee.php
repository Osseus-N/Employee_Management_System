<?php

use employee\employeeRepository;
use employee\employeeService;
use employee\employeeController;

require_once __DIR__ . '/../server/response/responseController.php';
require_once __DIR__ . '/../server/employee/employeeRepository.php';
require_once __DIR__ . '/../server/employee/employeeService.php';
require_once __DIR__ . '/../server/employee/employeeController.php';

require_once __DIR__ . '/../server/database/database.php';
require_once __DIR__ . '/../server/session/sessionManager.php';

$database = new Database();

$repo       = new \employee\employeeRepository($database);
$service    = new \employee\employeeService($repo);
$controller = new \employee\employeeController($service);

$controller->handleRequest();