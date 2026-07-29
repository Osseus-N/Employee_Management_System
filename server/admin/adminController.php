<?php

namespace admin;

use attendance\attendanceService;
use employee\employeeService;
use payroll\payrollService;
use response\responseController;
use service\SessionManager;

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

    public function handleRequest(): void
    {
        SessionManager::init();

        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'GET':
                if (isset($_GET['id']) && !empty($_GET['id'])) {
                    $this->getEmployeeById($_GET['id']);
                } else if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
                    $this->searchEmployee($_GET['search']);
                } else {
                    $this->getAllEmployee();
                }
                break;

            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true) ?? [];

                if (isset($data['action']) && $data['action'] === 'pay') {
                    $this->payEmployee($data);
                } else {
                    $this->createEmployee($data);
                }
                break;

            case 'PUT':
                $this->editEmployee();
                break;

            case 'DELETE':
                $this->deleteEmployee();
                break;

            default:
                $this->error('Method not allowed', 405);
                break;
        }
    }

    public function getAllEmployee(): void
    {
        $employees = $this->adminService->getAllEmployee();

        if (!empty($employees)) {
            $this->success('Employees retrieved successfully', $employees);
        }

        $this->success('No employees found', []);
    }

    public function getEmployeeById($id): void
    {
        $employee = $this->employeeService->getEmployee($id);

        if ($employee) {
            $this->success('Employee found', $employee);
        }

        $this->error('Employee not found', 404);
    }

    public function createEmployee(array $data = []): void
    {
        // Basic required field validation
        if (empty($data['firstname']) || empty($data['lastname']) || empty($data['email'])) {
            $this->error('Missing required fields: firstname, lastname, email.', 400);
        }

        $created = $this->employeeService->createEmployee($data);

        ($created) ? $this->success('Employee created successfully', $created, 201)
                    :$this->error('Failed to create employee', 500);
    }

    public function searchEmployee(string $searchTerm): void
    {
        $employees = $this->adminService->searchEmployee($searchTerm);

        (!empty($employees)) ? $this->success('Employees found', $employees)
                :$this->success('No employees matching criteria', []);
    }

    public function deleteEmployee(): void
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        if (empty($data['emp_id'])) {
            $this->error('Employee ID is required for deletion', 400);
        }

        $isDeleted = $this->adminService->deleteEmployee($data['emp_id']);

        ($isDeleted) ? $this->success('Employee deleted successfully')
                    :$this->error('Employee not found or could not be deleted', 404);
    }

    public function payEmployee(array $data = []): void
    {
        if (empty($data['emp_id']) || empty($data['amount'])) {
            $this->error('Employee ID and payment amount are required.', 400);
        }

        $isPaid = $this->payrollService->payEmployee($data['emp_id'], $data['amount']);

        ($isPaid) ? $this->success('Payment processed successfully' , ['emp_id' => $data['emp_id'],
            'amount' => $data['amount']])
            :$this->error('Failed to process payment.');
    }

    public function editEmployee(): void
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        if (empty($data['emp_id'])) {
            $this->error('Employee ID is required', 400);
        }

        $updated = $this->employeeService->editEmployee($data['emp_id'], $data);

        ($updated) ? $this->success('Employee successfully updated', [
            'emp_id' => $data['emp_id']])
            : $this->error('Failed to update employee detailes.');
    }
}