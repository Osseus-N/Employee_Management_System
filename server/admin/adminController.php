<?php

namespace admin;

use employee\employeeService;
use payroll\payrollService;
use response\responseController;
use session\sessionManager;

class adminController extends responseController
{
    private $adminService;
    private $payrollService;
    private $employeeService;

    public function __construct(
        adminService $adminService,
        payrollService $payrollService,
        employeeService $employeeService
    ) {
        $this->adminService = $adminService;
        $this->payrollService = $payrollService;
        $this->employeeService = $employeeService;
    }

    // Serves the Admin HTML View
    public function showDashboard(): void
    {
        SessionManager::init();
        $emp_id = SessionManager::isLoggedIn();

        if (!$emp_id) {
            header("Location: /employee_management_system/login");
            exit;
        }

        if (($_SESSION['role'] ?? null) !== 'Admin') {
            header("Location: /employee_management_system/employee");
            exit;
        }

        header("Content-Type: text/html; charset=UTF-8");
        include __DIR__ . '/../../client/view/admin_view.html';
    }

    public function getAllEmployee(): void
    {
        $employees = $this->adminService->getAllEmployees();

        if (!empty($employees)) {
            $this->success('Employees retrieved successfully', $employees);
        }

        $this->error('No employees found');
    }

    public function getEmployeeById($id = null): void
    {
        $id = $id ?? $_GET['id'] ?? null;

        if (empty($id)) {
            $this->error('Employee ID is required', 400);
            return;
        }

        $employee = $this->employeeService->getEmployee($id);

        if ($employee) {
            $this->success('Employee found', $employee);
            return;
        }

        $this->error('Employee not found', 404);
    }

    public function searchEmployee(string $searchTerm = ''): void
    {
        $searchTerm = !empty($searchTerm) ? $searchTerm : ($_GET['search'] ?? '');

        if (empty(trim($searchTerm))) {
            $this->error('Search query is required', 400);
            return;
        }

        $employees = $this->adminService->searchEmployee($searchTerm);

        if (!empty($employees)) {
            $this->success('Employees found', $employees);
            return;
        }

        $this->success('No employees matching criteria', []);
    }

    /**
     * @throws \Exception
     */
    public function createEmployee(array $data = []): void
    {
        if (empty($data)) {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
        }

        // Basic required field validation
        if (empty($data['emp_firstname']) || empty($data['emp_lastname']) || empty($data['acc_email'])) {
            $this->error('Missing required fields: firstname, lastname, email.', 400);
            return;
        }

        $created = $this->adminService->createEmployee($data);

        if ($created) {
            $this->success('Employee created successfully', $created, 201);
            return;
        }

        $this->error('Failed to create employee', 500);
    }

    public function editEmployee(): void
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        if (empty($data['emp_id'])) {
            $this->error('Employee ID is required', 400);
            return;
        }

        $updated = $this->adminService->editEmployee($data['emp_id'], $data);

        if ($updated) {
            $this->success('Employee successfully updated', ['emp_id' => $data['emp_id']]);
        }

        $this->error('Failed to update employee details.');
    }

    public function deleteEmployee(): void
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        // Fixed bug: checks if emp_id is missing
        if (empty($data['emp_id'])) {
            $this->error('Employee ID is required for deletion', 400);
            return;
        }

        $isDeleted = $this->adminService->deleteEmployee($data['emp_id']);

        if ($isDeleted) {
            $this->success('Employee deleted successfully');
            return;
        }

        $this->error('Employee not found or could not be deleted', 404);
    }

    public function payEmployee(array $data = []): void
    {
        if (empty($data)) {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
        }

        if (empty($data['emp_id']) || empty($data['amount'])) {
            $this->error('Employee ID and payment amount are required.', 400);
            return;
        }

        $isPaid = $this->payrollService->payEmployee($data['emp_id'], $data['amount']);

        if ($isPaid) {
            $this->success('Payment processed successfully', [
                'emp_id' => $data['emp_id'],
                'amount' => $data['amount']
            ]);
            return;
        }

        $this->error('Failed to process payment.');
    }
}