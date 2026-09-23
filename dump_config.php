<?php
define('BASEPATH', 'foo');
define('ENVIRONMENT', 'development');
require_once 'application/config/database.php';
$db_config = $db['default'];
$conn = new mysqli('localhost', $db_config['username'], $db_config['password'], $db_config['database']);
$res = $conn->query("SELECT * FROM payment_config");
if (!$res) {
    die("Query failed: " . $conn->error);
}
echo "Number of rows: " . $res->num_rows . "\n";
while($row = $res->fetch_assoc()) {
    echo "Branch ID: " . $row['branch_id'] . " | Cashfree App ID: " . $row['cashfree_app_id'] . " | Status: " . $row['cashfree_status'] . "\n";
}
$conn->close();
?>
