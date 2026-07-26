<?php

namespace login;

class logInService
{
    private logInRepository $repository;
    public function __construct(logInRepository $logInRepository ){
        $this->repository = $logInRepository;
    }
    public function authenticateAccount(mixed $email, mixed $password){

        $acc = $this->repository->isAccountExist($email);

        if($acc){
            $user =$this->repository->logIn($acc['emp_id']);

            if(!password_verify($password, $user['password'])){
                return false;
            }

            return $user;
        }
        return false;
    }



}