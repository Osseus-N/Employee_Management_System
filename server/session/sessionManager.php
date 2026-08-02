<?php

namespace service;

class SessionManager
{
    public static function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login(int $empId, string $role, string $email): void
    {
        self::init();
        session_regenerate_id(true);
        $_SESSION['emp_id'] = $empId;
        $_SESSION['role']   = $role;
        $_SESSION['email']  = $email;
    }

    public static function isLoggedIn(): bool
    {
        self::init();
        return isset($_SESSION['emp_id']);
    }

    public static function isAdmin(): bool
    {
        self::init();
        return self::isLoggedIn() && ($_SESSION['role'] ?? '') === 'admin';
    }

    public static function currentEmpId(): ?int
    {
        self::init();
        return isset($_SESSION['emp_id']) ? (int) $_SESSION['emp_id'] : null;
    }

    public static function currentRole(): ?string
    {
        self::init();
        return $_SESSION['role'] ?? null;
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
