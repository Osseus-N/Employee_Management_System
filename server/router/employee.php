<?php

use employee\employeeController;
use employee\employeeRepository;
use employee\employeeService;

require_once "../database/database.php";

require_once "../response/responseController.php";
require_once "../session/sessionManager.php";

require_once "../employee/employeeRepository.php";
require_once "../employee/employeeService.php";
require_once "../employee/employeeController.php";

header("Content-Type: application/json");

try {

    $database = new Database();

    $employeeRepository = new employeeRepository($database);

    $employeeService = new employeeService($employeeRepository);

    $employeeController = new employeeController($employeeService);

    $employeeController->handleRequest();

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);

}