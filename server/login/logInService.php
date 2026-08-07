<?php

namespace login;

use employee\employeeRepository;

class logInService
{
    private logInRepository $logInRepository;
    private EmployeeRepository $employeeRepository;
    public function __construct(logInRepository $logInRepository, employeeRepository $employeeRepository){
        $this->logInRepository = $logInRepository;
        $this->employeeRepository = $employeeRepository;
    }
    public function authenticateAccount(mixed $email, mixed $password): ?array
    {

        $acc = $this->logInRepository->isAccountExist($email);

        if (!$acc) {
            return null;
        }

        $user =$this->employeeRepository->getEmployee($acc['emp_id']);

        if (!$user || !isset($acc['acc_password'])) {
            return null;
        }

        if(!password_verify($password, $acc['acc_password'])){
            return null;
        }

        $user['role'] = $acc['acc_role'];
        unset($user['password']);

        return $user;
    }



}