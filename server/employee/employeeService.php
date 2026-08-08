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
    public function getEmployee(mixed $emp_id): false|array|null
    {

        $user =$this->employeeRepository->getEmployee($emp_id);
        $accountEmail = $this->employeeRepository->getAccountEmail($emp_id);

        if($user) {
            $user['email']= $accountEmail;
            unset($user['password']);
            return $user;
        }
        return null;
    }
    public function getAccountEmail($emp_id){

        $user = $this->employeeRepository->getAccountEmail($emp_id);

        return $user ?? null;
}
}