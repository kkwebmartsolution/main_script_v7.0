<?php
define('BASEPATH', 'foo');
define('ENVIRONMENT', 'development');
require_once 'application/config/database.php';
$db_config = $db['default'];

// Try different host variations
$hosts = ['localhost', '127.0.0.1'];
$connected = false;
$conn = null;

foreach ($hosts as $host) {
    try {
        $conn = new mysqli($host, $db_config['username'], $db_config['password'], $db_config['database']);
        if (!$conn->connect_error) {
            $connected = true;
            echo "Connected successfully to $host\n";
            break;
        }
    } catch (Exception $e) {
        echo "Failed to connect to $host: " . $e->getMessage() . "\n";
    }
}

if (!$connected) {
    die("Could not connect to database on any host.\n");
}

$sql = "SHOW COLUMNS FROM `payment_config` LIKE 'cashfree_%'";
$result = $conn->query($sql);

$existing_columns = [];
while ($row = $result->fetch_assoc()) {
    $existing_columns[] = $row['Field'];
}

echo "Existing columns: " . implode(', ', $existing_columns) . "\n";

$needed_columns = [
    'cashfree_app_id' => "VARCHAR(255) DEFAULT NULL",
    'cashfree_secret_key' => "VARCHAR(255) DEFAULT NULL",
    'cashfree_sandbox' => "TINYINT(1) DEFAULT 0",
    'cashfree_status' => "TINYINT(1) DEFAULT 0"
];

foreach ($needed_columns as $col => $definition) {
    if (!in_array($col, $existing_columns)) {
        $alter_sql = "ALTER TABLE `payment_config` ADD COLUMN `$col` $definition";
        if ($conn->query($alter_sql) === TRUE) {
            echo "Added column $col\n";
        } else {
            echo "Error adding column $col: " . $conn->error . "\n";
        }
    } else {
        echo "Column $col already exists.\n";
    }
}

$conn->close();
?>
