<?php

namespace session;

use JetBrains\PhpStorm\NoReturn;

class sessionManager
{
    public static function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_secure', 0);
            session_start();
        }

        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } elseif (time() - $_SESSION['created'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
    }

    public static function isLoggedIn()
    {
        self::init();

        if(!isset($_SESSION["emp_id"])){
            http_response_code(401);
            echo json_encode(array("error" => "You must log in to access this page."));
            header("Location: /Employee_Management_System/login");
            exit;
        }

        return $_SESSION['emp_id'];

    }
    public static function setUserSession(int $id, string $name, string $role): void
    {
        $_SESSION['emp_id']   = $id;
        $_SESSION['emp_name'] = $name;
        $_SESSION['role']      = $role;
        session_regenerate_id(true);
    }

    #[NoReturn]
    public static function redirectByRole(?string $role = null): void
    {
        self::init();

        $userRole = strtolower($role ?? $_SESSION['role'] ?? '');
        $basePath = '/employee_management_system';

        if (empty($userRole)) {
            header("Location: {$basePath}/login");
            exit;
        }

        if ($userRole === 'admin') {
            header("Location: {$basePath}/admin");
            exit;
        }

        header("Location: {$basePath}/employee");
        exit;
    }

    public static function destroySession(): void
    {
        self::init();

        $_SESSION = [];

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        session_destroy();
    }
}