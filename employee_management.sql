-- Employee Management System - Database Schema
-- Import this into MySQL/MariaDB (e.g. via phpMyAdmin) before running the app.

CREATE DATABASE IF NOT EXISTS employee_management CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE employee_management;

-- --------------------------------------------------------

CREATE TABLE `employees` (
  `emp_id` int(11) NOT NULL AUTO_INCREMENT,
 `emp_firstname` varchar(50) NOT NULL,
 `emp_lastname` varchar(50) NOT NULL,
 `emp_gender` enum('Male','Female','Other') NOT NULL,
  `emp_date_of_birth` date NOT NULL,
`emp_contact_number` varchar(20) DEFAULT NULL,
 `emp_position` varchar(50) NOT NULL,
`emp_hourly_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
 `emp_status` enum('Active','Inactive','Terminated') DEFAULT 'Active',
`emp_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`emp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `accounts` (
`acc_id` int(11) NOT NULL AUTO_INCREMENT,
`emp_id` int(11) NOT NULL,
`acc_email` varchar(255) NOT NULL,
 `acc_role` ENUM('employee', 'admin') NOT NULL DEFAULT 'employee',
`acc_password` varchar(255) NOT NULL,
`acc_date_created` datetime DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`acc_id`),
 UNIQUE KEY `emp_id` (`emp_id`),
 UNIQUE KEY `acc_email` (`acc_email`),
 CONSTRAINT `fk_accounts_employees` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `attendance` (
`att_id` int(11) NOT NULL AUTO_INCREMENT,
`emp_id` int(11) NOT NULL,
`att_work_date` date NOT NULL,
`att_clock_in` datetime NOT NULL,
 `att_clock_out` datetime DEFAULT NULL,
`att_total_hours` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`att_id`),
  UNIQUE KEY `unique_emp_work_date` (`emp_id`,`att_work_date`),
 CONSTRAINT `fk_attendance_employee` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `payroll` (
`pay_id` int(11) NOT NULL AUTO_INCREMENT,
`emp_id` int(11) NOT NULL,
`pay_period_start` date NOT NULL,
 `pay_period_end` date NOT NULL,
`pay_total_hours` decimal(5,2) NOT NULL DEFAULT 0.00,
 `pay_status` enum('Pending','Paid','Cancelled') DEFAULT 'Pending',
 `pay_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
PRIMARY KEY (`pay_id`),
 KEY `fk_payroll_employee` (`emp_id`),
 CONSTRAINT `fk_payroll_employee` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
