<?php

require_once __DIR__ . '/../database/database.php';
require_once __DIR__ . '/../session/sessionManager.php';
require_once __DIR__ . '/../response/responseController.php';
require_once __DIR__ . '/../attendance/attendanceRepository.php';
require_once __DIR__ . '/../attendance/attendanceService.php';
require_once __DIR__ . '/../attendance/attendanceController.php';

use attendance\attendanceController;
use attendance\attendanceRepository;
use attendance\attendanceService;

$repository = new attendanceRepository();
$service    = new attendanceService($repository);
$controller = new attendanceController($service);

$controller->handleRequest();
