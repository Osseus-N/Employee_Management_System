<?php

namespace employee;

use response\responseController;
use service\SessionManager;

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
        SessionManager::isLoggedIn();

        switch ($_SERVER["REQUEST_METHOD"]) {

            case "GET":
                $this->getEmployee();
                break;

            case "PUT":
                $this->updateEmployee();
                break;

            default:
                $this->error("Method Not Allowed", 405);
        }
    }

    /**
     * Get logged-in employee
     */
    private function getEmployee(): void
    {
        $empId = SessionManager::getEmpId();

        $employee = $this->employeeService->getEmployee($empId);

        if (!$employee) {
            $this->error("Employee not found", 404);
        }

        $this->success(
            "Employee retrieved successfully",
            $employee
        );
    }

    /**
     * Update logged-in employee
     */
    private function updateEmployee(): void
    {
        $empId = SessionManager::getEmpId();

        $data = json_decode(
            file_get_contents("php://input"),
            true
        );

        if (!$data) {
            $this->error("No data received", 400);
        }

        $updated = $this->employeeService->updateEmployee(
            $empId,
            $data
        );

        if (!$updated) {
            $this->error("Unable to update employee", 400);
        }

        $employee = $this->employeeService->getEmployee($empId);

        $this->success(
            "Employee updated successfully",
            $employee
        );
    }
}