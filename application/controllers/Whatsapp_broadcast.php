<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Whatsapp_broadcast extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!moduleIsEnabled('bulk_sms_and_email') || !get_permission('sendsmsmail', 'is_add')) access_denied();
        $this->load->model('whatsapp_broadcast_model', 'broadcast');
        $this->load->model('whatsapp_model');
        $this->broadcast->ensure_schema();
    }

    private function reply($data, $code = 200)
    {
        $this->output->set_status_header($code)->set_content_type('application/json')->set_output(json_encode($data));
    }

    private function branch()
    {
        $id = is_superadmin_loggedin() ? (int) $this->input->post('branch_id') : (int) get_loggedin_branch_id();
        $branch = $this->db->get_where('branch', array('id' => $id))->row_array();
        if (!$branch) throw new RuntimeException('Select a valid branch.');
        return $branch;
    }

    private function template($branch)
    {
        $t = $this->db->get_where('whatsapp_broadcast_templates', array('id' => (int) $this->input->post('template_id'), 'branch_id' => $branch))->row_array();
        if (!$t) throw new RuntimeException('Select a saved approved template.');
        return $t;
    }

    private function due_date($template)
    {
        $date = (string) $this->input->post('due_date');
        if (in_array('due_date', json_decode($template['field_map'], true), true)) {
            $d = DateTime::createFromFormat('!Y-m-d', $date);
            if (!$d || $d->format('Y-m-d') !== $date) throw new RuntimeException('Select a valid payment due date.');
        }
        return $date;
    }

    public function index()
    {
        $this->data['branches'] = is_superadmin_loggedin() ? $this->db->select('id,name')->get('branch')->result_array() : array();
        $this->data['branch_id'] = is_superadmin_loggedin() ? '' : get_loggedin_branch_id();
        $this->data['fields'] = $this->broadcast->fields();
        $this->data['title'] = 'WhatsApp Template Message Send';
        $this->data['sub_page'] = 'sendsmsmail/whatsapp';
        $this->data['main_menu'] = 'sendsmsmail';
        $this->load->view('layout/index', $this->data);
    }

    public function action()
    {
        if ($this->input->method() !== 'post') { $this->reply(array('error' => 'POST required.'), 405); return; }
        try {
            $branch = $this->branch();
            $action = $this->input->post('action');
            if ($action === 'load') {
                $job = $this->session->userdata('wa_broadcast_job');
                $this->reply(array('templates' => $this->db->get_where('whatsapp_broadcast_templates', array('branch_id' => $branch['id']))->result_array(),
                    'classes' => $this->db->select('id,name')->get_where('class', array('branch_id' => $branch['id']))->result_array(),
                    'job' => $job && $job['branch_id'] == $branch['id'] ? $this->progress($job) : null));
            } elseif ($action === 'save_template') {
                $this->save_template($branch['id']);
            } elseif ($action === 'students') {
                $this->reply(array('students' => $this->broadcast->students($branch['id'], (int) $this->input->post('class_id'))));
            } elseif ($action === 'preview' || $action === 'start') {
                $template = $this->template($branch['id']);
                $date = $this->due_date($template);
                $mode = $this->input->post('student_mode');
                if (!in_array($mode, array('all', 'selected'), true)) throw new RuntimeException('Choose all or selected students.');
                $ids = $this->input->post('students');
                $ids = is_array($ids) ? array_values(array_unique(array_filter(array_map('intval', $ids)))) : array();
                if ($mode === 'selected' && !$ids) throw new RuntimeException('Select at least one student.');
                $class = (int) $this->input->post('class_id');
                $students = $this->broadcast->students($branch['id'], $class, $mode === 'selected' ? $ids : array());
                if (!$students || ($mode === 'selected' && count($students) !== count($ids))) throw new RuntimeException('Select valid students from this class and current session.');
                if ($action === 'preview') {
                    $values = $this->broadcast->values($students[0], $branch, $template, $date);
                    $body = preg_replace_callback('/\{\{\s*(\d+)\s*\}\}/', function ($m) use ($values) { return $values['parameters'][(int) $m[1] - 1]; }, $template['body']);
                    $this->reply(array('body' => $body, 'student' => $students[0]['student_name'], 'count' => count($students)));
                    return;
                }
                if (empty($branch['whatsapp_api_key']) || empty($branch['whatsapp_api_url']) || (isset($branch['whatsapp_status']) && !$branch['whatsapp_status'])) throw new RuntimeException('Enable WhatsApp and save the API URL and key in School Settings.');
                $target = $this->input->post('recipient');
                if (!in_array($target, array('student', 'parent'), true)) throw new RuntimeException('Choose a recipient type.');
                $old = $this->session->userdata('wa_broadcast_job');
                if ($old && $old['next'] < count($old['ids'])) throw new RuntimeException('Resume the existing broadcast before starting another.');
                $job = array('token' => bin2hex(random_bytes(16)), 'branch_id' => $branch['id'], 'session_id' => get_session_id(), 'class_id' => $class,
                    'template' => $template, 'date' => $date, 'recipient' => $target, 'ids' => array_column($students, 'id'), 'next' => 0,
                    'accepted' => 0, 'failed' => 0, 'skipped' => 0, 'last' => '');
                $this->session->set_userdata('wa_broadcast_job', $job);
                $this->reply($this->progress($job));
            } elseif ($action === 'step') {
                $this->step($branch);
            } elseif ($action === 'cancel') {
                $job = $this->session->userdata('wa_broadcast_job');
                if (!$job || $job['branch_id'] != $branch['id'] || !hash_equals($job['token'], (string) $this->input->post('token'))) throw new RuntimeException('Broadcast session expired.');
                $this->session->unset_userdata('wa_broadcast_job');
                $this->reply(array('cancelled' => true));
            } else throw new RuntimeException('Invalid action.');
        } catch (RuntimeException $e) {
            $this->reply(array('error' => $e->getMessage()), 422);
        }
    }

    private function save_template($branch)
    {
        $name = trim((string) $this->input->post('name'));
        $language = trim((string) $this->input->post('language'));
        $body = trim((string) $this->input->post('body'));
        if (!preg_match('/^[a-z0-9_]{1,150}$/D', $name) || !preg_match('/^[a-z]{2,3}(?:_[A-Z]{2})?$/D', $language) || !$body || strlen($body) > 10000) throw new RuntimeException('Enter the exact approved template name, language code and body.');
        if ($this->input->post('approved') !== '1') throw new RuntimeException('Confirm this template is already approved by WhatsApp.');
        $ids = $this->broadcast->placeholders($body);
        $rest = preg_replace('/\{\{\s*\d+\s*\}\}/', '', $body);
        if (strpos($rest, '{{') !== false || strpos($rest, '}}') !== false || ($ids && $ids !== range(1, count($ids)))) throw new RuntimeException('Use sequential numbered body variables: {{1}}, {{2}}, etc.');
        $map = $this->input->post('mapping');
        $map = is_array($map) ? array_values($map) : array();
        if (count($map) !== count($ids) || array_diff($map, array_keys($this->broadcast->fields()))) throw new RuntimeException('Map every template variable to a field.');
        $reminder = $this->input->post('fee_reminder') === '1';
        if ($reminder && (!in_array('pending_amount', $map, true) || !in_array('due_date', $map, true))) throw new RuntimeException('Fee reminders need pending amount and due date variables.');
        $data = array('branch_id' => $branch, 'name' => $name, 'language' => $language, 'body' => $body, 'field_map' => json_encode($map), 'fee_reminder' => (int) $reminder);
        $existing = $this->db->get_where('whatsapp_broadcast_templates', array('branch_id' => $branch, 'name' => $name, 'language' => $language))->row_array();
        if ($existing) $this->db->where('id', $existing['id'])->update('whatsapp_broadcast_templates', $data);
        else $this->db->insert('whatsapp_broadcast_templates', $data);
        $this->reply(array('saved' => true));
    }

    private function progress($job)
    {
        return array_intersect_key($job, array_flip(array('token', 'next', 'accepted', 'failed', 'skipped', 'last'))) + array('total' => count($job['ids']));
    }

    private function step($branch)
    {
        // The CI session lock serializes requests; the expected index prevents replay sends.
        $job = $this->session->userdata('wa_broadcast_job');
        if (!$job || $job['branch_id'] != $branch['id'] || !hash_equals($job['token'], (string) $this->input->post('token'))) throw new RuntimeException('Broadcast session expired. Reload the page.');
        if ($job['session_id'] != get_session_id()) throw new RuntimeException('Return to the academic session in which this broadcast was started.');
        if (empty($branch['whatsapp_status']) || empty($branch['whatsapp_api_key']) || empty($branch['whatsapp_api_url'])) throw new RuntimeException('WhatsApp is disabled or its API configuration is missing.');
        if ((int) $this->input->post('next') !== $job['next'] || $job['next'] >= count($job['ids'])) { $this->reply($this->progress($job)); return; }
        $students = $this->broadcast->students($branch['id'], $job['class_id'], array($job['ids'][$job['next']]));
        $status = 'skipped';
        $message = 'Student no longer active in this class.';
        if ($students) {
            $s = $students[0];
            $values = $this->broadcast->values($s, $branch, $job['template'], $job['date']);
            $phone = $this->whatsapp_model->format_number($job['recipient'] === 'parent' ? $s['parent_mobile'] : $s['mobileno']);
            $message = $s['student_name'] . ': ';
            if ($job['template']['fee_reminder'] && $values['pending'] <= 0) $message .= 'Skipped — no pending fees.';
            elseif (!$phone) $message .= 'Skipped — missing or invalid mobile number.';
            elseif (in_array('', $values['parameters'], true)) $message .= 'Skipped — a required database value is missing.';
            else {
                $r = $this->whatsapp_model->send_direct($branch['whatsapp_api_url'], $branch['whatsapp_api_key'], $phone, $job['template']['name'], $values['parameters'], $branch['id'], $job['template']['language']);
                $status = $r['status'] ? 'accepted' : 'failed';
                $message .= $r['status'] ? 'Accepted by WhatsApp provider.' : 'Not confirmed by provider (HTTP ' . $r['http_code'] . ', log #' . $r['queue_id'] . ').';
            }
        }
        $job[$status]++;
        $job['next']++;
        $job['last'] = $message;
        $this->session->set_userdata('wa_broadcast_job', $job);
        $this->reply($this->progress($job));
    }
}
