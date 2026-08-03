<?php

namespace admin;

use employee\employeeService;
use model\Employee;

class adminService
{
    private adminRepository $adminRepository;
    public function __construct(adminRepository $adminRepository){
    $this->adminRepository = $adminRepository;
    }

    public function getAllEmployee(){

    }
    public function createEmployee(array $requestData)
    {
        // Create domain object
        $employee = new Employee(
            $requestData['firstname'],
            $requestData['lastname'],
            $requestData['gender'],
            $requestData['position'],
            (float) ($requestData['hourly_rate'] ?? 0.00),
            $requestData['dob'] ?? null,
            $requestData['contact'] ?? null
        );

        $email = $requestData['email'];
        $password = $requestData['password']; // Raw password string from request

        return $this->repository->registerEmployeeWithAccount($employee, $email, $password);
    }
    public function editEmployee(mixed $data)
    {
        $emp = $this->employeeService->getEmployee($data['emp_id']);

        if($emp){

        }
    }



    public function deleteEmployee(mixed $emp_id)
    {
    }

    public function searchEmployee(mixed $search)
    {
    }
}