<?php

use login\logInRepository;
use login\logInService;
use login\logInController;

require_once __DIR__ . '/../login/logInRepository.php';
require_once __DIR__ . '/../login/logInService.php';
require_once __DIR__ . '/../login/logInController.php';

require_once __DIR__ . '/../response/responseController.php';
require_once __DIR__ . '/../database/database.php';
require_once __DIR__ . '/../session/sessionManager.php';

$database = new Database();
$employeeRepo = new \employee\employeeRepository($database);
$loginRepo = new \login\loginRepository($database);

$service = new \login\logInService($loginRepo, $employeeRepo);
$controller = new \login\logInController($service);

$controller->showLoginForm();