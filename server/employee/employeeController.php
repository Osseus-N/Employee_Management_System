<?php

namespace employee;

use http\Encoding\Stream\Debrotli;
use service\SessionManager;

class employeeController
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

        SessionManager::isLoggedIn();

        $emp_id = $_SESSION['emp_id'];

        $user = $this->service->getEmployee($emp_id);

        if($user){
            echo json_encode([
                "success" => true,
                "message" => "Logged in successfully",
                "data" => $user
            ]);
        }
        else{
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "Employee not found",
            ]);
        }
        exit;
    }

    public function handleUpdate(){

        $data = json_decode(file_get_contents("php://input"));

        SessionManager::isLoggedIn();

        $emp_id = $_SESSION['emp_id'];

        $user = $this->service->editEmployee($emp_id, $data);

        if($user){
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "message" => "Employee updated successfully",
                "data" => $user
            ]);
        }else{
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "Employee not found",
            ]);
        }
        exit;
    }

}