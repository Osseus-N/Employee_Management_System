<?php

namespace login;

use service\SessionManager;

class logInController
{

    private logInService $service;
    public function __construct(logInService $logInService){
        $this->service = $logInService;
    }

    public function handleRequest()
    {
        SessionManager::init();

        switch ($_SERVER["REQUEST_METHOD"]) {

            case "POST":
                $this->login();
                break;

            case "GET":
                $this->showLoginForm();
                break;

            case "DELETE":
                SessionManager::destroySession();

                echo json_encode([
                    "success" => true
                ]);
                break;

            default:
                http_response_code(405);

                echo json_encode([
                    "success" => false,
                    "message" => "Method Not Allowed"
                ]);
        }
    }

    public function showLoginForm(){

        if (isset($_SESSION['emp_id'])) {
            echo json_encode([
                'success'  => true,
                'message'  => 'Already logged in.',
                'redirect' => '/dashboard.php'
            ]);
            exit;
        }

        $this->login();
    }
    public function login()
    {

        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);

        $password = $data['password'] ?? '';
        $email = $data['email'] ?? '';

        $user = $this->service->authenticateAccount($email, $password);

        if ($user) {

            SessionManager::setUserSession(
                $user['emp_id'],
                $user['emp_firstname'],
                $user['emp_position']
            );

            echo json_encode([
                "success" => true,
                "message" => "Logged in successfully",
                "role" => $user["emp_role"],
                "name" => $user["emp_name"]
            ]);
            exit;
        } else http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email or password'
        ]);
        exit;
    }
}