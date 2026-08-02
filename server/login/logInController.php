<?php

namespace login;

use response\responseController;
use service\SessionManager;

class loginController extends responseController
{
    private loginService $loginService;

    public function __construct(loginService $loginService)
    {
        $this->loginService = $loginService;
    }

    public function handleRequest(): void
    {
        SessionManager::init();
        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'POST':
                $this->login();
                break;
            case 'DELETE':
                $this->logout();
                break;
            case 'GET':
                $this->me();
                break;
            default:
                $this->error('Method not allowed', 405);
        }
    }

    public function login(): void
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $email    = trim((string) ($data['email'] ?? ''));
        $password = (string) ($data['password'] ?? '');

        if ($email === '' || $password === '') {
            $this->error('Email and password are required.', 400);
        }

        $account = $this->loginService->authenticate($email, $password);

        if (!$account) {
            $this->error('Invalid email or password.', 401);
        }

        SessionManager::login((int) $account['emp_id'], $account['acc_role'], $account['acc_email']);

        $this->success('Login successful', [
            'emp_id'    => (int) $account['emp_id'],
            'role'      => $account['acc_role'],
            'email'     => $account['acc_email'],
            'firstname' => $account['emp_firstname'],
            'lastname'  => $account['emp_lastname'],
        ]);
    }

    public function logout(): void
    {
        SessionManager::destroySession();
        $this->success('Logged out successfully');
    }

    public function me(): void
    {
        if (!SessionManager::isLoggedIn()) {
            $this->error('Not logged in', 401);
        }

        $this->success('Session active', [
            'emp_id' => SessionManager::currentEmpId(),
            'role'   => SessionManager::currentRole(),
        ]);
    }
}
