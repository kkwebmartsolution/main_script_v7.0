<?php
// Run with PHP CLI and --apply to add missing templates without replacing existing cards.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
define('BASEPATH', dirname(__DIR__) . '/system/');
define('ENVIRONMENT', 'production');
require dirname(__DIR__) . '/application/config/database.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$config = $db['default'];
$connection = new mysqli($config['hostname'], $config['username'], $config['password'], $config['database']);
$connection->set_charset('utf8mb4');
$apply = in_array('--apply', $argv, true);
$branches = $connection->query('SELECT id FROM branch WHERE status = 1')->fetch_all(MYSQLI_ASSOC);
$connection->begin_transaction();
try {
    foreach ($branches as $branch) {
        foreach ([1 => 'Student Identity Card', 2 => 'Staff Identity Card'] as $card_user_type => $name) {
            $branchID = (int) $branch['id'];
            $exists = $connection->prepare('SELECT id FROM card_templete WHERE branch_id=? AND user_type=? AND card_type=1 AND name=?');
            $exists->bind_param('iis', $branchID, $card_user_type, $name);
            $exists->execute();
            if ($exists->get_result()->num_rows) { continue; }
            ob_start();
            require dirname(__DIR__) . '/application/views/card_manage/default_id_card.php';
            $content = ob_get_clean();
            if ($apply) {
                $insert = $connection->prepare("INSERT INTO card_templete (branch_id,name,card_type,user_type,layout_width,layout_height,photo_style,photo_size,top_space,bottom_space,right_space,left_space,qr_code,content,background,logo,signature) VALUES (?,?,1,?,'54','86',1,'74',0,0,0,0,'attendance',?,'','','')");
                $insert->bind_param('isis', $branchID, $name, $card_user_type, $content);
                $insert->execute();
            }
            echo ($apply ? 'Created' : 'Would create') . " $name for branch $branchID\n";
        }
    }
    $connection->commit();
} catch (Throwable $error) {
    $connection->rollback();
    throw $error;
}
