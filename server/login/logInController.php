<?php

namespace login;

use service\SessionManager;
use response\responseController;

class logInController extends ResponseController
{
    private logInService $service;

    public function __construct(logInService $logInService)
    {
        $this->service = $logInService;
    }

    public function handleRequest(): void
    {
        SessionManager::init();

        switch ($_SERVER["REQUEST_METHOD"]) {
            case "POST":
                $this->login();
                break;

            case "GET":
                $this->showLoginForm();
                break;

            default:
                $this->error("Method Not Allowed", 405);
                break;
        }
    }

    public function showLoginForm(): void
    {
        if (isset($_SESSION['emp_id'])) {
            $this->success('Already logged in.', [
                'redirect' => '/dashboard.php'
            ]);
        }

        $this->login();
    }

    private function login(): void
    {
        $data = json_decode(file_get_contents("php://input"), true) ?? [];

        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->error('Email and password are required', 400);
        }

        $user = $this->service->authenticateAccount($email, $password);

        if ($user) {
            SessionManager::setUserSession(
                $user['emp_id'],
                $user['emp_firstname'],
                $user['emp_position']
            );

            $this->success("Logged in successfully", [
                "role" => $user["role"] ?? null,
                "user" => $user
            ], 200);
        }

        $this->error('Invalid email or password', 401);
    }
}