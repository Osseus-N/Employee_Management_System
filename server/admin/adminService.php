<?php

namespace admin;

use employee\employeeRepository;
use employee\employeeService;
use model\Employee;

class adminService
{
    private adminRepository $adminRepository;
    private employeeRepository $employeeRepository;
    public function __construct(adminRepository $adminRepository, employeeRepository $employeeRepository){
        $this->employeeRepository = $employeeRepository;
        $this->adminRepository = $adminRepository;
    }

    public function getAllEmployees(){
        return $this->adminRepository->getAllEmployees();
    }
    public function searchEmployee(string $search):array{

        $cleanSearch = trim($search);

        if (empty($cleanSearchTerm)) {
            return [];
        }

        return $this->adminRepository->searchEmployee($cleanSearchTerm);
    }

    /**
     * @throws \Exception
     */
    public function createEmployee(array $data)
    {

        if($this->adminRepository->emailExists($data['acc_email'])){
            throw new \Exception("Email Already Exists");
        }

        if (empty($data['emp_firstname']) || empty($data['emp_lastname']) || empty($data['acc_email']) || empty($data['acc_password'])) {
            throw new \InvalidArgumentException("Missing mandatory fields: firstname, lastname, email, or password.");
        }

        if (!filter_var($data['acc_email'], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email format.");
        }

        if (strlen($data['acc_password']) < 8) {
            throw new \InvalidArgumentException("Password must be at least 8 characters.");
        }

        $employee = new Employee(
            $data['emp_firstname'] ,
            $data['emp_lastname'] ,
            $data['emp_gender'] ?? '',
            $data['emp_position'] ?? '',
            (float) ($data['emp_hourly_rate'] ?? 0.00),
            $data['emp_date_of_birth'] ?? null,
            $data['emp_address'] ?? null,
            $data['emp_contact_number'] ?? null,
            $data['emp_status'] ?? 'Active',
        );
         return $this->adminRepository->createEmployee($employee, trim($data['acc_email']), $data['acc_password']);
    }
    public function editEmployee($empId, mixed $data)
    {
        if (empty($empId) || empty($data)) {
            return false;
        }

        $accData = ['acc_email' => $data['acc_email']];

        if($data['acc_password']) {
            $hashedPassword = password_hash($data['acc_password'], PASSWORD_DEFAULT);
            $accData['acc_password'] = $hashedPassword;
        }

        unset(
            $data['acc_email'],
            $data['acc_password'],
        );
        return $this->employeeRepository->editEmployee($data, $accData ,$empId);
    }

    public function deleteEmployee(mixed $emp_id){

        if(empty($emp_id)){
            return false;
        }

        $this->adminRepository->deleteEmployee($emp_id);
        return true;
    }

    /**
     * @throws \Exception
     */
    public function createDefaultAdmin()
    {
        $defaultAdminEmployee = new Employee(
            empFirstname:      'Admin',
            empLastname:       'Employee',
            empGender:         'Other',
            empPosition:       'Admin',
            empHourlyRate:     0.00,
            empDateOfBirth:    '2000-01-01',
            empAddress:        'Manila City',
            empContactNumber:  '0123456789',
            empStatus:         'Active',
        );

        $defaultAdminEmail = 'admin@gmail.com';
        $defaultAdminPassword = 'ChangeMe123!';

        $this->adminRepository->createEmployee(
            $defaultAdminEmployee,
            $defaultAdminEmail,
            $defaultAdminPassword
        );

    }
}