<?php

namespace login;

class logInService
{
    private logInRepository $repository;
    public function __construct(logInRepository $logInRepository){
        $this->repository = $logInRepository;
    }
    public function authenticateAccount(mixed $email, mixed $password){
    }


}