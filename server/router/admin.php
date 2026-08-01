<?php

use admin\adminController;
use admin\adminRepository;
use admin\adminService;

use employee\employeeRepository;
use employee\employeeService;

require_once "../database/database.php";

require_once "../response/responseController.php";
require_once "../session/sessionManager.php";

require_once "../model/Employee.php";

require_once "../employee/employeeRepository.php";
require_once "../employee/employeeService.php";

require_once "../admin/adminRepository.php";
require_once "../admin/adminService.php";
require_once "../admin/adminController.php";

header("Content-Type: application/json");

try {

    // Database
    $database = new Database();

    // Employee Module
    $employeeRepository = new employeeRepository($database);
    $employeeService = new employeeService($employeeRepository);

    // Admin Module
    $adminRepository = new adminRepository($database);
    $adminService = new adminService($adminRepository);

    // Controller
    $controller = new adminController($adminService);

    // Handle Request
    $controller->handleRequest();

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}