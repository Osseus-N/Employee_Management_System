<?php

namespace employee;

class employeeService
{
    private employeeRepository $employeeRepository;
    public function __construct(employeeRepository $employeeRepository){
        $this->employeeRepository = $employeeRepository;
    }
    public function editEmployee($emp_id, $data){

        $user = $this->employeeRepository->editEmployee('employee' , $data, ['emp_id' => $emp_id]);

        if($user) {
            unset($user['password']);
            return $user;
        }
        return null;
    }
    public function getEmployee(mixed $emp_id){

        $user =$this->employeeRepository->getEmployee($emp_id);

        if($user) {
            unset($user['password']);
            return $user;
        }
        return null;
    }

    public function createEmployee(array $data)
    {
    }
}