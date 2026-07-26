<?php

use employee\employeeRepository;
use employee\employeeService;
use employee\employeeController;

require_once __DIR__ . '/../employee/employeeRepository.php';
require_once __DIR__ . '/../employee/employeeService.php';
require_once __DIR__ . '/../employee/employeeController.php';

require_once __DIR__ . '/../response/responseController.php';
require_once __DIR__ . '/../database/database.php';
require_once __DIR__ . '/../session/sessionManager.php';

$database = new Database();

$repo       = new \employee\employeeRepository($database);
$service    = new \employee\employeeService($repo);
$controller = new \employee\employeeController($service);

$controller->handleRequest();