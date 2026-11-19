<?php
// Run: php tests/subject_class_assign_test.php (no real database writes).
define('BASEPATH', __DIR__);
class Admin_Controller {
    public $input, $form_validation, $application_model, $app_lib, $db, $subject_model;
}
function get_permission($module, $action) { return true; }
function is_superadmin_loggedin() { return false; }
function get_session_id() { return 7; }
function translate($key) { return $key; }
function set_alert($type, $message) {}
function base_url($path) { return $path; }
require __DIR__ . '/../application/controllers/Subject.php';
class AssignmentInput {
    public $data;
    public function post($key) { return isset($this->data[$key]) ? $this->data[$key] : null; }
}
class AssignmentValidation {
    public function set_rules($field, $label, $rules) {}
    public function run() { return true; }
}
class AssignmentBranch {
    public function get_branch_id() { return 3; }
}
class AssignmentLists {
    public function getClass($branch, $select) { return array(1 => 'Class 1', 2 => 'Class 2', 3 => 'Empty class'); }
    public function getSections($class) {
        return $class == 1 ? array('' => 'Select', 10 => 'A', 11 => 'B') : ($class == 2 ? array(20 => 'A') : array());
    }
}
class AssignmentResult {
    public $count;
    public function __construct($count) { $this->count = $count; }
    public function num_rows() { return $this->count; }
}
class AssignmentDB {
    public $rows = array();
    public function get_where($table, $where) {
        if ($table === 'class') return new AssignmentResult($where['branch_id'] == 3 && in_array($where['id'], array(1, 2, 3)) ? 1 : 0);
        if ($table === 'subject') return new AssignmentResult($where['branch_id'] == 3 && in_array($where['id'], array(5, 6)) ? 1 : 0);
        foreach ($this->rows as $row) {
            if (!array_diff_assoc($where, $row)) return new AssignmentResult(1);
        }
        return new AssignmentResult(0);
    }
    public function insert($table, $row) { $this->rows[] = $row; }
    public function trans_start() {}
    public function trans_complete() {}
    public function trans_status() { return true; }
}
class AssignmentTeachers {
    public function get($table, $where, $single) {
        return $where['section_id'] == 20 ? array() : array('teacher_id' => $where['section_id'] + 100);
    }
}
function checkAssignment($condition, $message) {
    if (!$condition) throw new Exception($message);
}
function assignSubjects($classes, $section, $subjects, $db = null) {
    $controller = (new ReflectionClass('Subject'))->newInstanceWithoutConstructor();
    $controller->input = new AssignmentInput();
    $controller->input->data = array('class_ids' => $classes, 'section_id' => $section, 'subjects' => $subjects);
    $controller->form_validation = new AssignmentValidation();
    $controller->application_model = new AssignmentBranch();
    $controller->app_lib = new AssignmentLists();
    $controller->subject_model = new AssignmentTeachers();
    $controller->db = $db ?: new AssignmentDB();
    $_POST = $controller->input->data;
    ob_start();
    $controller->class_assign_save();
    $response = json_decode(ob_get_clean(), true);
    return array($response, $controller->db);
}
list($response, $db) = assignSubjects(array('1', '2'), 'all', array('5', '6'));
checkAssignment($response['status'] === 'success' && count($db->rows) === 6, 'Both subjects must reach all three sections.');
foreach ($db->rows as $row) {
    checkAssignment($row['branch_id'] === 3 && $row['session_id'] === 7, 'Branch and session must be scoped.');
    checkAssignment($row['teacher_id'] === ($row['section_id'] == 20 ? 0 : $row['section_id'] + 100), 'Teacher must come from the target section.');
}
$db->rows[0]['teacher_id'] = 999;
list($response, $db) = assignSubjects(array('1', '2', '1'), 'all', array('5', '6', '5'), $db);
checkAssignment(count($db->rows) === 6 && $db->rows[0]['teacher_id'] === 999, 'Repeat assignment must preserve rows and assigned teachers.');
list($response, $db) = assignSubjects(array('1'), '11', array('5'));
checkAssignment(count($db->rows) === 1 && $db->rows[0]['section_id'] == 11, 'Single class must respect the chosen section.');
foreach (array(
    array(array('1', '99'), 'all', array('5')),
    array(array('1', '3'), 'all', array('5')),
    array(array('1'), '20', array('5')),
    array(array('1', '2'), 'all', array('5', '99')),
    array(array(array('1')), 'all', array('5')),
    array(array(), 'all', array('5')),
    array(array('1'), 'all', '5'),
) as $invalid) {
    list($response, $db) = assignSubjects($invalid[0], $invalid[1], $invalid[2]);
    checkAssignment($response['status'] === 'fail' && count($db->rows) === 0, 'Invalid selections must fail before any writes.');
}
echo "Subject class assignment checks passed.\n";
