<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Whatsapp_broadcast_model extends CI_Model
{
    public function ensure_schema()
    {
        // The reminder contains emoji; the application's default connection uses utf8mb3.
        $this->db->db_set_charset('utf8mb4');
        if ($this->db->table_exists('whatsapp_broadcast_templates')) return;
        $this->db->query("CREATE TABLE IF NOT EXISTS whatsapp_broadcast_templates (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            branch_id INT NOT NULL,
            name VARCHAR(150) NOT NULL,
            language VARCHAR(20) NOT NULL,
            body TEXT NOT NULL,
            field_map TEXT NOT NULL,
            fee_reminder TINYINT NOT NULL DEFAULT 0,
            UNIQUE KEY branch_template (branch_id, name, language)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function fields()
    {
        return array('student_name' => 'Student name', 'pending_amount' => 'Pending fees (excluding fines)',
            'due_date' => 'Due date selected when sending', 'school_name' => 'School name',
            'parent_name' => 'Parent name', 'class_name' => 'Class', 'section_name' => 'Section',
            'register_no' => 'Admission number');
    }

    public function placeholders($body)
    {
        preg_match_all('/\{\{\s*(\d+)\s*\}\}/', $body, $matches);
        $ids = array_values(array_unique(array_map('intval', $matches[1])));
        sort($ids);
        return $ids;
    }

    public function students($branch, $class, $ids = array())
    {
        $this->db->select("e.id, s.register_no, s.mobileno, CONCAT_WS(' ', s.first_name, s.last_name) AS student_name, p.name AS parent_name, p.mobileno AS parent_mobile, c.name AS class_name, sec.name AS section_name");
        $this->db->from('enroll e');
        $this->db->join('student s', 's.id=e.student_id');
        $this->db->join('login_credential l', "l.user_id=s.id AND l.role=7 AND l.active=1");
        $this->db->join('parent p', 'p.id=s.parent_id AND p.branch_id=e.branch_id', 'left');
        $this->db->join('class c', 'c.id=e.class_id');
        $this->db->join('section sec', 'sec.id=e.section_id', 'left');
        $this->db->where(array('e.branch_id' => $branch, 'e.session_id' => get_session_id(), 'e.class_id' => $class));
        if ($ids) $this->db->where_in('e.id', $ids);
        return $this->db->order_by('s.first_name')->get()->result_array();
    }

    public function pending($enroll)
    {
        $this->load->model('fees_model');
        $balance = 0;
        foreach ($this->fees_model->getInvoiceDetails($enroll) as $fee) {
            $b = $this->fees_model->getBalance($fee['allocation_id'], $fee['fee_type_id']);
            $balance += max(0, (float) $b['balance']);
        }
        $transport = $this->db->select('t.id')->from('transport_fee_details t')
            ->join('transport_stoppage_point p', 'p.id=t.stoppage_point_id')
            ->where('t.enroll_id', $enroll)->get()->result_array();
        foreach ($transport as $fee) {
            $b = $this->fees_model->getTransportBalance($fee['id']);
            $balance += max(0, (float) $b['balance']);
        }
        return round($balance, 2);
    }

    public function values($student, $branch, $template, $date)
    {
        $map = json_decode($template['field_map'], true);
        $student['school_name'] = $branch['name'];
        $student['due_date'] = $date ? date('d-m-Y', strtotime($date)) : '';
        if (in_array('pending_amount', $map, true) || $template['fee_reminder']) {
            $student['pending_amount'] = number_format($this->pending($student['id']), 2, '.', '');
        }
        $params = array();
        foreach ($map as $field) $params[] = isset($student[$field]) ? (string) $student[$field] : '';
        return array('parameters' => $params, 'pending' => isset($student['pending_amount']) ? (float) $student['pending_amount'] : null);
    }
}
