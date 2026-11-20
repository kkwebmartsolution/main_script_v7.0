<?php
define('BASEPATH', __DIR__);
class MY_Model { public function __construct() {} }
class Admin_Controller {}
function translate($key) { return $key; }
function get_permission($module, $action) { return !empty($GLOBALS['permissions'][$action]); }
function access_denied() { throw new RuntimeException('Access denied'); }
require_once __DIR__ . '/../application/models/Student_model.php';
require_once __DIR__ . '/../application/controllers/Student.php';

function check($condition, $message) {
    if (!$condition) throw new RuntimeException($message);
}
class GuardianDb {
    public $parents = [7 => ['id' => 7, 'branch_id' => 2, 'name' => 'Old', 'photo' => 'existing.png']];
    public $credentials = [];
    private $where = [];
    public function where($where) { $this->where = $where; return $this; }
    public function get($table) { return $this; }
    public function row_array() {
        $where = $this->where; $this->where = [];
        foreach ($this->parents as $row) {
            if (!array_diff_assoc($where, $row)) return $row;
        }
        return [];
    }
    public function update($table, $values) {
        $row = $this->row_array();
        if ($row) $this->parents[$row['id']] = array_merge($row, $values);
    }
    public function insert($table, $values) {
        if ($table === 'parent') $this->parents[8] = ['id' => 8] + $values;
        else $this->credentials[] = $values;
    }
    public function insert_id() { return 8; }
}
class GuardianValidation {
    public $rules = [];
    public function set_rules($field, $label, $rules) { $this->rules[$field] = $rules; }
}
class GuardianController extends Student {
    public $form_validation;
    public function __construct() { $this->form_validation = new GuardianValidation(); }
    public function rules() { $this->guardian_details_validation(); return $this->form_validation->rules; }
}
class GuardianModel extends Student_model {
    public $db;
    public $app_lib;
}
$rules = (new GuardianController())->rules();
$required = array_keys(array_filter($rules, function ($rule) { return in_array('required', explode('|', $rule)); }));
check($required === ['father_name', 'grd_mobileno'], 'Only father name and phone must be required.');

$GLOBALS['permissions'] = ['is_edit' => true, 'is_add' => true];
$model = new GuardianModel();
$model->db = new GuardianDb();
$model->app_lib = new class {
    public function pass_hashed($password) { return password_hash($password, PASSWORD_DEFAULT); }
};
$data = ['father_name' => ' Father ', 'grd_mobileno' => '+91 9876543210', 'parent_id' => 99];
$id = $model->saveProfileGuardian(['parent_id' => 7, 'branch_id' => 2], $data);
check($id === 7 && count($model->db->parents) === 1, 'Editing must retain the linked guardian and ignore submitted IDs.');
check($model->db->parents[7]['father_name'] === 'Father', 'Father name must be saved.');
check($model->db->parents[7]['name'] === 'Father', 'An optional empty guardian name should display the father name.');
check($model->db->parents[7]['photo'] === 'existing.png', 'Editing details must preserve existing photo.');
check(!$model->db->credentials, 'Editing must preserve login credentials.');
check($model->db->parents[7]['occupation'] === '' && $model->db->parents[7]['relation'] === '', 'Optional details can be blank.');

$id = $model->saveProfileGuardian(['parent_id' => 0, 'branch_id' => 2], $data);
check($id === 8 && $model->db->parents[8]['branch_id'] === 2, 'Missing guardian must be created in the student branch.');
check(count($model->db->credentials) === 1 && $model->db->credentials[0]['user_id'] === 8, 'New guardian must have a parent login record.');

$GLOBALS['permissions'] = ['is_edit' => false, 'is_add' => false];
$denied = false;
try { $model->saveProfileGuardian(['parent_id' => 7, 'branch_id' => 2], $data); }
catch (RuntimeException $e) { $denied = true; }
check($denied, 'Guardian changes must require parent permission.');
$GLOBALS['permissions'] = ['is_edit' => true, 'is_add' => false];
$denied = false;
try { $model->saveProfileGuardian(['parent_id' => 7, 'branch_id' => 3], $data); }
catch (RuntimeException $e) { $denied = true; }
check($denied, 'A guardian from another branch must never be edited.');
echo "Student guardian regression checks passed.\n";
