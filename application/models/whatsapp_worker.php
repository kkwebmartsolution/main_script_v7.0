<?php
/**
 * Standalone High-Speed Background WhatsApp Worker
 * Dispatched asynchronously without blocking web requests
 */

define('BASEPATH', 'TRUE');
define('ENVIRONMENT', 'production');

$configFile = dirname(dirname(__DIR__)) . '/application/config/database.php';
if (!file_exists($configFile)) {
    exit(1);
}
require_once $configFile;

$db_config = $db['default'];
$conn = new mysqli($db_config['hostname'], $db_config['username'], $db_config['password'], $db_config['database']);
if ($conn->connect_error) {
    exit(1);
}

// Get queue ID from CLI argument if passed
$queueId = isset($argv[1]) ? intval($argv[1]) : 0;

if ($queueId > 0) {
    $sql = "SELECT * FROM `whatsapp_queue` WHERE `id` = $queueId AND `status` = 0 LIMIT 1";
} else {
    $sql = "SELECT * FROM `whatsapp_queue` WHERE `status` = 0 ORDER BY `id` ASC LIMIT 5";
}

$result = $conn->query($sql);
if (!$result || $result->num_rows == 0) {
    exit(0);
}

while ($row = $result->fetch_assoc()) {
    $id = intval($row['id']);
    // Mark as in progress (status = 1)
    $conn->query("UPDATE `whatsapp_queue` SET `status` = 1, `updated_at` = NOW() WHERE `id` = $id");

    $apiUrl = rtrim($row['api_url'], '/');
    $endpoint = (substr($apiUrl, -9) === '/messages') ? $apiUrl : $apiUrl . '/messages';
    $apiKey = $row['api_key'];
    $recipient = $row['recipient'];
    $templateName = $row['template_name'];
    $parameters = json_decode($row['parameters'], true);

    $paramComponents = array();
    if (is_array($parameters)) {
        foreach ($parameters as $param) {
            $paramComponents[] = array(
                'type' => 'text',
                'text' => (string) $param
            );
        }
    }

    $payload = array(
        'messaging_product' => 'whatsapp',
        'to' => $recipient,
        'type' => 'template',
        'template' => array(
            'name' => $templateName,
            'language' => array(
                'code' => 'en'
            ),
            'components' => array(
                array(
                    'type' => 'body',
                    'parameters' => $paramComponents
                )
            )
        )
    );

    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json',
        'Accept: application/json'
    ));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);

    $success = ($httpCode >= 200 && $httpCode < 300);
    $finalStatus = $success ? 2 : 3; // 2 = sent, 3 = failed

    $escapedResponse = $conn->real_escape_string($response ?: $curlErr);
    $conn->query("UPDATE `whatsapp_queue` SET `status` = $finalStatus, `response` = '$escapedResponse', `updated_at` = NOW() WHERE `id` = $id");
}

$conn->close();
exit(0);
