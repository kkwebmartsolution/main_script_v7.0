<?php defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        // Ensure ONLY_FULL_GROUP_BY is removed from MySQL session mode (crucial for MySQL 8 on Hostinger/cPanel)
        $this->db->query("SET SESSION sql_mode = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(@@sql_mode, 'STRICT_ALL_TABLES,', ''), ',STRICT_ALL_TABLES', ''), 'STRICT_ALL_TABLES', ''), 'STRICT_TRANS_TABLES,', ''), ',STRICT_TRANS_TABLES', ''), 'STRICT_TRANS_TABLES', ''), 'ONLY_FULL_GROUP_BY,', ''), ',ONLY_FULL_GROUP_BY', ''), 'ONLY_FULL_GROUP_BY', '')");

        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

        if ($this->config->item('installed') == false) {
            redirect(site_url('install'));
        }

        $get_config = $this->db->get_where('global_settings', array('id' => 1))->row_array();
        $branchID = $this->application_model->get_branch_id();
        if (!empty($branchID)) {
            $branch = $this->db->select('currency_formats,symbol_position,symbol,currency,timezone')->where('id', $branchID)->get('branch')->row();
            if (!empty($branch) && is_object($branch)) {
                $get_config['currency'] = $branch->currency;
                $get_config['currency_symbol'] = !empty($branch->symbol) && $branch->symbol !== '$' ? $branch->symbol : '₹';
                $get_config['currency_formats'] = $branch->currency_formats;
                $get_config['symbol_position'] = $branch->symbol_position;
                if (!empty($branch->timezone)) {
                    $get_config['timezone'] = $branch->timezone;
                }
            }
        }
        if (empty($get_config['currency_symbol']) || $get_config['currency_symbol'] === '$') {
            $get_config['currency_symbol'] = '₹';
        }
        $this->data['global_config'] = $get_config;

        $this->data['theme_config'] = $this->db->get_where('theme_settings', array('id' => 1))->row_array();
        date_default_timezone_set($get_config['timezone']);
    }

    public function get_payment_config()
    {
        $branchID = $this->application_model->get_branch_id();
        $this->db->where('branch_id', $branchID);
        $this->db->select('*')->from('payment_config');
        return $this->db->get()->row_array();
    }

    public function getBranchDetails()
    {
        $branchID = $this->application_model->get_branch_id();
        $this->db->select('*');
        $this->db->where('id', $branchID);
        $this->db->from('branch');
        $r = $this->db->get()->row_array();
        if (empty($r)) {
            return ['stu_generate' => "", 'grd_generate' => ""];
        } else {
            return $r;
        }
    }

    public function photoHandleUpload($str, $fields)
    {
        $allowedExts = array_map('trim', array_map('strtolower', explode(',', $this->data['global_config']['image_extension'])));
        if (!in_array('webp', $allowedExts)) {
            $allowedExts[] = 'webp';
        }
        $allowedSizeKB = $this->data['global_config']['image_size'];
        $allowedSize = floatval(1024 * $allowedSizeKB);
        if (isset($_FILES["$fields"]) && !empty($_FILES["$fields"]['name'])) {
            if (isset($_FILES["$fields"]['error']) && $_FILES["$fields"]['error'] !== UPLOAD_ERR_OK) {
                if ($_FILES["$fields"]['error'] == UPLOAD_ERR_INI_SIZE || $_FILES["$fields"]['error'] == UPLOAD_ERR_FORM_SIZE) {
                    $this->form_validation->set_message('photoHandleUpload', translate('file_size_shoud_be_less_than') . " $allowedSizeKB KB.");
                    return false;
                }
                $this->form_validation->set_message('photoHandleUpload', translate('error_reading_the_file'));
                return false;
            }
            $file_size = $_FILES["$fields"]["size"];
            $file_name = $_FILES["$fields"]["name"];
            $extension = pathinfo($file_name, PATHINFO_EXTENSION);
            if (!empty($_FILES["$fields"]['tmp_name']) && file_exists($_FILES["$fields"]['tmp_name'])) {
                $files = filesize($_FILES["$fields"]['tmp_name']);
                if (!in_array(strtolower($extension), $allowedExts)) {
                    $this->form_validation->set_message('photoHandleUpload', translate('this_file_type_is_not_allowed'));
                    return false;
                }
                if ($file_size > $allowedSize) {
                    $this->form_validation->set_message('photoHandleUpload', translate('file_size_shoud_be_less_than') . " $allowedSizeKB KB.");
                    return false;
                }
            } else {
                $this->form_validation->set_message('photoHandleUpload', translate('error_reading_the_file'));
                return false;
            }
            return true;
        }
        return true;
    }

    public function fileHandleUpload($str, $fields)
    {
        $allowedExts = array_map('trim', array_map('strtolower', explode(',', $this->data['global_config']['file_extension'])));
        $allowedSizeKB = $this->data['global_config']['file_size'];
        $allowedSize = floatval(1024 * $allowedSizeKB);
        if (isset($_FILES["$fields"]) && !empty($_FILES["$fields"]['name'])) {
            $file_size = $_FILES["$fields"]["size"];
            $file_name = $_FILES["$fields"]["name"];
            $extension = pathinfo($file_name, PATHINFO_EXTENSION);
            if ($files = filesize($_FILES["$fields"]['tmp_name'])) {
                if (!in_array(strtolower($extension), $allowedExts)) {
                    $this->form_validation->set_message('fileHandleUpload', translate('this_file_type_is_not_allowed'));
                    return false;
                }
                if ($file_size > $allowedSize) {
                    $this->form_validation->set_message('fileHandleUpload', translate('file_size_shoud_be_less_than') . " $allowedSizeKB KB.");
                    return false;
                }
            } else {
                $this->form_validation->set_message('fileHandleUpload', translate('error_reading_the_file'));
                return false;
            }
            return true;
        }
    }
}

class Admin_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('saas_model');
        if (!is_loggedin()) {
            $this->session->set_userdata('redirect_url', current_url());
            redirect(base_url('authentication'), 'refresh');
        }

        if (!$this->saas_model->checkSubscriptionValidity()) {
            redirect(base_url('dashboard'));
        }
    }
}

class Dashboard_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('saas_model');
        if (!is_loggedin()) {
            $this->session->set_userdata('redirect_url', current_url());
            redirect(base_url('authentication'), 'refresh');
        }
    }
}

class User_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!is_student_loggedin() && !is_parent_loggedin()) {
            $this->session->set_userdata('redirect_url', current_url());
            redirect(base_url('authentication'), 'refresh');
        }
        $this->load->model('saas_model');
        if (!$this->saas_model->checkSubscriptionValidity()) {
            redirect(base_url('dashboard'));
        }
    }
}

class Authentication_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('authentication_model');
    }
}

class Frontend_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('home_model');
        $this->load->model('saas_model');
        $branchID = $this->home_model->getDefaultBranch();
        $db_cms_setting = $this->db->get_where('front_cms_setting', array('branch_id' => $branchID))->row_array();
        $branch_info = $this->db->select('name')->get_where('branch', array('id' => $branchID))->row_array();
        $school_name = !empty($branch_info['name']) ? $branch_info['name'] : 'School Campus';

        $default_cms_setting = array(
            'branch_id' => $branchID,
            'url_alias' => !empty($this->uri->segment(1)) ? $this->uri->segment(1) : 'default',
            'cms_active' => 1,
            'application_title' => $school_name,
            'logo' => '',
            'fav_icon' => '',
            'primary_color' => '#1b1d21',
            'hover_color' => '#3b82f6',
            'text_color' => '#333333',
            'text_secondary_color' => '#666666',
            'footer_background_color' => '#0f172a',
            'footer_text_color' => '#ffffff',
            'copyright_bg_color' => '#090d16',
            'copyright_text_color' => '#ffffff',
            'border_radius' => '8px',
            'menu_color' => '#1e293b',
            'google_analytics' => '',
            'online_admission' => 1,
            'working_hours' => 'Mon - Sat: 8:00 AM - 3:00 PM',
            'email' => '',
            'mobile_no' => '',
            'address' => '',
            'fax' => '',
            'footer_about_text' => '',
            'copyright_text' => '© ' . date('Y') . ' ' . $school_name . '. All Rights Reserved.'
        );
        $cms_setting = is_array($db_cms_setting) ? array_merge($default_cms_setting, array_filter($db_cms_setting, function($v){ return !is_null($v); })) : $default_cms_setting;

        if (empty($cms_setting['application_title']) || $cms_setting['application_title'] == 'School Website' || $cms_setting['application_title'] == 'School Management System With CMS') {
            $cms_setting['application_title'] = $school_name;
        }

        if (isset($cms_setting['cms_active']) && !$cms_setting['cms_active']) {
            redirect(site_url('authentication'));
        }
        $this->data['cms_setting'] = $cms_setting;
        $this->data['real_stats'] = $this->home_model->getSchoolRealStats($branchID);
    }

    public function load_school_static_view($view = 'index', $data = array(), $return = false)
    {
        $alias = !empty($this->data['cms_setting']['url_alias']) ? $this->data['cms_setting']['url_alias'] : 'default';
        $branch_view = "home/schools/{$alias}/{$view}";
        if (file_exists(VIEWPATH . "{$branch_view}.php")) {
            return $this->load->view($branch_view, $data, $return);
        }

        $default_view = "home/schools/default/{$view}";
        if (file_exists(VIEWPATH . "{$default_view}.php")) {
            return $this->load->view($default_view, $data, $return);
        }

        return $this->load->view("home/{$view}", $data, $return);
    }

    public function load_school_static_layout($data = array())
    {
        $alias = !empty($this->data['cms_setting']['url_alias']) ? $this->data['cms_setting']['url_alias'] : 'default';

        $branch_layout = "home/schools/{$alias}/layout/index";
        if (file_exists(VIEWPATH . "{$branch_layout}.php")) {
            $this->load->view($branch_layout, $data);
            return;
        }

        $default_layout = "home/schools/default/layout/index";
        if (file_exists(VIEWPATH . "{$default_layout}.php")) {
            $this->load->view($default_layout, $data);
            return;
        }

        $this->load->view('home/layout/index', $data);
    }
}
