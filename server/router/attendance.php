<?php

use attendance\attendanceController;
use attendance\attendanceRepository;
use attendance\attendanceService;

require_once "../database/database.php";

require_once "../response/responseController.php";
require_once "../session/sessionManager.php";

require_once "../attendance/attendanceRepository.php";
require_once "../attendance/attendanceService.php";
require_once "../attendance/attendanceController.php";

header("Content-Type: application/json");

try {

    // Database
    $database = new Database();

    // Repository
    $attendanceRepository = new attendanceRepository($database);

    // Service
    $attendanceService = new attendanceService($attendanceRepository);

    // Controller
    $attendanceController = new attendanceController($attendanceService);

    // Handle Request
    $attendanceController->handleRequest();

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);

}