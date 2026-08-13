<?php

namespace payroll;

use attendance\attendanceService;
use employee\employeeService;
use response\responseController;
use session\SessionManager;

class payrollController extends responseController
{
    private payrollService $payrollService;
    private employeeService $employee;
    private attendanceService $attendance;

    public function __construct(
        payrollService $payrollService,
        employeeService $employeeService,
        attendanceService $attService
    ) {
        $this->payrollService = $payrollService;
        $this->employee = $employeeService;
        $this->attendance = $attService;
    }
    public function getAllUnpaidSchedules(): void
    {
        $schedules = $this->payrollService->getAllUnpaidSchedules();

        if (empty($schedules)) {
            $this->error('No unpaid payroll schedules found.',404);
        }

        $this->success('Unpaid payroll schedules retrieved successfully',$schedules);
    }

    public function createSchedule(): void
    {
        $data = json_decode(
            file_get_contents('php://input'),
            true
        ) ?? [];

        $startDate = $data['payroll_start_date'] ?? null;
        $endDate   = $data['payroll_end_date'] ?? null;


        if (empty($startDate) || empty($endDate)) {
            $this->error('Payroll start date and end date are required.');
        }

        if ($startDate > $endDate) {
            $this->error('Payroll start date cannot be later than the end date.');
        }

        $created = $this->payrollService->createSchedule($startDate,$endDate);

        if (!$created) {
            $this->error( 'Error creating payroll schedule.');
        }

        $this->success('Payroll schedule created successfully.');
    }

    public function payAllEmployees(): void
    {
        $data = json_decode(
            file_get_contents('php://input'),
            true) ?? [];

        $scheduleId = $data['schedule_id'] ?? null;

        if (empty($scheduleId)) {
            $this->error('Payroll schedule ID is required.');
        }

        $saved = $this->payrollService->payAllEmployees((int) $scheduleId);

        if (!$saved) {
            $this->error('Failed to process payroll schedule.');
        }

        $this->success('Payroll schedule processed successfully.');
    }

    public function updateSchedule(): void
    {
        $data = json_decode(
            file_get_contents('php://input'),
            true
        ) ?? [];

        $scheduleId = $data['schedule_id'] ?? null;
        $startDate  = $data['payroll_start_date'] ?? null;
        $endDate    = $data['payroll_end_date'] ?? null;

        if (empty($scheduleId) || empty($startDate) || empty($endDate)) {
            $this->error('Schedule ID, start date and end date are required.');
        }

        if ($startDate > $endDate) {
            $this->error('Payroll start date cannot be later than the end date.');
        }

        $updated = $this->payrollService->updateSchedule((int) $scheduleId,$startDate,$endDate);

        if (!$updated) {
            $this->error('Failed to update payroll schedule.');
        }

        $this->success('Payroll schedule updated successfully.');
    }

    public function deleteSchedule(): void
    {
        $scheduleId = $_GET['id'] ?? null;

        if (empty($scheduleId)) {
            $this->error('Schedule ID is required.');
        }

        $deleted = $this->payrollService->deleteSchedule( (int) $scheduleId);

        if (!$deleted) {
            $this->error(
                'Failed to delete payroll schedule.');
        }

        $this->success('Payroll schedule deleted successfully.');
    }
}