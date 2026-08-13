<?php

namespace login;

use admin\adminRepository;
use admin\adminService;
use JetBrains\PhpStorm\NoReturn;
use response\responseController;
use session\sessionManager;

class logInController extends ResponseController
{
    private logInService $service;
    private adminRepository $adminRepository;
    private adminService $adminService;

    public function __construct(
        logInService $logInService,
        adminRepository $adminRepository,
        adminService $adminService
    ) {
        $this->service = $logInService;
        $this->adminRepository = $adminRepository;
        $this->adminService = $adminService;
    }

    public function login(): void
    {
        SessionManager::init();

        $data = json_decode(file_get_contents("php://input"), true) ?? [];

        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->error('Email and password are required', 422);
            return;
        }

        $user = $this->service->authenticateAccount($email, $password);

        if ($user) {
            SessionManager::setUserSession(
                $user['emp_id'],
                $user['emp_firstname'],
                $user['emp_position']
            );

            $this->success("Logged in successfully", [
                "emp_id" => $user['emp_id'],
                "role" => $user["role"] ?? null,
                "user" => $user
            ]);
            return;
        }

        $this->error('Invalid email or password', 422);
    }

    public function showLoginForm(): void
    {
        SessionManager::init();
        $this->seedDefaultAdminIfNeeded();

        if (isset($_SESSION['role'])) {
            header('Location: /employee_management_system/employee');
            exit;
        }

        header("Content-Type: text/html; charset=UTF-8");
        include __DIR__ . '/../../client/view/login.html';
    }

    #[NoReturn]
    public function logout(): void
    {
        SessionManager::destroySession();
        $this->success("Logged out successfully");

    }

    /**
     * @throws \Exception
     */
    private function seedDefaultAdminIfNeeded(): void
    {
        $employees = $this->adminRepository->getAllEmployees();

        $data = $this->adminRepository->getAllEmployees();

        $employees = $data['employees'] ?? [];

        if (empty($employees)) {
            $this->adminService->createDefaultAdmin();
        }
    }

}