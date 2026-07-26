<?php

use login\logInRepository;

require_once __DIR__ . '/logInRepository.php';
require_once __DIR__ . '/logInService.php';
require_once __DIR__ . '/logInController.php';
require_once __DIR__ . '/../database/database.php';
require_once __DIR__ . '/../session/sessionManager.php';

$database = new Database();
$repo = new \logIn\LoginRepository($database);
$service = new \login\logInService($repo);
$controller = new \login\logInController($service);

$controller->showLoginForm();