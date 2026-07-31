<?php

use attendance\AttendanceRepository;
use attendance\AttendanceService;
use attendance\attendanceController;

require_once __DIR__ . '/../attendance/AttendanceRepository.php';
require_once __DIR__ . '/../attendance/AttendanceService.php';
require_once __DIR__ . '/../attendance/attendanceController.php';

require_once __DIR__ . '/../response/responseController.php';
require_once __DIR__ . '/../database/database.php';
require_once __DIR__ . '/../session/sessionManager.php';

$database = new Database();

$repo       = new \attendance\AttendanceRepository($database);
$service    = new \attendance\AttendanceService($repo);
$controller = new \attendance\attendanceController($service);

$controller->handleRequest();