<?php

namespace employee;

class employeeService
{
    private employeeRepository $employeeRepository;

    public function __construct(employeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    /**
     * Get logged-in employee
     */
    public function getEmployee(int $empId): ?array
    {
        return $this->employeeRepository->getEmployee($empId);
    }

    /**
     * Update logged-in employee
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

        return $this->employeeRepository->updateEmployee(
            $empId,
            $updateData
        );
    }
}