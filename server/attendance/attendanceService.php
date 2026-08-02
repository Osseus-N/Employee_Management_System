<?php

namespace attendance;

use Exception;

class attendanceService
{
    private attendanceRepository $repository;

    public function __construct(attendanceRepository $repository)
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
    public function clockIn(int $empId): array
    {
        $date = date('Y-m-d');
        $time = date('Y-m-d H:i:s');

        if ($this->repository->findTodayRecord($empId, $date)) {
            throw new Exception('Already clocked in today.');
        }

        $id = $this->repository->clockIn($empId, $date, $time);

        return ['att_id' => $id, 'att_work_date' => $date, 'att_clock_in' => $time];
    }

    /** @throws Exception */
    public function clockOut(int $empId): array
    {
        $date = date('Y-m-d');
        $record = $this->repository->findTodayRecord($empId, $date);

        if (!$record) {
            throw new Exception('No clock-in record found for today.');
        }
        if ($record['att_clock_out']) {
            throw new Exception('Already clocked out today.');
        }

        $timeOut = date('Y-m-d H:i:s');
        $hours = round((strtotime($timeOut) - strtotime($record['att_clock_in'])) / 3600, 2);

        $this->repository->clockOut((int) $record['att_id'], $timeOut, $hours);

        return ['att_clock_out' => $timeOut, 'att_total_hours' => $hours];
    }
}
