<?php
define('BASEPATH', 'dummy');
define('ENVIRONMENT', 'development');
require_once('application/config/database.php');

$db_config = $db['default'];
$conn = new mysqli($db_config['hostname'], $db_config['username'], $db_config['password'], $db_config['database']);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure saas_settings table
$conn->query("CREATE TABLE IF NOT EXISTS `saas_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$settings_columns = [
    'receive_contact_email' => "VARCHAR(255) DEFAULT NULL",
    'terms_status' => "INT(1) NOT NULL DEFAULT '0'",
    'pwa_enable' => "INT(1) NOT NULL DEFAULT '0'",
    'primary_color' => "VARCHAR(255) DEFAULT '#6e8fd4'",
    'heading_text_color' => "VARCHAR(255) DEFAULT '#333333'",
    'text_color' => "VARCHAR(255) DEFAULT '#666666'",
    'menu_bg_color' => "VARCHAR(255) DEFAULT '#ffffff'",
    'menu_text_color' => "VARCHAR(255) DEFAULT '#333333'",
    'footer_bg_color' => "VARCHAR(255) DEFAULT '#222222'",
    'footer_text_color' => "VARCHAR(255) DEFAULT '#ffffff'",
    'copyright_bg_color' => "VARCHAR(255) DEFAULT '#111111'",
    'copyright_text_color' => "VARCHAR(255) DEFAULT '#ffffff'",
    'slider_title' => "VARCHAR(255) DEFAULT NULL",
    'slider_description' => "TEXT DEFAULT NULL",
    'feature_title' => "VARCHAR(255) DEFAULT NULL",
    'feature_description' => "TEXT DEFAULT NULL",
    'price_plan_title' => "VARCHAR(255) DEFAULT NULL",
    'price_plan_description' => "TEXT DEFAULT NULL",
    'faq_title' => "VARCHAR(255) DEFAULT NULL",
    'faq_description' => "TEXT DEFAULT NULL",
    'contact_title' => "VARCHAR(255) DEFAULT NULL",
    'contact_description' => "TEXT DEFAULT NULL",
    'contact_button' => "VARCHAR(255) DEFAULT 'Send Message'",
    'footer_about' => "TEXT DEFAULT NULL",
    'price_plan_button' => "VARCHAR(255) DEFAULT 'Get Started'",
    'seo_title' => "VARCHAR(255) DEFAULT NULL",
    'seo_keyword' => "VARCHAR(255) DEFAULT NULL",
    'seo_description' => "TEXT DEFAULT NULL",
    'expired_alert' => "int(11) DEFAULT 0",
    'expired_alert_days' => "int(11) DEFAULT 0",
    'expired_alert_message' => "text DEFAULT NULL",
    'expired_message' => "text DEFAULT NULL",
    'paypal_status' => "int(11) DEFAULT 0",
    'stripe_status' => "int(11) DEFAULT 0",
    'payumoney_status' => "int(11) DEFAULT 0",
    'playstore_url' => "varchar(255) DEFAULT NULL",
    'appstore_url' => "varchar(255) DEFAULT NULL",
    'google_analytics' => "text DEFAULT NULL",
    'captcha_status' => "int(11) DEFAULT 0",
    'recaptcha_site_key' => "varchar(255) DEFAULT NULL",
    'recaptcha_secret_key' => "varchar(255) DEFAULT NULL",
    'terms_and_conditions' => "longtext DEFAULT NULL",
    'agree_checkbox_text' => "text DEFAULT NULL",
    'slider_bg_image' => "varchar(255) DEFAULT NULL",
    'slider_image' => "varchar(255) DEFAULT NULL",
    'payment_logo' => "varchar(255) DEFAULT NULL",
    'overly_image' => "varchar(255) DEFAULT NULL",
    'overly_image_status' => "int(11) DEFAULT 0",
    'button_text_1' => "varchar(255) DEFAULT NULL",
    'button_url_1' => "varchar(255) DEFAULT NULL",
    'button_text_2' => "varchar(255) DEFAULT NULL",
    'button_url_2' => "varchar(255) DEFAULT NULL",
    'automatic_approval' => "int(11) DEFAULT 0",
    'offline_payments' => "int(11) DEFAULT 0"
];

foreach ($settings_columns as $col => $type) {
    $check = $conn->query("SHOW COLUMNS FROM `saas_settings` LIKE '$col'");
    if ($check->num_rows == 0) {
        echo "Adding column $col...\n";
        $conn->query("ALTER TABLE `saas_settings` ADD `$col` $type");
    }
}

// Ensure row ID 1 exists
$check_row = $conn->query("SELECT id FROM saas_settings WHERE id = 1");
if ($check_row->num_rows == 0) {
    echo "Inserting row ID 1...\n";
    $conn->query("INSERT INTO saas_settings (id) VALUES (1)");
}

echo "Live Server Database Fix Completed.\n";
$conn->close();
?>
