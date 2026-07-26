<?php

namespace login;

class logInService
{
    private logInRepository $repository;
    public function __construct(logInRepository $logInRepository ){
        $this->repository = $logInRepository;
    }
    public function authenticateAccount(int $emp_id , mixed $email, mixed $password){

        if($this->repository->isAccountExist($emp_id)){

            $user =$this->repository->logIn($emp_id);

            if(!password_verify($password, $user['password'])){
                return false;
            }

            return true;
        }
    }


}