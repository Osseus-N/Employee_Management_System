<?php

namespace payroll;

class payrollService
{

    public function getMonthlyPayroll(){

    }

    public function payEmployee(int $emp_id, int $hourly_rate, int $presentDays)
    {
        $amount = $hourly_rate * $presentDays;

    }
}