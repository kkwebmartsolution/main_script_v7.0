<?php
define('BASEPATH', 'dummy');
define('ENVIRONMENT', 'development');
require_once('application/config/database.php');

$db_config = $db['default'];
$conn = new mysqli($db_config['hostname'], $db_config['username'], $db_config['password'], $db_config['database']);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create custom_domain table
$conn->query("CREATE TABLE IF NOT EXISTS `custom_domain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT 0,
  `domain_type` int(11) DEFAULT 1,
  `comments` text DEFAULT NULL,
  `request_date` datetime DEFAULT NULL,
  `approved_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Create custom_domain_instruction table
$conn->query("CREATE TABLE IF NOT EXISTS `custom_domain_instruction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `dns_status` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 1,
  `instruction` text DEFAULT NULL,
  `dns_title` varchar(255) DEFAULT NULL,
  `dns_host_1` varchar(255) DEFAULT NULL,
  `dns_host_2` varchar(255) DEFAULT NULL,
  `dns_value_1` varchar(255) DEFAULT NULL,
  `dns_value_2` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Insert default instruction if not exists
$check = $conn->query("SELECT id FROM custom_domain_instruction WHERE id = 1");
if ($check->num_rows == 0) {
    $conn->query("INSERT INTO custom_domain_instruction (id, title, status, instruction) VALUES (1, 'Custom Domain Setup', 1, 'Please point your domain CNAME record to our server.');");
}

echo "Custom Domain Database Fix Completed.\n";
$conn->close();
