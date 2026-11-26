<?php
// CLI-only bulk export. Files are self-contained and are written outside the web root.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
if (count($argv) < 3) { fwrite(STDERR, "Usage: php scripts/export_id_cards.php BRANCH_ID OUTPUT_DIRECTORY [--dry-run]\n"); exit(1); }
error_reporting(E_ALL & ~E_DEPRECATED);
date_default_timezone_set('Asia/Calcutta');
chdir(dirname(__DIR__));
define('FCPATH', getcwd() . '/');
define('BASEPATH', FCPATH . 'system/');
define('APPPATH', FCPATH . 'application/');
define('ENVIRONMENT', 'production');
function is_php($version) { return version_compare(PHP_VERSION, $version, '>='); }
function log_message($level, $text) { if ($level === 'error') { fwrite(STDERR, $text . "\n"); } }
function show_error($text) { throw new RuntimeException($text); }
function html_escape($text) { return htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); }
function base_url($path = '') { return FCPATH . $path; }
function get_image_url($role, $photo) {
    $path = 'uploads/images/' . $role . '/' . basename((string) $photo);
    return FCPATH . (is_file($path) ? $path : 'uploads/app_image/defualt.png');
}
function _d($date) { return empty($date) || strpos($date, '0000-00-00') === 0 ? '' : date('d M Y', strtotime($date)); }
require BASEPATH . 'database/DB.php';
// Supply only the dependencies used by the application's existing card renderer.
class MY_Model { public $db; public $ciqrcode; public function __construct() {} }
require APPPATH . 'models/Card_manage_model.php';
require APPPATH . 'libraries/Ciqrcode.php';
$db = DB();
$branchID = (int) $argv[1];
$branch = $db->get_where('branch', ['id' => $branchID, 'status' => 1])->row_array();
if (!$branch) { throw new RuntimeException('Active branch not found.'); }
$session = $db->select('session_id')->get_where('global_settings', ['id' => 1])->row()->session_id;
$students = $db->query('SELECT e.id FROM enroll e JOIN student s ON s.id=e.student_id WHERE e.branch_id=? AND e.session_id=? AND s.active=1 ORDER BY e.class_id,e.section_id,e.roll,s.first_name', [$branchID, $session])->result_array();
$staff = $db->query('SELECT DISTINCT s.id FROM staff s JOIN login_credential l ON l.user_id=s.id AND l.role NOT IN (1,6,7) AND l.active=1 WHERE s.branch_id=? ORDER BY s.name', [$branchID])->result_array();
echo $branch['name'] . ': ' . count($students) . ' students, ' . count($staff) . " active staff\n";
if (in_array('--dry-run', $argv, true)) { exit; }
$out = rtrim(str_replace('\\', '/', $argv[2]), '/');
if (!is_dir($out) && !mkdir($out, 0700, true)) { throw new RuntimeException('Cannot create output directory.'); }
$realOutput = str_replace('\\', '/', realpath($out));
if (stripos($realOutput . '/', str_replace('\\', '/', FCPATH)) === 0) { throw new RuntimeException('Choose an output directory outside the website.'); }
$model = new Card_manage_model(); $model->db = $db; $model->ciqrcode = new Ciqrcode(['cacheable' => false]);
$issued = date('Y-m-d');
foreach ($argv as $argument) {
    if (strpos($argument, '--issue-date=') === 0) {
        $issued = substr($argument, strlen('--issue-date='));
    }
}
$issueDate = DateTimeImmutable::createFromFormat('!Y-m-d', $issued);
if (!$issueDate || $issueDate->format('Y-m-d') !== $issued) { throw new RuntimeException('Invalid issue date. Use YYYY-MM-DD.'); }
$expires = $issueDate->modify('+1 year -1 day')->format('Y-m-d');
$manifest = ['branch' => $branch['name'], 'branch_id' => $branchID, 'session_id' => $session, 'issued' => $issued, 'expires' => $expires];
foreach ([1 => ['student', $students], 2 => ['staff', $staff]] as $type => [$label, $people]) {
    $template = $db->get_where('card_templete', ['branch_id' => $branchID, 'card_type' => 1, 'user_type' => $type, 'name' => $type === 1 ? 'Student Identity Card' : 'Staff Identity Card'])->row_array();
    if (!$template) { throw new RuntimeException('Missing template for ' . $label); }
    $title = html_escape($branch['name'] . ' - ' . ucfirst($label) . ' ID cards');
    $html = '<!doctype html><html><head><meta charset="utf-8"><title>' . $title . '</title><style>@page{size:A4;margin:8mm}*{box-sizing:border-box}body{margin:0;font-family:Arial;background:#eef2f6}.sheet{width:180mm;height:276mm;display:grid;grid-template-columns:repeat(3,54mm);grid-auto-rows:86mm;gap:6mm;margin:0 auto;break-after:page;padding:0}.sheet:last-child{break-after:auto}.card{width:54mm;height:86mm;break-inside:avoid}.toolbar{padding:18px;text-align:center}@media print{body{background:white}.toolbar{display:none}*{-webkit-print-color-adjust:exact;print-color-adjust:exact}}</style></head><body><div class="toolbar"><h2>' . $title . '</h2><p>' . count($people) . ' cards | Print A4 at 100% / Actual size</p><button onclick="window.print()">Print / Save PDF</button></div>';
    foreach (array_chunk($people, 9) as $page) {
        $html .= '<section class="sheet">';
        foreach ($page as $person) {
            $body = $model->tagsReplace($type, $person['id'], $template, $issued, $expires);
            if (preg_match('/\{[a-z_]+\}/', $body)) { throw new RuntimeException('Unresolved card placeholder'); }
            $body = preg_replace_callback('/src="([^"]+)"/', function ($match) {
                $path = $match[1];
                if (!is_file($path)) { throw new RuntimeException('Missing image: ' . $path); }
                $mime = mime_content_type($path);
                return 'src="data:' . $mime . ';base64,' . base64_encode(file_get_contents($path)) . '"';
            }, $body);
            $html .= '<article class="card">' . $body . '</article>';
        }
        $html .= '</section>';
    }
    $html .= '</body></html>';
    $file = "$out/$label-id-cards.html";
    if (file_exists($file)) { throw new RuntimeException('Output already exists: ' . $file); }
    file_put_contents($file, $html);
    $manifest[$label] = ['cards' => count($people), 'pages' => (int) ceil(count($people)/9)];
    echo "Saved $file\n";
}
file_put_contents("$out/manifest.json", json_encode($manifest, JSON_PRETTY_PRINT));
