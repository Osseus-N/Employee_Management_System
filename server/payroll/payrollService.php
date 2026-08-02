<?php

namespace payroll;

use Exception;

class payrollService
{
    private payrollRepository $repository;

    public function __construct(payrollRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAll(): array
    {
        return $this->repository->findAll();
    }

    public function getByEmployee(int $empId): array
    {
        return $this->repository->findByEmployee($empId);
    }

    /** @throws Exception */
    public function generatePayroll(int $empId, string $start, string $end): array
    {
        if ($this->repository->existsForPeriod($empId, $start, $end)) {
            throw new Exception('Payroll for this period already exists.');
        }

        $hours = $this->repository->sumHours($empId, $start, $end);
        if ($hours <= 0) {
            throw new Exception('No attendance hours found for this period.');
        }

        $rate = $this->repository->getHourlyRate($empId);
        $amount = round($hours * $rate, 2);
        $id = $this->repository->create($empId, $start, $end, $hours, $amount);

        return [
            'pay_id'           => $id,
            'pay_total_hours'  => $hours,
            'pay_amount'       => $amount,
            'pay_period_start' => $start,
            'pay_period_end'   => $end,
        ];
    }

    public function payEmployee(int $payId): bool
    {
        return $this->repository->markPaid($payId);
    }
}
