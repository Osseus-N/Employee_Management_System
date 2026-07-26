<?php

namespace employee;

require_once __DIR__ . "/employeeRepository.php";

class employeeService
{
    private employeeRepository $repository;

    public function __construct(employeeRepository $repository)
    {
        $this->repository = $repository;
    }

    // GET ALL EMPLOYEES
    public function getEmployees()
    {
        return $this->repository->getEmployees();
    }

    // GET ONE EMPLOYEE
    public function getEmployeeById($id)
    {
        return $this->repository->getEmployeeById($id);
    }

    // ADD EMPLOYEE
    public function addEmployee(array $employee)
    {
        if (
            empty($employee['emp_firstname']) ||
            empty($employee['emp_lastname']) ||
            empty($employee['emp_gender']) ||
            empty($employee['emp_date_of_birth']) ||
            empty($employee['emp_position']) ||
            $employee['emp_hourly_rate'] === ""
        ) {
            return [
                "success" => false,
                "message" => "Please complete all required fields."
            ];
        }

        $success = $this->repository->addEmployee($employee);

        return [
            "success" => $success,
            "message" => $success
                ? "Employee added successfully."
                : "Failed to add employee."
        ];
    }

    // UPDATE EMPLOYEE
    public function updateEmployee(array $employee)
    {
        if (empty($employee['emp_id'])) {
            return [
                "success" => false,
                "message" => "Employee ID is required."
            ];
        }

        $id = $employee['emp_id'];
        unset($employee['emp_id']);

        $success = $this->repository->updateEmployee($id, $employee);

        return [
            "success" => $success,
            "message" => $success
                ? "Employee updated successfully."
                : "Failed to update employee."
        ];
    }

    // DELETE EMPLOYEE
    public function deleteEmployee($id)
    {
        if (empty($id)) {
            return [
                "success" => false,
                "message" => "Employee ID is required."
            ];
        }

        $success = $this->repository->deleteEmployee($id);

        return [
            "success" => $success,
            "message" => $success
                ? "Employee deleted successfully."
                : "Failed to delete employee."
        ];
    }
}