<?php

require_once __DIR__ . '/../database/database.php';
require_once __DIR__ . '/../session/sessionManager.php';
require_once __DIR__ . '/../response/responseController.php';
require_once __DIR__ . '/../model/employee.php';
require_once __DIR__ . '/../employee/employeeRepository.php';
require_once __DIR__ . '/../employee/employeeService.php';
require_once __DIR__ . '/../employee/employeeController.php';

use employee\employeeController;
use employee\employeeRepository;
use employee\employeeService;

$repository = new employeeRepository();
$service    = new employeeService($repository);
$controller = new employeeController($service);

$controller->handleRequest();
