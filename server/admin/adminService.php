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

        if (empty($data['emp_firstname']) || empty($data['emp_lastname']) || empty($data['user_email']) || empty($data['user_password'])) {
            throw new \InvalidArgumentException("Missing mandatory fields: firstname, lastname, email, or password.");
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email format.");
        }

        if (strlen($data['password']) < 8) {
            throw new \InvalidArgumentException("Password must be at least 8 characters.");
        }

        $employee = new Employee(
            $data['firstname'],
            $data['lastname'],
            $data['gender'],
            $data['position'],
            (float) ($data['hourly_rate'] ?? 0.00),
            $data['dob'] ?? null,
            $data['contact'] ?? null
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

    public function createDefaultAdmin()
    {
        $defaultAdminEmployee = new Employee(
            empFirstname:      'Admin',
            empLastname:       'Employee',
            empGender:         'Other',
            empPosition:       'admin',
            empHourlyRate:     0.00,
            empDateOfBirth:    '2000-01-01',
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

        header('Location:index.php');
    }
}