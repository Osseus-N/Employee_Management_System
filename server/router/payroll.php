<?php

require_once __DIR__ . '/../database/database.php';
require_once __DIR__ . '/../session/sessionManager.php';
require_once __DIR__ . '/../response/responseController.php';
require_once __DIR__ . '/../payroll/payrollRepository.php';
require_once __DIR__ . '/../payroll/payrollService.php';
require_once __DIR__ . '/../payroll/payrollController.php';

use payroll\payrollController;
use payroll\payrollRepository;
use payroll\payrollService;

$repository = new payrollRepository();
$service    = new payrollService($repository);
$controller = new payrollController($service);

$controller->handleRequest();
