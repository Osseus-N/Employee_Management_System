<?php

namespace admin;

class adminService
{
    private adminRepository $repository;

    public function __construct(adminRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getDashboardCounts(): array
    {
        return $this->repository->getDashboardCounts();
    }

    public function emailExists(string $email): bool
    {
        return $this->repository->emailExists($email);
    }

    public function createAccount(int $empId, string $email, string $password, string $role): bool
    {
        return $this->repository->createAccount($empId, $email, $password, $role);
    }

    public function deleteAccountForEmployee(int $empId): bool
    {
        return $this->repository->deleteAccountForEmployee($empId);
    }
}
