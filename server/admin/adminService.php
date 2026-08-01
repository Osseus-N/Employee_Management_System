<?php

namespace admin;

use model\Employee;

class adminService
{
    private adminRepository $adminRepository;

    public function __construct(adminRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    /*
    |--------------------------------------------------------------------------
    | Get All Employees
    |--------------------------------------------------------------------------
    */

    public function getAllEmployees(): array
    {
        return $this->adminRepository->getAllEmployees();
    }

    /*
    |--------------------------------------------------------------------------
    | Get One Employee
    |--------------------------------------------------------------------------
    */

    public function getEmployeeById(int $empId): ?array
    {
        return $this->adminRepository->getEmployeeById($empId);
    }

    /*
    |--------------------------------------------------------------------------
    | Search Employee
    |--------------------------------------------------------------------------
    */

    public function searchEmployee(string $keyword): array
    {
        return $this->adminRepository->searchEmployee(trim($keyword));
    }

    /*
    |--------------------------------------------------------------------------
    | Create Employee
    |--------------------------------------------------------------------------
    */

    public function createEmployee(array $data): bool
    {
        $employee = new Employee(
            trim($data["emp_firstname"]),
            trim($data["emp_lastname"]),
            $data["emp_gender"],
            trim($data["emp_position"]),
            (float)$data["emp_hourly_rate"],
            $data["emp_date_of_birth"] ?? null,
            $data["emp_contact_number"] ?? null,
            null,
            $data["emp_status"] ?? "Active"
        );

        return $this->adminRepository->createEmployee($employee);
    }

    /*
    |--------------------------------------------------------------------------
    | Update Employee
    |--------------------------------------------------------------------------
    */

    public function updateEmployee(int $empId, array $data): bool
    {
        $updateData = [];

        if (isset($data["emp_firstname"])) {
            $updateData["emp_firstname"] = trim($data["emp_firstname"]);
        }

        if (isset($data["emp_lastname"])) {
            $updateData["emp_lastname"] = trim($data["emp_lastname"]);
        }

        if (isset($data["emp_gender"])) {
            $updateData["emp_gender"] = $data["emp_gender"];
        }

        if (isset($data["emp_date_of_birth"])) {
            $updateData["emp_date_of_birth"] = $data["emp_date_of_birth"];
        }

        if (isset($data["emp_contact_number"])) {
            $updateData["emp_contact_number"] = trim($data["emp_contact_number"]);
        }

        if (isset($data["emp_position"])) {
            $updateData["emp_position"] = trim($data["emp_position"]);
        }

        if (isset($data["emp_hourly_rate"])) {
            $updateData["emp_hourly_rate"] = (float)$data["emp_hourly_rate"];
        }

        if (isset($data["emp_status"])) {
            $updateData["emp_status"] = $data["emp_status"];
        }

        if (empty($updateData)) {
            return false;
        }

        return $this->adminRepository->updateEmployee(
            $empId,
            $updateData
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Employee
    |--------------------------------------------------------------------------
    */

    public function deleteEmployee(int $empId): bool
    {
        return $this->adminRepository->deleteEmployee($empId);
    }
}