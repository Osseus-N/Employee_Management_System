<?php

namespace employee;

use model\Employee;

class employeeService
{
    private employeeRepository $repository;

    public function __construct(employeeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllEmployees(): array
    {
        return $this->repository->findAll();
    }

    public function getEmployee(int $id): ?array
    {
        return $this->repository->findById($id);
    }

    public function searchEmployee(string $term): array
    {
        return $this->repository->search($term);
    }

    /** @return array{0: bool, 1: array|string} [success, employeeArray|errorMessage] */
    public function createEmployee(array $data): array
    {
        $model = Employee::fromArray($data);
        $errors = $model->validate();

        if (!empty($errors)) {
            return [false, implode(' ', $errors)];
        }

        $id = $this->repository->create($data);
        $created = $this->repository->findById($id);

        return [true, $created];
    }

    /** @return array{0: bool, 1: string|null} [success, errorMessage] */
    public function editEmployee(int $id, array $data): array
    {
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return [false, 'Employee not found.'];
        }

        $merged = array_merge($existing, $data);
        $model = Employee::fromArray($merged);
        $errors = $model->validate();

        if (!empty($errors)) {
            return [false, implode(' ', $errors)];
        }

        $ok = $this->repository->update($id, $merged);

        return [$ok, $ok ? null : 'Database update failed.'];
    }

     {
  public function deleteEmployee(int $id): bool
         return $this->repository->delete($id);
    }
}
