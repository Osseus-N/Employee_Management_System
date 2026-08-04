<?php

namespace employee;

use http\Encoding\Stream\Debrotli;
use response\responseController;
use service\SessionManager;

class employeeController extends responseController
{
    private $service;
    public function __construct(employeeService $service){
    $this->service = $service;
    }

    public function handleRequest(){

        SessionManager::init();

        $method = $_SERVER['REQUEST_METHOD'];

        switch($method){
            case "GET":
                $this->handleEmployees();
                break;
            case "PUT":
                $this->handleUpdate();
                break;
        }
    }

    public function handleEmployees(){

        $emp_id = $_SESSION['emp_id'];

        $user = $this->service->getEmployee($emp_id);

        ($user) ? include __DIR__ . '/../../client/view/employee/employee_view.html'
        : $this->error("Employee not found" , 404);
    }

    public function handleUpdate(){

        $data = json_decode(file_get_contents("php://input"));

        SessionManager::isLoggedIn();

        $emp_id = $_SESSION['emp_id'];

        $user = $this->service->editEmployee($emp_id, $data);

        ($user) ? $this->success("Employee updated successfully", $user)
                : $this->error("Employee not found", 404);

        exit;
    }

}