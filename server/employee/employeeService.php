<?php

namespace employee;

class employeeService
{
    private employeeRepository $employeeRepository;

    public function __construct(employeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function getEmployee($emp_id)
    {
        return $this->employeeRepository->getEmployee($emp_id);
    }

    public function createEmployee(array $data)
    {
        return $this->employeeRepository->createEmployee($data);
    }

    public function editEmployee($emp_id, array $data)
    {
        return $this->employeeRepository->editEmployee(
            $data,
            ["emp_id" => $emp_id]
        );
    }

    public function deleteEmployee($emp_id)
    {
        return $this->employeeRepository->deleteEmployee($emp_id);
    }
}