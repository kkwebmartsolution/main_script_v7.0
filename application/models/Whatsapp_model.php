<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Whatsapp_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        // Ensure timezone is set to Indian Standard Time (Asia/Kolkata)
        date_default_timezone_set('Asia/Kolkata');
    }

    /**
     * Send WhatsApp Fee Payment Notification
     * 
     * Template: payment_rechived
     * "Dear {{1}},
     * We have successfully received your payment of ₹{{2}} for {{3}}.
     * Payment Date: {{4}}
     * Receipt No.: {{5}}
     * Thank you for your payment."
     *
     * @param int|string $studentID Enroll ID or Student ID
     * @param float|string $totalAmount Consolidated amount paid
     * @param string $feeTypes Description of fee type(s) / months
     * @param string $paymentDate Formatted payment date
     * @param string $receiptNo Receipt / invoice number
     * @param int|null $branchID Branch / School ID (optional)
     * @return array Array of sending results
     */
    public function send_fee_payment_notification($studentID, $totalAmount, $feeTypes, $paymentDate = '', $receiptNo = '', $branchID = null)
    {
        // 1. Fetch student details
        $student = $this->application_model->getStudentDetails($studentID, true);
        if (empty($student)) {
            $student = $this->application_model->getStudentDetails($studentID, false);
        }

        if (empty($student)) {
            log_message('error', "Whatsapp_model: Student details not found for ID: " . $studentID);
            return array('status' => false, 'message' => 'Student not found');
        }

        // 2. Resolve branch ID and configuration
        if (empty($branchID)) {
            $branchID = !empty($student['branch_id']) ? $student['branch_id'] : $this->application_model->get_branch_id();
        }

        $branch = $this->db->get_where('branch', array('id' => $branchID))->row_array();
        if (empty($branch)) {
            log_message('error', "Whatsapp_model: Branch not found for ID: " . $branchID);
            return array('status' => false, 'message' => 'Branch not found');
        }

        // Check if WhatsApp is enabled for this school
        $whatsapp_status = isset($branch['whatsapp_status']) ? intval($branch['whatsapp_status']) : 1;
        if ($whatsapp_status !== 1) {
            return array('status' => false, 'message' => 'WhatsApp notifications disabled for branch ' . $branchID);
        }

        $apiKey = !empty($branch['whatsapp_api_key']) ? trim($branch['whatsapp_api_key']) : 'kkwaba_live_6Zl4DD-P-JylxkORDszcDvDg_jreH6RS0OdPGdWKx_0';
        $apiUrl = !empty($branch['whatsapp_api_url']) ? trim($branch['whatsapp_api_url']) : 'https://waba.kkwebmart.in/api/v1';
        $templateName = !empty($branch['whatsapp_template_name']) ? trim($branch['whatsapp_template_name']) : 'payment_rechived';

        if (empty($apiKey)) {
            log_message('error', "Whatsapp_model: API key missing for branch ID: " . $branchID);
            return array('status' => false, 'message' => 'WhatsApp API key missing');
        }

        // 3. Resolve recipient mobile numbers
        $notify_student = isset($branch['whatsapp_student_notification']) ? intval($branch['whatsapp_student_notification']) : 1;
        $notify_parent = isset($branch['whatsapp_parent_notification']) ? intval($branch['whatsapp_parent_notification']) : 1;

        $recipients = array();

        // Guardian / Parent
        if ($notify_parent == 1 && !empty($student['parent_id'])) {
            $parent = $this->db->select('name,mobileno')->where('id', $student['parent_id'])->get('parent')->row_array();
            if (!empty($parent['mobileno'])) {
                $formatted = $this->format_number($parent['mobileno']);
                if ($formatted) {
                    $recipients[] = $formatted;
                }
            }
        }

        // Student
        if ($notify_student == 1 && !empty($student['mobileno'])) {
            $formatted = $this->format_number($student['mobileno']);
            if ($formatted) {
                $recipients[] = $formatted;
            }
        }

        $recipients = array_unique($recipients);
        if (empty($recipients)) {
            log_message('info', "Whatsapp_model: No valid mobile numbers found for student ID: " . $studentID);
            return array('status' => false, 'message' => 'No valid recipient phone numbers');
        }

        // 4. Prepare parameters
        $studentFullName = trim($student['first_name'] . ' ' . $student['last_name']);
        $formattedAmount = number_format(floatval($totalAmount), 2, '.', '');
        $feeDescription = !empty($feeTypes) ? trim($feeTypes) : 'Fees';

        // 4a. Real Payment Date Calculation
        $branchTz = !empty($branch['timezone']) ? $branch['timezone'] : 'Asia/Kolkata';
        try {
            $dt = new DateTime('now', new DateTimeZone($branchTz));
            $defaultDate = $dt->format('d-m-Y');
        } catch (Exception $e) {
            $defaultDate = date('d-m-Y');
        }

        if (!empty($paymentDate)) {
            $cleanDate = str_replace(array('/', '.'), '-', trim($paymentDate));
            $ts = strtotime($cleanDate);
            $dateFormatted = ($ts !== false && $ts > 0) ? date('d-m-Y', $ts) : $defaultDate;
        } else {
            $dateFormatted = $defaultDate;
        }

        // 4b. Real Receipt / Invoice Number Calculation
        $enrollID = !empty($student['enrollid']) ? $student['enrollid'] : (!empty($student['enroll_id']) ? $student['enroll_id'] : $studentID);
        if (empty($receiptNo) || strpos($receiptNo, 'REC-') === 0) {
            $this->load->model('fees_model');
            $invoice = $this->fees_model->getInvoiceStatus($enrollID);
            if (!empty($invoice['invoice_no'])) {
                $receipt = '#' . $invoice['invoice_no'];
            } else {
                $receipt = !empty($receiptNo) ? $receiptNo : ('#' . $enrollID);
            }
        } else {
            $receipt = $receiptNo;
        }

        $parameters = array(
            $studentFullName,  // {{1}}
            $formattedAmount,  // {{2}}
            $feeDescription,   // {{3}}
            $dateFormatted,    // {{4}}
            $receipt           // {{5}}
        );

        // 5. Send message to all recipients via direct reliable WABA cURL
        $results = array();
        foreach ($recipients as $recipient) {
            $res = $this->send_direct($apiUrl, $apiKey, $recipient, $templateName, $parameters, $branchID);
            $results[$recipient] = $res;
        }

        return array('status' => true, 'results' => $results);
    }

    /**
     * Send generic template message
     */
    public function send_template($to, $template_name, $parameters, $branchID = null)
    {
        if (empty($branchID)) {
            $branchID = $this->application_model->get_branch_id();
        }
        $branch = $this->db->get_where('branch', array('id' => $branchID))->row_array();
        $apiKey = !empty($branch['whatsapp_api_key']) ? trim($branch['whatsapp_api_key']) : 'kkwaba_live_6Zl4DD-P-JylxkORDszcDvDg_jreH6RS0OdPGdWKx_0';
        $apiUrl = !empty($branch['whatsapp_api_url']) ? trim($branch['whatsapp_api_url']) : 'https://waba.kkwebmart.in/api/v1';

        $formatted = $this->format_number($to);
        if (!$formatted) {
            return array('status' => false, 'message' => 'Invalid phone number');
        }

        return $this->send_direct($apiUrl, $apiKey, $formatted, $template_name, $parameters, $branchID);
    }

    /**
     * Flush JSON response immediately to browser and detach HTTP connection
     * Allows UI / Receipt modal to load in < 5ms while background processing continues
     */
    public function finish_request_fast($json_data)
    {
        ignore_user_abort(true);
        set_time_limit(120);

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        $json = is_string($json_data) ? $json_data : json_encode($json_data);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');
        header('Connection: close');
        header('Content-Length: ' . strlen($json));
        header('X-Accel-Buffering: no');

        echo $json;

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } else {
            ob_flush();
            flush();
        }
    }

    /**
     * Direct robust WABA API send with database queue logging
     */
    public function send_direct($apiUrl, $apiKey, $recipient, $templateName, $parameters, $branchID = null)
    {
        // 1. Insert into queue with status 0
        $data = array(
            'branch_id' => $branchID,
            'recipient' => $recipient,
            'template_name' => $templateName,
            'parameters' => json_encode($parameters),
            'api_url' => $apiUrl,
            'api_key' => $apiKey,
            'status' => 0,
            'created_at' => date('Y-m-d H:i:s')
        );
        $this->db->insert('whatsapp_queue', $data);
        $queueId = $this->db->insert_id();

        // 2. Prepare endpoint & payload
        $endpoint = rtrim($apiUrl, '/');
        if (substr($endpoint, -9) !== '/messages') {
            $endpoint .= '/messages';
        }
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
        $status = $success ? 2 : 3;

        $updateData = array(
            'status' => $status,
            'response' => $response ?: $curlErr,
            'updated_at' => date('Y-m-d H:i:s')
        );
        $this->db->where('id', $queueId)->update('whatsapp_queue', $updateData);

        return array('status' => $success, 'http_code' => $httpCode, 'response' => $response, 'queue_id' => $queueId);
    }

    /**
     * Format phone number to international format (India prefix 91 if 10-digits)
     */
    public function format_number($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', (string) $phone);
        if (empty($phone)) {
            return false;
        }

        // If 10 digits (Standard Indian Mobile), prepend 91
        if (strlen($phone) == 10) {
            return '91' . $phone;
        }
        // If 11 digits starting with 0, replace 0 with 91
        if (strlen($phone) == 11 && substr($phone, 0, 1) === '0') {
            return '91' . substr($phone, 1);
        }
        // If 12 digits starting with 91, return as is
        if (strlen($phone) == 12 && substr($phone, 0, 2) === '91') {
            return $phone;
        }

        // Return number if it has valid international length (>= 10)
        if (strlen($phone) >= 10 && strlen($phone) <= 15) {
            return $phone;
        }

        return false;
    }
}
