<?php

namespace service;

class SessionManager
{
    /**
     * Start session safely
     */
    public static function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set("session.cookie_httponly", 1);
            ini_set("session.use_only_cookies", 1);
            ini_set("session.cookie_secure", 0); // Change to 1 when using HTTPS
            session_start();
        }

        if (!isset($_SESSION["created"])) {
            $_SESSION["created"] = time();
        } elseif (time() - $_SESSION["created"] > 1800) {
            session_regenerate_id(true);
            $_SESSION["created"] = time();
        }
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION["emp_id"])) {
            http_response_code(401);

            header("Content-Type: application/json");

            echo json_encode([
                "success" => false,
                "message" => "Unauthorized. Please login first."
            ]);

            exit;
        }

        return true;
    }

    /**
     * Store user session
     */
    public static function setUserSession(
        int $id,
        string $name,
        string $role
    ): void {

        self::init();

        $_SESSION["emp_id"] = $id;
        $_SESSION["emp_name"] = $name;
        $_SESSION["role"] = $role;

        session_regenerate_id(true);
    }

    /**
     * Get logged in employee ID
     */
    public static function getEmpId(): ?int
    {
        return $_SESSION["emp_id"] ?? null;
    }

    /**
     * Get employee role
     */
    public static function getRole(): ?string
    {
        return $_SESSION["role"] ?? null;
    }

    /**
     * Check if current user is admin
     */
    public static function isAdmin(): bool
    {
        return (self::getRole() === "Admin");
    }

    /**
     * Destroy session
     */
    public static function destroySession(): void
    {
        self::init();

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {

            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                "",
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
    }
}