<?php

if (isset($_SESSION['emp_id'])) {
    $role = $_SESSION['emp_role'];

    if ($role == 'admin') {
        header('location: /admin.php');
    }

    header('location: /employee.php');
}
