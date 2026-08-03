<?php

namespace login;

class loginService
{
    private loginRepository $repository;

    public function __construct(loginRepository $repository)
    {
        $this->repository = $repository;
    }
    public function authenticate(string $email, string $password): ?array
    {
        $account = $this->repository->findByEmail($email);

        if (!$account) {
            return null;
        }

        if (!password_verify($password, $account['acc_password'])) {
            return null;
        }

        if ($account['emp_status'] !== 'Active') {
            return null;
        }

        unset($account['acc_password']);

        return $account;
    }
}
