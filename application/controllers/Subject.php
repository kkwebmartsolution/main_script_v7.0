<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @package : Ramom school management system
 * @version : 6.5
 * @developed by : RamomCoder
 * @support : ramomcoder@yahoo.com
 * @author url : http://codecanyon.net/user/RamomCoder
 * @filename : Subject.php
 * @copyright : Reserved RamomCoder Team
 */

class Subject extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('subject_model');
    }

    public function index()
    {
        if (!get_permission('subject', 'is_view')) {
            access_denied();
        }
        $this->data['subjectlist'] = $this->app_lib->getTable('subject');
        $this->data['title'] = translate('subject');
        $this->data['sub_page'] = 'subject/index';
        $this->data['main_menu'] = 'subject';
        $this->load->view('layout/index', $this->data);
    }

    // subject edit page
    public function edit($id = '')
    {
        if (!get_permission('subject', 'is_edit')) {
            access_denied();
        }

        $this->data['subject'] = $this->app_lib->getTable('subject', array('t.id' => $id), true);
        $this->data['title'] = translate('subject');
        $this->data['sub_page'] = 'subject/edit';
        $this->data['main_menu'] = 'subject';
        $this->load->view('layout/index', $this->data);
    }

    // moderator subject all information
    public function save()
    {
        if ($_POST) {
            if (is_superadmin_loggedin()) {
                $this->form_validation->set_rules('branch_id', translate('branch'), 'required');
            }
            $this->form_validation->set_rules('name', translate('subject_name'), 'trim|required');
            $this->form_validation->set_rules('subject_code', translate('subject_code'), 'trim|required');
            $this->form_validation->set_rules('subject_type', translate('subject_type'), 'trim|required');
            if ($this->form_validation->run() !== false) {
                $arraySubject = array(
                    'name' => $this->input->post('name'),
                    'subject_code' => $this->input->post('subject_code'),
                    'subject_type' => $this->input->post('subject_type'),
                    'subject_author' => $this->input->post('subject_author'),
                    'branch_id' => $this->application_model->get_branch_id(),
                );
                $subjectID = $this->input->post('subject_id');
                if (empty($subjectID)) {
                    if (get_permission('subject', 'is_add')) {
                        $this->db->insert('subject', $arraySubject);
                    }
                    set_alert('success', translate('information_has_been_saved_successfully'));
                } else {
                    if (get_permission('subject', 'is_edit')) {
                        if (!is_superadmin_loggedin()) {
                            $this->db->where('branch_id', get_loggedin_branch_id());
                        }
                        $this->db->where('id', $subjectID);
                        $this->db->update('subject', $arraySubject);
                    }
                    set_alert('success', translate('information_has_been_updated_successfully'));
                }
                $url = base_url('subject/index');
                $array = array('status' => 'success', 'url' => $url);
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'error' => $error);
            }
            echo json_encode($array);
        }
    }

    public function delete($id = '')
    {
        if (get_permission('subject', 'is_delete')) {
            $this->app_lib->check_branch_restrictions('subject', $id);
            $this->db->where('id', $id);
            $this->db->delete('subject');
            $this->db->where('subject_id', $id);
            $this->db->delete('subject_assign');
        }
    }

    // add subject assign information and delete
    public function class_assign()
    {
        if (!get_permission('subject_class_assign', 'is_view')) {
            access_denied();
        }

        $this->data['branch_id'] = $this->application_model->get_branch_id();
        $this->data['assignlist'] = $this->subject_model->getAssignList();
        $this->data['title'] = translate('class_assign');
        $this->data['sub_page'] = 'subject/class_assign';
        $this->data['main_menu'] = 'subject';
        $this->load->view('layout/index', $this->data);
    }

    // moderator class assign save all information
    public function class_assign_save()
    {
        if ($_POST) {
            if (get_permission('subject_class_assign', 'is_add')) {
                foreach (array('class_ids', 'subjects') as $field) {
                    $values = $this->input->post($field);
                    if (!is_array($values) || empty($values) || count(array_filter($values, function ($value) {
                        return is_scalar($value) && ctype_digit((string) $value) && (int) $value > 0;
                    })) !== count($values)) {
                        echo json_encode(array('status' => 'fail', 'url' => '', 'error' => array($field . '[]' => 'Please select at least one valid item.')));
                        return;
                    }
                }
                if (!is_string($this->input->post('section_id'))) {
                    echo json_encode(array('status' => 'fail', 'url' => '', 'error' => array('section_id' => 'Please select a section.')));
                    return;
                }
                if (is_superadmin_loggedin()) {
                    $this->form_validation->set_rules('branch_id', translate('branch'), 'required');
                }
                $this->form_validation->set_rules('class_ids[]', translate('class'), 'required|integer');
                $this->form_validation->set_rules('section_id', translate('section'), 'trim|required');
                $this->form_validation->set_rules('subjects[]', translate('subject'), 'trim|required');
                if ($this->form_validation->run() !== false) {
                    $branchID = $this->application_model->get_branch_id();
                    $classIDs = $this->input->post('class_ids');
                    $subjects = $this->input->post('subjects');
                    $sectionID = $this->input->post('section_id');
                    $error = array();
                    $targets = array();
                    if (!is_array($classIDs) || !is_array($subjects)) {
                        $error['class_ids[]'] = 'Please select classes and subjects.';
                    } else {
                        $classIDs = array_unique($classIDs);
                        $allowedClasses = $this->app_lib->getClass($branchID, false);
                        foreach ($classIDs as $classID) {
                            if (!isset($allowedClasses[$classID]) || !$this->db->get_where('class', array('id' => $classID, 'branch_id' => $branchID))->num_rows()) {
                                $error['class_ids[]'] = 'Please select valid classes for this branch.';
                                break;
                            }
                            $sections = $this->app_lib->getSections($classID);
                            unset($sections[''], $sections['all']);
                            if (empty($sections)) {
                                $error['class_ids[]'] = 'Every selected class must have an available section.';
                                break;
                            }
                            if (count($classIDs) === 1 && $sectionID !== 'all') {
                                if (!isset($sections[$sectionID])) {
                                    $error['section_id'] = 'Please select a valid section for this class.';
                                    break;
                                }
                                $sections = array($sectionID => $sections[$sectionID]);
                            }
                            foreach ($sections as $id => $name) {
                                $targets[] = array('class_id' => $classID, 'section_id' => $id,
                                    'session_id' => get_session_id(), 'branch_id' => $branchID);
                            }
                        }
                        foreach ($subjects as $subject) {
                            if (!is_scalar($subject) || !ctype_digit((string) $subject) || !$this->db->get_where('subject', array('id' => $subject, 'branch_id' => $branchID))->num_rows()) {
                                $error['subjects[]'] = 'Please select valid subjects for this branch.';
                                break;
                            }
                        }
                    }
                    if (!empty($error)) {
                        echo json_encode(array('status' => 'fail', 'url' => '', 'error' => $error));
                        return;
                    }
                    $this->db->trans_start();
                    foreach ($targets as $target) {
                        $teacher = $this->subject_model->get('teacher_allocation', $target, true);
                        foreach (array_unique($subjects) as $subject) {
                            $assignment = array_merge($target, array('subject_id' => $subject));
                            if (!$this->db->get_where('subject_assign', $assignment)->num_rows()) {
                                $assignment['teacher_id'] = empty($teacher) ? 0 : $teacher['teacher_id'];
                                $this->db->insert('subject_assign', $assignment);
                            }
                        }
                    }
                    $this->db->trans_complete();
                    if ($this->db->trans_status() === false) {
                        echo json_encode(array('status' => 'fail', 'url' => '', 'error' => array('subjects[]' => 'Unable to save assignments. Please try again.')));
                        return;
                    }
                    set_alert('success', translate('information_has_been_saved_successfully'));
                    $url = base_url('subject/class_assign');
                    $array = array('status' => 'success', 'url' => $url, 'error' => '');
                } else {
                    $error = $this->form_validation->error_array();
                    $array = array('status' => 'fail', 'url' => '', 'error' => $error);
                }
                echo json_encode($array);
            }
        }
    }

    // subject assign information edit
    public function class_assign_edit()
    {
        if ($_POST) {
            if (get_permission('subject_class_assign', 'is_edit')) {
                $this->form_validation->set_rules('subjects[]', translate('subject'), 'trim|required');
                if ($this->form_validation->run() !== false) {
                    $sessionID = get_session_id();
                    $classID = $this->input->post('class_id');
                    $sectionID = $this->input->post('section_id');
                    $branchID = $this->application_model->get_branch_id();
                    $arraySubject = array(
                        'class_id' => $classID,
                        'section_id' => $sectionID,
                        'session_id' => $sessionID,
                        'branch_id' => $branchID,
                    );
                    // get class teacher details
                    $get_teacher = $this->subject_model->get('teacher_allocation', $arraySubject, true);

                    $subjects = $this->input->post('subjects');
                    foreach ($subjects as $subject) {
                        $arraySubject['subject_id'] = $subject;
                        $query = $this->db->get_where("subject_assign", $arraySubject);
                        if ($query->num_rows() == 0) {
                            $arraySubject['teacher_id'] = empty($get_teacher) ? 0 : $get_teacher['teacher_id'];
                            $this->db->insert('subject_assign', $arraySubject);
                        }
                    }
                    $this->db->where_not_in('subject_id', $subjects);
                    $this->db->where('class_id', $classID);
                    $this->db->where('section_id', $sectionID);
                    $this->db->where('session_id', $sessionID);
                    $this->db->where('branch_id', $branchID);
                    $this->db->delete('subject_assign');
                    set_alert('success', translate('information_has_been_updated_successfully'));
                    $url = base_url('subject/class_assign');
                    $array = array('status' => 'success', 'url' => $url, 'error' => '');
                } else {
                    $error = $this->form_validation->error_array();
                    $array = array('status' => 'fail', 'url' => '', 'error' => $error);
                }
                echo json_encode($array);
            }
        }
    }

    public function class_assign_delete($class_id = '', $section_id = '')
    {
        if (!get_permission('subject_class_assign', 'is_delete')) {
            access_denied();
        }
        if (!is_superadmin_loggedin()) {
            $this->db->where('branch_id', get_loggedin_branch_id());
        }
        $this->db->where('class_id', $class_id);
        $this->db->where('section_id', $section_id);
        $this->db->where('session_id', get_session_id());
        $this->db->delete('subject_assign');
    }

    // validate here, if the check class assign
    public function unique_subject_assign($class_id)
    {
        $where = array(
            'class_id' => $class_id,
            'section_id' => $this->input->post('section_id'),
            'session_id' => get_session_id(),
        );
        $q = $this->db->get_where('subject_assign', $where)->num_rows();
        if ($q == 0) {
            return true;
        } else {
            $this->form_validation->set_message('unique_subject_assign', 'This class and section is already assigned.');
            return false;
        }
    }

    // teacher assign view page
    public function teacher_assign()
    {
        if (!get_permission('subject_teacher_assign', 'is_view')) {
            access_denied();
        }
        if ($_POST) {
            if (get_permission('subject_teacher_assign', 'is_add')) {
                if (is_superadmin_loggedin()) {
                    $this->form_validation->set_rules('branch_id', translate('branch'), 'required');
                }
                $this->form_validation->set_rules('staff_id', translate('teacher'), 'trim|required');
                $this->form_validation->set_rules('class_id', translate('class'), 'trim|required');
                $this->form_validation->set_rules('section_id', translate('section'), 'trim|required');
                $this->form_validation->set_rules('subject_id', translate('subject'), 'trim|required');
                if ($this->form_validation->run() !== false) {
                    $sessionID = get_session_id();
                    $branchID = $this->application_model->get_branch_id();
                    $classID = $this->input->post('class_id');
                    $sectionID = $this->input->post('section_id');
                    $subjectID = $this->input->post('subject_id');
                    $teacherID = $this->input->post('staff_id');
                    $query = $this->db->get_where("subject_assign", array(
                        'class_id' => $classID,
                        'section_id' => $sectionID,
                        'subject_id' => $subjectID,
                        'session_id' => $sessionID,
                        'branch_id' => $branchID,
                    ));
                    if ($query->num_rows() != 0) {
                        $this->db->where('id', $query->row()->id);
                        $this->db->update('subject_assign', array('teacher_id' => $teacherID));
                    }
                    set_alert('success', translate('information_has_been_updated_successfully'));
                    $url = base_url('subject/teacher_assign');
                    $array = array('status' => 'success', 'url' => $url, 'error' => '');
                } else {
                    $error = $this->form_validation->error_array();
                    $array = array('status' => 'fail', 'url' => '', 'error' => $error);
                }
                echo json_encode($array);
                exit();
            }
        }

        $this->data['branch_id'] = $this->application_model->get_branch_id();
        $this->data['assignlist'] = $this->subject_model->getTeacherAssignList();
        $this->data['title'] = translate('teacher_assign');
        $this->data['sub_page'] = 'subject/teacher_assign';
        $this->data['main_menu'] = 'subject';
        $this->load->view('layout/index', $this->data);
    }

    // teacher assign information moderator
    public function teacher_assign_delete($id = '')
    {
        if (get_permission('subject_teacher_assign', 'is_delete')) {
            if (!is_superadmin_loggedin()) {
                $this->db->where('branch_id', get_loggedin_branch_id());
            }
            $this->db->where('id', $id);
            $this->db->update('subject_assign', array('teacher_id' => 0));
        }
    }

    // get subject list based on class section
    public function getByClassSection()
    {
        $html = '';
        $classID = $this->input->post('classID');
        $sectionID = $this->input->post('sectionID');
        if (!empty($classID)) {
            $query = $this->subject_model->getSubjectByClassSection($classID, $sectionID);
            if ($query->num_rows() > 0) {
                $html .= '<option value="">' . translate('select') . '</option>';
                $subjects = $query->result_array();
                foreach ($subjects as $row) {
                    $html .= '<option value="' . $row['subject_id'] . '">' . $row['subjectname'] . " (" . $row['subject_code'] . ')</option>';
                }
            } else {
                $html .= '<option value="">' . translate('no_information_available') . '</option>';
            }
        } else {
            $html .= '<option value="">' . translate('select') . '</option>';
        }
        echo $html;
    }
}
