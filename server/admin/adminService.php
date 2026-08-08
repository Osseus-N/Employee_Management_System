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

        if (strlen($data['password']) < 8) {
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
         return $this->adminRepository->createEmployee($employee, trim($data['email']), $data['password']);
    }
    public function editEmployee(mixed $data)
    {
        if (empty($empId) || empty($data)) {
            return false;
        }

        $updateFields = [];

        if (!empty($data['firstname']))      $updateFields['emp_firstname']      = $data['firstname'];
        if (!empty($data['lastname']))       $updateFields['emp_lastname']       = $data['lastname'];
        if (!empty($data['gender']))         $updateFields['emp_gender']         = $data['gender'];
        if (!empty($data['position']))       $updateFields['emp_position']       = $data['position'];
        if (isset($data['hourly_rate']))     $updateFields['emp_hourly_rate']    = (float) $data['hourly_rate'];
        if (isset($data['dob']))             $updateFields['emp_date_of_birth']  = $data['dob'];
        if (isset($data['contact_number']))  $updateFields['emp_contact_number'] = $data['contact_number'];
        if (isset($data['status']))          $updateFields['emp_status']         = $data['status'];

        if (empty($updateFields)) {
            return false;
        }

        $where = ['emp_id' => $empId];

        return $this->adminRepository->updateEmployee($updateFields, $where, 'employees');
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
            empPosition:       'admin',
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

        header('Location: /employee_management_system/');
    }
}