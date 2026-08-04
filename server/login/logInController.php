<?php

namespace login;

use admin\adminRepository;
use admin\adminService;
use service\SessionManager;
use response\responseController;

class logInController extends ResponseController
{
    private logInService $service;
    private $adminRepository;
    private $adminService;

    public function __construct(logInService $logInService, adminRepository $adminRepository,
    adminService $adminService)
    {
        $this->service = $logInService;
        $this->adminRepository = $adminRepository;
        $this->adminService = $adminService;
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
        $this->seedDefaultAdminIfNeeded();

        if (isset($_SESSION['emp_id'])) {
            header('location: /Employee_Management_System/router/dashboard.php');
        }
        include __DIR__ . '/../../client/view/login/login.html';

    }

    private function seedDefaultAdminIfNeeded(): void
    {
        $employees = $this->adminRepository->getAllEmployees();

        if (empty($employees)) {
            $this->adminService->createDefaultAdmin();
        }

    }
    private function login(): void
    {
        $data = json_decode(file_get_contents("php://input"), true) ?? [];

        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->error('Email and password are required', 401);
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