<?php

namespace employee;

use response\responseController;
use service\SessionManager;

/**
 * Self-service endpoint: a logged-in employee (or admin) can view/edit
 * only THEIR OWN record. Full CRUD over all employees lives in
 * admin\adminController.
 */
class employeeController extends responseController
{
    private employeeService $employeeService;

    public function __construct(employeeService $employeeService)
    {
        $this->employeeService = $employeeService;
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
                $this->getProfile();
                break;
            case 'PUT':
                $this->updateProfile();
                break;
            default:
                $this->error('Method not allowed', 405);
        }
    }

    public function getProfile(): void
    {
        $empId = SessionManager::currentEmpId();
        $employee = $this->employeeService->getEmployee($empId);

        if ($employee) {
            $this->success('Profile retrieved', $employee);
        }

        $this->error('Employee not found', 404);
    }

    public function updateProfile(): void
    {
        $empId = SessionManager::currentEmpId();
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $existing = $this->employeeService->getEmployee($empId);
        if (!$existing) {
            $this->error('Employee not found', 404);
        }

        // Self-service employees may only change contact info, not their
        // own position, rate, or status.
        $data['emp_firstname']      = $data['emp_firstname'] ?? $existing['emp_firstname'];
        $data['emp_lastname']       = $data['emp_lastname'] ?? $existing['emp_lastname'];
        $data['emp_gender']         = $existing['emp_gender'];
        $data['emp_date_of_birth']  = $existing['emp_date_of_birth'];
        $data['emp_position']       = $existing['emp_position'];
        $data['emp_hourly_rate']    = $existing['emp_hourly_rate'];
        $data['emp_status']         = $existing['emp_status'];

        [$ok, $error] = $this->employeeService->editEmployee($empId, $data);

        if ($ok) {
            $this->success('Profile updated successfully', ['emp_id' => $empId]);
        }

        $this->error($error ?? 'Failed to update profile', 400);
    }
}
