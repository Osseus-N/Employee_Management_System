<?php

namespace payroll;

use Exception;
use response\responseController;
use service\SessionManager;

class payrollController extends responseController
{
    private payrollService $payrollService;

    public function __construct(payrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    public function handleRequest(): void
    {
        SessionManager::init();

        if (!SessionManager::isLoggedIn()) {
            $this->error('Please log in first.', 401);
        }

        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'GET':
                $this->getPayroll();
                break;
            case 'POST':
                $this->processPayroll();
                break;
            case 'PUT':
                $this->markPaid();
                break;
            default:
                $this->error('Method not allowed', 405);
        }
    }

    public function getPayroll(): void
    {
        if (SessionManager::isAdmin() && empty($_GET['emp_id'])) {
            $this->success('Payroll retrieved successfully', $this->payrollService->getAll());
        }

        $empId = (!empty($_GET['emp_id']) && SessionManager::isAdmin())
            ? (int) $_GET['emp_id']
            : SessionManager::currentEmpId();

        $this->success('Payroll retrieved successfully', $this->payrollService->getByEmployee($empId));
    }

    public function processPayroll(): void
    {
        if (!SessionManager::isAdmin()) {
            $this->error('Admin access required.', 403);
        }

        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        if (empty($data['emp_id']) || empty($data['pay_period_start']) || empty($data['pay_period_end'])) {
            $this->error('emp_id, pay_period_start and pay_period_end are required.', 400);
        }

        try {
            $result = $this->payrollService->generatePayroll(
                (int) $data['emp_id'],
                $data['pay_period_start'],
                $data['pay_period_end']
            );
            $this->success('Payroll processed successfully', $result, 201);
        } catch (Exception $e) {
            $this->error($e->getMessage(), 400);
        }
    }

    public function markPaid(): void
    {
        if (!SessionManager::isAdmin()) {
            $this->error('Admin access required.', 403);
        }

        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        if (empty($data['pay_id'])) {
            $this->error('pay_id is required.', 400);
        }

        $updated = $this->payrollService->payEmployee((int) $data['pay_id']);

        if ($updated) {
            $this->success('Payroll marked as paid.');
        }

        $this->error('Failed to update payroll status.', 500);
    }
}
