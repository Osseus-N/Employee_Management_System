<?php

namespace admin;

use employee\employeeService;
use response\responseController;
use service\SessionManager;

class adminController extends responseController
{
    private adminService $adminService;
    private employeeService $employeeService;

    public function __construct(adminService $adminService, employeeService $employeeService)
    {
        $this->adminService = $adminService;
        $this->employeeService = $employeeService;
    }

    public function handleRequest(): void
    {
        SessionManager::init();

        if (!SessionManager::isAdmin()) {
            $this->error('Admin access required.', 403);
        }

        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'GET':
                if (!empty($_GET['dashboard'])) {
                    $this->getDashboard();
                } elseif (!empty($_GET['id'])) {
                    $this->getEmployeeById((int) $_GET['id']);
                } elseif (!empty(trim($_GET['search'] ?? ''))) {
                    $this->searchEmployee($_GET['search']);
                } else {
                    $this->getAllEmployees();
                }
                break;

            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true) ?? [];
                $this->createEmployee($data);
                break;

            case 'PUT':
                $data = json_decode(file_get_contents('php://input'), true) ?? [];
                $this->editEmployee($data);
                break;

            case 'DELETE':
                $data = json_decode(file_get_contents('php://input'), true) ?? [];
                $this->deleteEmployee($data);
                break;

            default:
                $this->error('Method not allowed', 405);
        }
    }

    public function getAllEmployees(): void
    {
        $employees = $this->employeeService->getAllEmployees();
        $this->success('Employees retrieved successfully', $employees);
    }

    public function getEmployeeById(int $id): void
    {
        $employee = $this->employeeService->getEmployee($id);

        if ($employee) {
            $this->success('Employee found', $employee);
        }

        $this->error('Employee not found', 404);
    }

    public function searchEmployee(string $term): void
    {
        $employees = $this->employeeService->searchEmployee($term);
        $this->success('Search results', $employees);
    }

    public function createEmployee(array $data): void
    {
        $email = trim((string) ($data['acc_email'] ?? ''));
        $password = (string) ($data['acc_password'] ?? '');
        $role = ($data['acc_role'] ?? 'employee') === 'admin' ? 'admin' : 'employee';

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('A valid login email is required.', 400);
        }
        if (strlen($password) < 6) {
            $this->error('Password must be at least 6 characters.', 400);
        }
        if ($this->adminService->emailExists($email)) {
            $this->error('That email is already in use by another account.', 409);
        }

        [$ok, $result] = $this->employeeService->createEmployee($data);

        if (!$ok) {
            $this->error(is_string($result) ? $result : 'Failed to create employee', 400);
        }

        $empId = (int) $result['emp_id'];
        $accountCreated = $this->adminService->createAccount($empId, $email, $password, $role);

        if (!$accountCreated) {
            $this->employeeService->deleteEmployee($empId);
            $this->error('Employee record created but login account failed — rolled back. Try again.', 500);
        }

        $this->success('Employee and login account created successfully', $result, 201);
    }

    public function editEmployee(array $data): void
    {
        if (empty($data['emp_id'])) {
            $this->error('Employee ID is required', 400);
        }

        [$ok, $error] = $this->employeeService->editEmployee((int) $data['emp_id'], $data);

        if ($ok) {
            $this->success('Employee successfully updated', ['emp_id' => $data['emp_id']]);
        }

        $this->error($error ?? 'Failed to update employee details.', 400);
    }

    public function deleteEmployee(array $data): void
    {
        if (empty($data['emp_id'])) {
            $this->error('Employee ID is required for deletion', 400);
        }

        $empId = (int) $data['emp_id'];
        $this->adminService->deleteAccountForEmployee($empId);
        $isDeleted = $this->employeeService->deleteEmployee($empId);

        if ($isDeleted) {
            $this->success('Employee deleted successfully');
        }

        $this->error('Employee not found or could not be deleted', 404);
    }

    public function getDashboard(): void
    {
        $counts = $this->adminService->getDashboardCounts();
        $this->success('Dashboard stats retrieved', $counts);
    }
}
