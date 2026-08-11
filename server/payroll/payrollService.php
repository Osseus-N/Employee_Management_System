<?php

namespace payroll;

use attendance\attendanceRepository;

class payrollService
{
    private payrollRepository $payrollRepo;
    private attendanceRepository $attendanceRepo;

    public function __construct(
        payrollRepository $payrollRepo,
        attendanceRepository $attendanceRepo
    ) {
        $this->payrollRepo = $payrollRepo;
        $this->attendanceRepo = $attendanceRepo;
    }

    public function getMonthlyPayroll($emp_id): false|array
    {
        if (empty($emp_id)) {
            return false;
        }

        return $this->payrollRepo->getMonthlyPayroll($emp_id);
    }

    public function createSchedule(string $startDate,string $endDate){

        if (empty($startDate) || empty($endDate)) {
            return false;
        }

        $latestEndDate = $this->payrollRepo->getLastEndDate();

        if($startDate <= $latestEndDate){
            return false;
        }
        return $this->payrollRepo->createSchedule(
            $startDate,
            $endDate
        );
    }

    public function getAllUnpaidSchedules(): array
    {
        return $this->payrollRepo->getAllUnpaidSchedules();
    }

    public function payAllEmployees(int $scheduleId): bool|array
    {
        if ($scheduleId <= 0 || empty($scheduleId)) {
            return false;
        }

        $schedule = $this->payrollRepo->getScheduleById($scheduleId);

        if (empty($schedule) || $schedule['status'] !== 'Open') {
            return false;
        }

        $startDate = $schedule['payroll_start_date'];
        $endDate   = $schedule['payroll_end_date'];

        if($endDate > date('Y-m-d')){
            return false;
        }
        $employees = $this->payrollRepo->getActiveEmployees();

        if (empty($employees)) {
            return false;
        }

        foreach ($employees as $employee) {
            $empId = (int) $employee['emp_id'];
            $hourlyRate = (float) $employee['emp_hourly_rate'];

            $presentDays = $this->attendanceRepo->presentDaysBetween(
                $empId,
                $startDate,
                $endDate
            );

            if ($presentDays <= 0) {
                continue;
            }

            $amount = $hourlyRate * $presentDays;

            $payrollData = [
                'emp_id'           => $empId,
                'schedule_id'      => $scheduleId,
                'pay_total_days'   => $presentDays,
                'pay_status'       => 'Paid',
                'pay_amount'       => $amount
            ];

            $saved = $this->payrollRepo->createPayroll($payrollData);

            if (!$saved) {
                return false;
            }
        }

        return $this->payrollRepo->markScheduleProcessed($scheduleId);
    }

    public function updateSchedule(
        int $scheduleId,
        string $startDate,
        string $endDate
    ): bool {
        if ($scheduleId <= 0) {
            return false;
        }

        if (empty($startDate) || empty($endDate)) {
            return false;
        }

        if ($startDate > $endDate) {
            return false;
        }

        return $this->payrollRepo->updateSchedule(
            $scheduleId,
            $startDate,
            $endDate
        );
    }

    public function deleteSchedule(int $scheduleId): bool
    {
        if ($scheduleId <= 0) {
            return false;
        }

        $schedule = $this->payrollRepo->getScheduleById($scheduleId);

        if (!$schedule) {
            return false;
        }

        return $this->payrollRepo->deleteSchedule(
            $scheduleId
        );
    }
}