<?php
define('BASEPATH', 'dummy');
define('ENVIRONMENT', 'development');
require_once('application/config/database.php');

$db_config = $db['default'];
$conn = new mysqli($db_config['hostname'], $db_config['username'], $db_config['password'], $db_config['database']);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tables = [
    'saas_package', 
    'saas_settings', 
    'saas_school_register', 
    'saas_subscriptions', 
    'saas_subscriptions_transactions', 
    'custom_domain', 
    'custom_domain_instruction', 
    'saas_cms_features', 
    'saas_cms_faq_list'
];

echo "<h3>Database Schema Verification</h3>";
echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr><th>Table Name</th><th>Status</th><th>Columns Count</th></tr>";

foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        $cols = $conn->query("SHOW COLUMNS FROM `$table`")->num_rows;
        echo "<tr><td>$table</td><td style='color:green;'>Exists</td><td>$cols</td></tr>";
    } else {
        echo "<tr><td>$table</td><td style='color:red;'>Missing</td><td>0</td></tr>";
    }
}
echo "</table>";

$conn->close();
?>
