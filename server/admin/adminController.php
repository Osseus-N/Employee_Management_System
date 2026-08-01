<?php

namespace admin;

use response\responseController;
use service\SessionManager;

class adminController extends responseController
{
    private adminService $adminService;

    public function __construct(adminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function handleRequest(): void
    {
        SessionManager::init();
        SessionManager::isLoggedIn();

        switch ($_SERVER["REQUEST_METHOD"]) {

            case "GET":
                $this->handleGet();
                break;

            case "POST":
                $this->createEmployee();
                break;

            case "PUT":
                $this->updateEmployee();
                break;

            case "DELETE":
                $this->deleteEmployee();
                break;

            default:
                $this->error("Method Not Allowed", 405);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | GET
    |--------------------------------------------------------------------------
    */

    private function handleGet(): void
    {
        // Get employee by ID
        if (isset($_GET["emp_id"])) {

            $employee = $this->adminService->getEmployeeById(
                (int)$_GET["emp_id"]
            );

            if (!$employee) {
                $this->error("Employee not found", 404);
            }

            $this->success(
                "Employee retrieved successfully",
                $employee
            );
        }

        // Search employee
        if (isset($_GET["search"])) {

            $employees = $this->adminService->searchEmployee(
                $_GET["search"]
            );

            $this->success(
                "Search completed",
                $employees
            );
        }

        // Get all employees
        $employees = $this->adminService->getAllEmployees();

        $this->success(
            "Employees retrieved successfully",
            $employees
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    private function createEmployee(): void
    {
        $data = json_decode(
            file_get_contents("php://input"),
            true
        );

        if (!$data) {
            $this->error("Invalid request body", 400);
        }

        $success = $this->adminService->createEmployee($data);

        if (!$success) {
            $this->error("Unable to create employee");
        }

        $this->success(
            "Employee created successfully"
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    private function updateEmployee(): void
    {
        $data = json_decode(
            file_get_contents("php://input"),
            true
        );

        if (!isset($data["emp_id"])) {
            $this->error(
                "Employee ID is required",
                400
            );
        }

        $success = $this->adminService->updateEmployee(
            (int)$data["emp_id"],
            $data
        );

        if (!$success) {
            $this->error(
                "Unable to update employee"
            );
        }

        $employee = $this->adminService->getEmployeeById(
            (int)$data["emp_id"]
        );

        $this->success(
            "Employee updated successfully",
            $employee
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    private function deleteEmployee(): void
    {
        $data = json_decode(
            file_get_contents("php://input"),
            true
        );

        if (!isset($data["emp_id"])) {
            $this->error(
                "Employee ID is required",
                400
            );
        }

        $success = $this->adminService->deleteEmployee(
            (int)$data["emp_id"]
        );

        if (!$success) {
            $this->error(
                "Unable to delete employee"
            );
        }

        $this->success(
            "Employee deleted successfully"
        );
    }
}