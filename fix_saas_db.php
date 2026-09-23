<?php
define('BASEPATH', 'dummy');
define('ENVIRONMENT', 'development');
require_once('application/config/database.php');

$db_config = $db['default'];
$conn = new mysqli($db_config['hostname'], $db_config['username'], $db_config['password'], $db_config['database']);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure saas_package table and columns
$conn->query("CREATE TABLE IF NOT EXISTS `saas_package` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `discount` decimal(10,2) DEFAULT 0.00,
  `period_value` int(11) DEFAULT 0,
  `period_type` int(11) DEFAULT 1,
  `free_trial` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 1,
  `permission` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$package_columns = [
    'recommended' => "int(11) DEFAULT 0",
    'student_limit' => "int(11) DEFAULT 0",
    'staff_limit' => "int(11) DEFAULT 0",
    'teacher_limit' => "int(11) DEFAULT 0",
    'parents_limit' => "int(11) DEFAULT 0",
    'show_onwebsite' => "int(11) DEFAULT 0"
];

foreach ($package_columns as $col => $type) {
    $check = $conn->query("SHOW COLUMNS FROM `saas_package` LIKE '$col'");
    if ($check->num_rows == 0) {
        $conn->query("ALTER TABLE `saas_package` ADD `$col` $type");
    }
}

// Ensure other tables exist
$conn->query("CREATE TABLE IF NOT EXISTS `saas_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `expire_date` date DEFAULT NULL,
  `upgrade_lasttime` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$conn->query("CREATE TABLE IF NOT EXISTS `saas_school_register` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_name` varchar(255) DEFAULT NULL,
  `admin_name` varchar(255) DEFAULT NULL,
  `gender` varchar(50) DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `reference_no` varchar(50) DEFAULT NULL,
  `status` int(11) DEFAULT 0,
  `payment_status` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

echo "SaaS Database Fix Completed.\n";
$conn->close();
