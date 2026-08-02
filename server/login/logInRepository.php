<?php

namespace login;

use database\Database;
use PDO;

class loginRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT a.acc_id, a.emp_id, a.acc_email, a.acc_role, a.acc_password,
                    e.emp_firstname, e.emp_lastname, e.emp_status
             FROM accounts a
             INNER JOIN employees e ON a.emp_id = e.emp_id
             WHERE a.acc_email = :email
             LIMIT 1"
        );
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();

        return $row ?: null;
    }
}
