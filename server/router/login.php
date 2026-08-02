<?php

require_once __DIR__ . '/../database/database.php';
require_once __DIR__ . '/../session/sessionManager.php';
require_once __DIR__ . '/../response/responseController.php';
require_once __DIR__ . '/../login/loginRepository.php';
require_once __DIR__ . '/../login/loginService.php';
require_once __DIR__ . '/../login/loginController.php';

use login\loginController;
use login\loginRepository;
use login\loginService;

$repository = new loginRepository();
$service    = new loginService($repository);
$controller = new loginController($service);

$controller->handleRequest();
