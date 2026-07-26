<?php

namespace employee;

class employeeRepository
{
    public function getEmployee($emp_id){

        $data = $this->db->select('employees' , "*", ['emp_id' => $emp_id]);
        return $data->fetch_assoc();

    }

    public function editEmployee($table, $data, $where){

        $data = $this->editEmployee($table, $data, $where);

        if ($data && $data->num_rows > 0) {
            return $data->fetch_assoc();
        }

        return null;
    }
}