//Pang One Time Setup para sa admin para ma access yung login
<?php
require_once __DIR__ . '/server/database/database.php';

use database\Database;

$db = Database::getConnection();
$firstname = 'System';
$lastname  = 'Admin';
$email     = 'admin@company.com';
$password  = 'admin123';

$check = $db->prepare("SELECT acc_id FROM accounts WHERE acc_email = :email");
$check->execute(['email' => $email]);

if ($check->fetch()) {
    die("An account with email {$email} already exists. Setup already done — you can delete this file.");
}

$db->beginTransaction();

try {
    $stmt = $db->prepare(
        "INSERT INTO employees
            (emp_firstname, emp_lastname, emp_gender, emp_date_of_birth, emp_position, emp_hourly_rate, emp_status)
         VALUES
            (:firstname, :lastname, 'Other', '2000-01-01', 'Administrator', 0, 'Active')"
    );
    $stmt->execute(['firstname' => $firstname, 'lastname' => $lastname]);
    $empId = (int) $db->lastInsertId();

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare(
        "INSERT INTO accounts (emp_id, acc_email, acc_role, acc_password)
         VALUES (:emp_id, :email, 'admin', :password)"
    );
    $stmt->execute(['emp_id' => $empId, 'email' => $email, 'password' => $hash]);

    $db->commit();

    echo "<h2>Admin account created!</h2>";
    echo "<p>Email: <strong>{$email}</strong></p>";
    echo "<p>Password: <strong>{$password}</strong></p>";
    echo "<p style='color:red;'>Delete this file (setup_admin.php) now, then log in at /login/login.html</p>";
} catch (Exception $e) {
    $db->rollBack();
    die("Setup failed: " . $e->getMessage());
}
