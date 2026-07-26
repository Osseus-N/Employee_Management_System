<?php

namespace employee;

header("Content-Type: application/json");

require_once __DIR__ . "/../database/database.php";
require_once __DIR__ . "/employeeRepository.php";
require_once __DIR__ . "/employeeService.php";

use Exception;

try {

    $database = new \Database();
    $database->connect();

    $repository = new employeeRepository($database);
    $service = new employeeService($repository);

    $method = $_SERVER["REQUEST_METHOD"];

    switch ($method) {

        // ============================
        // GET ALL EMPLOYEES
        // ============================
        case "GET":

            echo json_encode([
                "success" => true,
                "data" => $service->getEmployees()
            ]);

            break;

        // ============================
        // ADD EMPLOYEE
        // ============================
        case "POST":

            $data = json_decode(file_get_contents("php://input"), true);

            $result = $service->addEmployee($data);

            echo json_encode($result);

            break;

        // ============================
        // UPDATE EMPLOYEE
        // ============================
        case "PUT":

            $data = json_decode(file_get_contents("php://input"), true);

            $result = $service->updateEmployee($data);

            echo json_encode($result);

            break;

        // ============================
        // DELETE EMPLOYEE
        // ============================
        case "DELETE":

            $data = json_decode(file_get_contents("php://input"), true);

            $result = $service->deleteEmployee($data["emp_id"]);

            echo json_encode($result);

            break;

        default:

            http_response_code(405);

            echo json_encode([
                "success" => false,
                "message" => "Method Not Allowed"
            ]);
    }

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}