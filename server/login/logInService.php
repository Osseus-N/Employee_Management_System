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
    public function authenticateAccount(mixed $email, mixed $password){

        $acc = $this->logInRepository->isAccountExist($email);

        if($acc){

            $user =$this->employeeRepository->getEmployee($acc['emp_id']);

            if(!password_verify($password, $user['password'])){
                return null;
            }

            $user['role'] = $acc['acc_role'];
            unset($user['password']);

            return $user;
        }
        return null;
    }



}