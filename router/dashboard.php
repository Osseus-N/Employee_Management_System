<?php

    $role = $_SESSION['emp_role'];

    if ($role === "admin") {
        header('location: /Employee_Management_System/router/admin.php');
    }

    header('location: /Employee_Management_System/router/employee.php');

