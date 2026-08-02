<?php

namespace database;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    // ---- EDIT THESE TO MATCH YOUR LOCAL MYSQL SETUP ----
    private static string $host   = '127.0.0.1';
    private static string $dbname = 'employee_management';
    private static string $user   = 'root';
    private static string $pass   = '';
    // ------------------------------------------------------

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$dbname . ";charset=utf8mb4";
            try {
                self::$pdo = new PDO($dsn, self::$user, self::$pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Database connection failed: ' . $e->getMessage(),
                ]);
                exit;
            }
        }

        return self::$pdo;
    }
}
