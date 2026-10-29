<?php
// No real messages or database writes. Run with the curl functions disabled as below:
// php -d disable_functions=curl_init,curl_setopt,curl_exec,curl_getinfo,curl_error,curl_close tests/whatsapp_broadcast_test.php
if (strpos(ini_get('disable_functions'), 'curl_exec') === false) exit("Run with the curl functions disabled; this test must never contact a real provider.\n");
define('BASEPATH', __DIR__);
class CI_Model { public function __construct() {} }
class Admin_Controller {}
function get_session_id() { return 12; }
function is_superadmin_loggedin() { return false; }
function get_loggedin_branch_id() { return 3; }
function curl_init($url) { $GLOBALS['url'] = $url; return 1; }
function curl_setopt($ch, $key, $value) { $GLOBALS['curl_options'][$key] = $value; }
function curl_exec($ch) { return $GLOBALS['provider_response']; }
function curl_getinfo($ch, $key) { return 200; }
function curl_error($ch) { return ''; }
function curl_close($ch) {}
require __DIR__ . '/../application/models/Whatsapp_model.php';
require __DIR__ . '/../application/models/Whatsapp_broadcast_model.php';
require __DIR__ . '/../application/controllers/Whatsapp_broadcast.php';
function check($condition, $message) { if (!$condition) throw new Exception($message); }
class FakeDB {
    public $filters = array(); public $writes = array(); public $rows = array();
    public function select($s) {return $this;} public function from($s) {return $this;}
    public function join($a, $b, $c = '') {return $this;} public function order_by($s) {return $this;}
    public function where($key, $value = null) { $this->filters[] = is_array($key) ? $key : array($key => $value); return $this; }
    public function where_in($key, $value) {return $this->where($key, $value);}
    public function get() {return $this;} public function result_array() {return $this->rows;}
    public function get_where($table, $where) {$this->filters[] = $where; return $this;}
    public function row_array() {return isset($this->rows[0]) ? $this->rows[0] : array();}
    public function insert($table, $row) {$this->writes[] = $row;}
    public function insert_id() {return 5;}
    public function update($table, $row) {$this->writes[] = $row;}
}
class FakeInput {public $post = array(); public function post($key) {return isset($this->post[$key]) ? $this->post[$key] : null;}}
class FakeLoad {public function model($name) {}}
class FakeSession {
    public $job;
    public function userdata($key) {return $this->job;}
    public function set_userdata($key, $value) {$this->job = $value;}
}
class FakeOutput {
    public $body;
    public function set_status_header($code) {return $this;}
    public function set_content_type($type) {return $this;}
    public function set_output($body) {$this->body = json_decode($body, true); return $this;}
}
class FakeFees {
    public function getInvoiceDetails($id) {return array(array('allocation_id'=>1,'fee_type_id'=>1), array('allocation_id'=>2,'fee_type_id'=>1));}
    public function getBalance($allocation, $type) {return array('balance'=>$allocation === 1 ? 750.50 : -10);}
    public function getTransportBalance($id) {return array('balance'=>200);}
}
function invokePrivate($object, $method, $args = array()) {
    $r = new ReflectionMethod($object, $method); $r->setAccessible(true); return $r->invokeArgs($object, $args);
}
function rejects($callback, $label) {try {$callback();} catch (RuntimeException $e) {return;} throw new Exception('Expected rejection: ' . $label);}
$model = new Whatsapp_broadcast_model(); $model->db = new FakeDB(); $model->load = new FakeLoad(); $model->fees_model = new FakeFees();
check($model->placeholders('{{1}} {{2}} {{2}} {{3}} {{4}}') === array(1,2,3,4), 'Repeated amount must be sent only once');
$model->db->rows = array(array('id'=>9));
check($model->pending(7) === 950.50, 'Outstanding tuition plus transport; paid/overpaid fee lines excluded');
$template = array('field_map'=>json_encode(array('student_name','pending_amount','due_date','school_name')), 'fee_reminder'=>1);
$values = $model->values(array('id'=>7,'student_name'=>'Student A'), array('name'=>'School A'), $template, '2026-11-15');
check($values['parameters'] === array('Student A','950.50','15-11-2026','School A'), 'Reminder parameter order and date');
$model->db->filters = array(); $model->students(3, 8, array(7));
check(in_array(array('e.branch_id'=>3,'e.session_id'=>12,'e.class_id'=>8), $model->db->filters, true), 'Branch/session/class scoping');
check(in_array(array('e.id'=>array(7)), $model->db->filters, true), 'Selected enrollment scoping');
$controller = (new ReflectionClass('Whatsapp_broadcast'))->newInstanceWithoutConstructor();
$controller->input = new FakeInput(); $controller->db = new FakeDB(); $controller->broadcast = $model;
$controller->input->post = array('due_date'=>'2026-02-30');
rejects(function() use ($controller,$template) {invokePrivate($controller,'due_date',array($template));}, 'Invalid calendar date');
$controller->input->post['due_date'] = '2028-02-29';
check(invokePrivate($controller,'due_date',array($template)) === '2028-02-29', 'Valid leap date');
$controller->input->post = array('branch_id'=>99); $controller->db->rows = array(array('id'=>3));
invokePrivate($controller,'branch');
check(end($controller->db->filters) === array('id'=>3), 'Staff cannot override their branch');
$controller->input->post = array('template_id'=>123); $controller->db->rows = array();
rejects(function() use ($controller) {invokePrivate($controller,'template',array(3));}, 'Unknown/cross-branch template');
check(end($controller->db->filters) === array('id'=>123,'branch_id'=>3), 'Template belongs to branch');
$wa = new Whatsapp_model(); $wa->db = new FakeDB();
check($wa->format_number('98765 43210') === '919876543210', 'Indian number normalization');
check($wa->format_number('12') === false, 'Invalid number rejected');
$GLOBALS['provider_response'] = '{"messages":[{"id":"mock-message"}]}';
$r = $wa->send_direct('https://mock.invalid/api/v1', 'test-key', '919876543210', 'fee_payment_reminder', $values['parameters'], 3, 'en_US');
$payload = json_decode($GLOBALS['curl_options'][CURLOPT_POSTFIELDS], true);
check($r['status'] === true && $GLOBALS['url'] === 'https://mock.invalid/api/v1/messages', 'Provider acceptance');
check($payload['template']['language']['code'] === 'en_US', 'Exact approved language');
check(count($payload['template']['components'][0]['parameters']) === 4, 'Four unique parameters');
check($payload['template']['components'][0]['parameters'][2]['text'] === '15-11-2026', 'Due date payload');
$GLOBALS['provider_response'] = '{"success":true}';
check(!$wa->send_direct('https://mock.invalid/api/v1','test','919876543210','notice',array(),3)['status'], 'HTTP 200 without a message ID is not acceptance');
$payload = json_decode($GLOBALS['curl_options'][CURLOPT_POSTFIELDS], true);
check(!isset($payload['template']['components']), 'Static template has no empty body component');
$controller->session = new FakeSession(); $controller->output = new FakeOutput();
$controller->session->job = array('branch_id'=>3,'session_id'=>12,'token'=>'abc','class_id'=>8,'ids'=>array(7,8), 'next'=>1,'accepted'=>1,'failed'=>0,'skipped'=>0,'last'=>'Accepted');
$controller->input->post = array('token'=>'abc','next'=>0);
invokePrivate($controller, 'step', array(array('id'=>3,'whatsapp_status'=>1,'whatsapp_api_key'=>'test','whatsapp_api_url'=>'https://mock.invalid')));
check($controller->output->body['next'] === 1 && $controller->output->body['accepted'] === 1, 'Replayed step returns current progress without sending');
$controller->input->post['token'] = 'wrong';
rejects(function() use ($controller) {invokePrivate($controller, 'step', array(array('id'=>3)));}, 'Wrong campaign token');
echo "PASS: reminder mapping, balance, dates, tenant scoping, phone validation and mocked provider payload/acceptance. No messages sent.\n";
