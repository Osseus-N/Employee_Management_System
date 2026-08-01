<?php

namespace payroll;

use attendance\attendanceRepository;
use DateTime;

class payrollService
{
    private payrollRepository $payrollRepo;
    private attendanceRepository $attendanceRepo;
    public function __construct(payrollRepository $payrollRepository ,
                                attendanceRepository $attendanceRepo){
        $this->payrollRepo = $payrollRepository;
        $this->attendanceRepo = $attendanceRepo;
    }

    public function getMonthlyPayroll($emp_Id){

        if(empty($emp_Id)) {
            return false;
        }

        return $this->payrollRepo->getMonthlyPayroll($emp_Id);
    }

    public function payEmployee(int $emp_id, int $hourly_rate, int $presentDays)
    {
        if(empty($emp_id) || $hourly_rate <= 0 || $presentDays <= 0){
            return false;
        }
        $first_day = $this->attendanceRepo->getFirstUnpaidDate($emp_id);
        $last_day =$this->attendanceRepo->getLastUnpaidDate($emp_id);
        $amount = $hourly_rate * $presentDays;

        $data = ['emp_id'=> $emp_id,
            'pay_period_start' => $first_day,
            'pay_period_end' => $last_day,
            'pay_total_days' => $presentDays,
            'pay_status' => 'Paid',
            'pay_amount' => $amount];

        return $this->payrollRepo->payEmployee($emp_id, $data);
    }
}