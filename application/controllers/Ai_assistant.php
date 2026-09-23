<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ai_assistant extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('application_model');
        $this->check_gemini_key_column();
    }

    private function check_gemini_key_column()
    {
        if (!$this->db->field_exists('gemini_api_key', 'global_settings')) {
            $this->db->query("ALTER TABLE `global_settings` ADD `gemini_api_key` TEXT NULL");
        }
    }

    public function save_key()
    {
        if ($_POST) {
            $key = trim($this->input->post('gemini_api_key'));
            $this->db->where('id', 1);
            $this->db->update('global_settings', array('gemini_api_key' => $key));
            echo json_encode(array('status' => 'success', 'message' => 'API Key saved successfully!'));
            exit;
        }
    }

    public function get_key()
    {
        $key = get_global_setting('gemini_api_key');
        echo json_encode(array('key' => $key ? $key : ''));
        exit;
    }

    public function process()
    {
        header('Content-Type: application/json');
        
        if (!is_loggedin()) {
            echo json_encode(array('status' => 'error', 'message' => 'Unauthorized access.'));
            exit;
        }

        $prompt = trim($this->input->post('prompt'));
        if (empty($prompt)) {
            echo json_encode(array('status' => 'error', 'message' => 'Please enter a message.'));
            exit;
        }

        $apiKey = get_global_setting('gemini_api_key');
        if (empty($apiKey)) {
            $apiKey = trim($this->input->post('api_key'));
        }

        if (empty($apiKey)) {
            echo json_encode(array('status' => 'need_key', 'message' => 'Gemini API Key is required. Please set your Gemini API Key in the settings.'));
            exit;
        }

        // Gather DB Schema context for accurate data entry & query
        $dbContext = $this->get_db_context();

        $systemInstruction = "You are an intelligent AI Database Assistant for Ramom School Management System.
Your job is to assist users in performing data entry, managing records, and querying school data.
When the user asks to perform a data entry task (like adding a student, expense, fee type, fee group, staff/employee, etc.) or query database records, respond with a JSON object in a ```json codeblock containing the intended action and parameters, followed by a human-friendly response.

Database Context & Reference IDs:
" . json_encode($dbContext, JSON_PRETTY_PRINT) . "

Supported JSON actions format inside ```json ... ```:
1. Add Student:
{\"action\":\"add_student\", \"params\":{\"first_name\":\"John\", \"last_name\":\"Doe\", \"gender\":\"male\", \"class_id\":1, \"section_id\":1, \"branch_id\":1, \"register_no\":\"1001\", \"roll\":\"1\", \"email\":\"john@example.com\", \"mobileno\":\"9876543210\", \"address\":\"Street 123\", \"blood_group\":\"A+\"}}

2. Add Expense:
{\"action\":\"add_expense\", \"params\":{\"branch_id\":1, \"category_id\":1, \"voucher_head\":\"Electricity Bill\", \"amount\":500, \"date\":\"" . date('Y-m-d') . "\", \"pay_via\":\"cash\", \"description\":\"Monthly power bill\"}}

3. Add Fee Type:
{\"action\":\"add_fee_type\", \"params\":{\"branch_id\":1, \"name\":\"Sports Fee\", \"fee_code\":\"SF01\", \"description\":\"Annual sports charges\"}}

4. Add Fee Group:
{\"action\":\"add_fee_group\", \"params\":{\"branch_id\":1, \"name\":\"Quarterly Fee\", \"description\":\"Quarterly fee group\"}}

5. Add Staff / Employee:
{\"action\":\"add_staff\", \"params\":{\"branch_id\":1, \"role_id\":3, \"name\":\"Robert Smith\", \"sex\":\"male\", \"email\":\"robert@school.com\", \"mobileno\":\"9876543210\", \"designation\":\"Teacher\", \"department\":\"Science\"}}

6. Mark / Update Student Fees as Paid (Bulk & Partial Payments Supported):
{\"action\":\"mark_fee_paid\", \"params\":{
  \"class_name\":\"ANKUR\",
  \"student_payments\":[
    {\"student_name\":\"CHANDITA SAIKIA\", \"months\":[\"April\", \"May\", \"June\", \"July\", \"August\"]},
    {\"student_name\":\"HIMANGSHREE BORA\", \"month_amounts\":{\"April\": 250, \"May\": 500}}
  ]
}}

7. Custom Data Entry / SQL Action:
{\"action\":\"custom_insert\", \"table\":\"table_name\", \"data\":{\"col1\":\"val1\", \"col2\":\"val2\"}}

8. Direct Answer / Query Response:
If no DB modification is needed, just provide a clear, helpful response.

Always match class_id, section_id, branch_id, category_id, role_id from the Database Context provided above when doing data entry.";

        $payload = array(
            'contents' => array(
                array(
                    'parts' => array(
                        array('text' => $systemInstruction . "\n\nUser Request: " . $prompt)
                    )
                )
            ),
            'generationConfig' => array(
                'temperature' => 0.2
            )
        );

        $apiResult = $this->call_gemini_api($apiKey, $payload);
        if (!$apiResult['success']) {
            echo json_encode(array('status' => 'error', 'message' => $apiResult['error']));
            exit;
        }

        $resData = $apiResult['response'];
        $aiText = isset($resData['candidates'][0]['content']['parts'][0]['text']) ? $resData['candidates'][0]['content']['parts'][0]['text'] : 'No response generated.';

        // Parse JSON action if present in Gemini output
        $actionResult = $this->execute_ai_action($aiText);

        echo json_encode(array(
            'status' => 'success',
            'response' => $aiText,
            'action_result' => $actionResult
        ));
        exit;
    }

    private function get_db_context()
    {
        $branchID = get_loggedin_branch_id();
        $branches = $this->db->select('id, name')->get('branch')->result_array();
        
        $this->db->select('id, name, branch_id');
        if (!empty($branchID)) {
            $this->db->where('branch_id', $branchID);
        }
        $classes = $this->db->get('class')->result_array();

        $sections = $this->db->select('id, name')->get('section')->result_array();

        $this->db->select('id, name, branch_id');
        if (!empty($branchID)) {
            $this->db->where('branch_id', $branchID);
        }
        $expenseCategories = $this->db->get('voucher_head')->result_array();

        $roles = $this->db->select('id, name')->get('roles')->result_array();

        $this->db->select('id, name, branch_id');
        if (!empty($branchID)) {
            $this->db->where('branch_id', $branchID);
        }
        $feeGroups = $this->db->get('fee_groups')->result_array();

        $this->db->select('id, name, fee_code, branch_id');
        if (!empty($branchID)) {
            $this->db->where('branch_id', $branchID);
        }
        $feeTypes = $this->db->get('fees_type')->result_array();

        return array(
            'logged_in_branch_id' => $branchID,
            'current_session_id' => get_session_id(),
            'branches' => $branches,
            'classes' => $classes,
            'sections' => $sections,
            'expense_categories' => $expenseCategories,
            'roles' => $roles,
            'fee_groups' => $feeGroups,
            'fee_types' => $feeTypes
        );
    }

    private function execute_ai_action($aiText)
    {
        $jsonBlocks = array();

        if (preg_match_all('/```(?:json)?\s*([\s\S]*?)\s*```/i', $aiText, $matches)) {
            foreach ($matches[1] as $blk) {
                $jsonBlocks[] = trim($blk);
            }
        } elseif (preg_match('/\{[\s\S]*\}/', $aiText, $matches)) {
            $jsonBlocks[] = trim($matches[0]);
        }

        $results = array();
        foreach ($jsonBlocks as $jsonStr) {
            $actionData = json_decode($jsonStr, true);
            
            $actionsList = array();
            if (isset($actionData[0]) && is_array($actionData[0])) {
                $actionsList = $actionData;
            } elseif (!empty($actionData) && isset($actionData['action'])) {
                $actionsList[] = $actionData;
            }

            foreach ($actionsList as $actItem) {
                if (isset($actItem['action'])) {
                    $action = $actItem['action'];
                    $params = isset($actItem['params']) ? $actItem['params'] : (isset($actItem['data']) ? $actItem['data'] : array());

                    $res = null;
                    switch ($action) {
                        case 'add_student':
                            $res = $this->ai_add_student($params);
                            break;

                        case 'add_expense':
                            $res = $this->ai_add_expense($params);
                            break;

                        case 'add_fee_type':
                            $res = $this->ai_add_fee_type($params);
                            break;

                        case 'add_fee_group':
                            $res = $this->ai_add_fee_group($params);
                            break;

                        case 'add_staff':
                            $res = $this->ai_add_staff($params);
                            break;

                        case 'mark_fee_paid':
                            $res = $this->ai_mark_fee_paid($params);
                            break;

                        case 'custom_insert':
                            if (isset($actItem['table']) && !empty($params)) {
                                $table = preg_replace('/[^a-zA-Z0-9_]/', '', $actItem['table']);
                                if ($this->db->table_exists($table)) {
                                    $this->db->insert($table, $params);
                                    $res = array('success' => true, 'message' => "Inserted record into table `{$table}` (ID: " . $this->db->insert_id() . ")");
                                }
                            }
                            break;
                    }
                    if ($res) {
                        $results[] = $res;
                    }
                }
            }
        }

        if (empty($results)) {
            return null;
        }

        $messages = array();
        $hasSuccess = false;
        foreach ($results as $r) {
            if (!empty($r['message'])) {
                $messages[] = $r['message'];
            }
            if (!empty($r['success'])) {
                $hasSuccess = true;
            }
        }

        return array(
            'success' => $hasSuccess,
            'message' => implode(' | ', $messages)
        );
    }

    private function ai_add_student($p)
    {
        $branchID = isset($p['branch_id']) ? $p['branch_id'] : get_loggedin_branch_id();
        $classID = isset($p['class_id']) ? $p['class_id'] : 1;
        $sectionID = isset($p['section_id']) ? $p['section_id'] : 1;
        $sessionID = get_session_id();

        $studentData = array(
            'register_no' => isset($p['register_no']) ? $p['register_no'] : ('REG' . rand(1000, 9999)),
            'first_name' => isset($p['first_name']) ? $p['first_name'] : 'New',
            'last_name' => isset($p['last_name']) ? $p['last_name'] : 'Student',
            'gender' => isset($p['gender']) ? strtolower($p['gender']) : 'male',
            'blood_group' => isset($p['blood_group']) ? $p['blood_group'] : 'A+',
            'birthday' => isset($p['birthday']) ? $p['birthday'] : date('Y-m-d'),
            'email' => isset($p['email']) ? $p['email'] : ('student' . rand(100, 999) . '@school.com'),
            'mobileno' => isset($p['mobileno']) ? $p['mobileno'] : '0000000000',
            'address' => isset($p['address']) ? $p['address'] : '',
            'branch_id' => $branchID,
            'active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        );

        $this->db->insert('student', $studentData);
        $studentID = $this->db->insert_id();

        // Enroll Student
        $enrollData = array(
            'student_id' => $studentID,
            'class_id' => $classID,
            'section_id' => $sectionID,
            'session_id' => $sessionID,
            'branch_id' => $branchID,
            'roll' => isset($p['roll']) ? $p['roll'] : 1
        );
        $this->db->insert('enroll', $enrollData);

        return array(
            'success' => true,
            'message' => "Student successfully registered and enrolled! (Student ID: {$studentID}, Reg No: {$studentData['register_no']})"
        );
    }

    private function ai_add_expense($p)
    {
        $branchID = isset($p['branch_id']) ? $p['branch_id'] : get_loggedin_branch_id();
        $expenseData = array(
            'account_id' => 1,
            'voucher_head_id' => isset($p['category_id']) ? $p['category_id'] : 1,
            'voucher_no' => 'EXP' . rand(1000, 9999),
            'amount' => isset($p['amount']) ? $p['amount'] : 0,
            'date' => isset($p['date']) ? $p['date'] : date('Y-m-d'),
            'type' => 'expense',
            'pay_via' => isset($p['pay_via']) ? $p['pay_via'] : 'cash',
            'description' => isset($p['description']) ? $p['description'] : (isset($p['voucher_head']) ? $p['voucher_head'] : 'AI Expense Entry'),
            'branch_id' => $branchID,
            'prepared_by' => get_loggedin_user_id()
        );

        $this->db->insert('voucher', $expenseData);
        $expenseID = $this->db->insert_id();

        return array(
            'success' => true,
            'message' => "Expense record added successfully! (Voucher ID: {$expenseID}, Amount: {$expenseData['amount']})"
        );
    }

    private function ai_add_fee_type($p)
    {
        $branchID = isset($p['branch_id']) ? $p['branch_id'] : get_loggedin_branch_id();
        $feeTypeData = array(
            'name' => isset($p['name']) ? $p['name'] : 'New Fee Type',
            'fee_code' => isset($p['fee_code']) ? $p['fee_code'] : ('FT' . rand(100, 999)),
            'description' => isset($p['description']) ? $p['description'] : '',
            'branch_id' => $branchID,
            'system' => 0
        );

        $this->db->insert('fees_type', $feeTypeData);
        $feeTypeID = $this->db->insert_id();

        return array(
            'success' => true,
            'message' => "Fee Type '{$feeTypeData['name']}' created successfully! (ID: {$feeTypeID})"
        );
    }

    private function ai_add_fee_group($p)
    {
        $branchID = isset($p['branch_id']) ? $p['branch_id'] : get_loggedin_branch_id();
        $groupData = array(
            'name' => isset($p['name']) ? $p['name'] : 'New Fee Group',
            'description' => isset($p['description']) ? $p['description'] : '',
            'branch_id' => $branchID,
            'system' => 0
        );

        $this->db->insert('fee_groups', $groupData);
        $groupID = $this->db->insert_id();

        return array(
            'success' => true,
            'message' => "Fee Group '{$groupData['name']}' created successfully! (ID: {$groupID})"
        );
    }

    private function ai_add_staff($p)
    {
        $branchID = isset($p['branch_id']) ? $p['branch_id'] : get_loggedin_branch_id();
        $roleID = isset($p['role_id']) ? $p['role_id'] : 3;

        $staffData = array(
            'staff_id' => 'EMP' . rand(1000, 9999),
            'name' => isset($p['name']) ? $p['name'] : 'New Staff',
            'sex' => isset($p['sex']) ? strtolower($p['sex']) : 'male',
            'email' => isset($p['email']) ? $p['email'] : ('staff' . rand(100, 999) . '@school.com'),
            'mobileno' => isset($p['mobileno']) ? $p['mobileno'] : '0000000000',
            'designation' => 1,
            'department' => 1,
            'branch_id' => $branchID,
            'active' => 1,
            'joining_date' => date('Y-m-d')
        );

        $this->db->insert('staff', $staffData);
        $staffID = $this->db->insert_id();

        // Create login account
        $loginData = array(
            'user_id' => $staffID,
            'role' => $roleID,
            'username' => $staffData['email'],
            'password' => $this->app_lib->pass_hashed('123456'),
            'active' => 1
        );
        $this->db->insert('login_credential', $loginData);

        return array(
            'success' => true,
            'message' => "Staff member '{$staffData['name']}' added successfully! (Staff ID: {$staffData['staff_id']})"
        );
    }

    private function ai_mark_fee_paid($p)
    {
        $className = isset($p['class_name']) ? $p['class_name'] : '';
        $feeGroupName = isset($p['fee_group_name']) ? $p['fee_group_name'] : '';
        
        // Support bulk student payments array or single/multiple student names
        $studentPayments = isset($p['student_payments']) && is_array($p['student_payments']) ? $p['student_payments'] : array();
        
        if (empty($studentPayments)) {
            $studentNames = isset($p['student_names']) ? $p['student_names'] : (isset($p['student_name']) ? array($p['student_name']) : array());
            foreach ($studentNames as $name) {
                $studentPayments[] = array(
                    'student_name' => $name,
                    'months' => isset($p['months']) ? $p['months'] : array(),
                    'month_amounts' => isset($p['month_amounts']) ? $p['month_amounts'] : array(),
                    'amount' => isset($p['amount']) ? $p['amount'] : null
                );
            }
        }

        if (empty($studentPayments)) {
            return array('success' => false, 'message' => 'No student payment details provided.');
        }

        $paidCount = 0;
        $totalPaidSum = 0;
        $detailsMsg = array();

        foreach ($studentPayments as $stPay) {
            $stName = isset($stPay['student_name']) ? trim($stPay['student_name']) : '';
            if (empty($stName)) continue;

            $months = isset($stPay['months']) ? $stPay['months'] : (isset($p['months']) ? $p['months'] : array());
            $monthAmounts = isset($stPay['month_amounts']) ? $stPay['month_amounts'] : (isset($p['month_amounts']) ? $p['month_amounts'] : array());
            $singleAmount = isset($stPay['amount']) ? $stPay['amount'] : (isset($p['amount']) ? $p['amount'] : null);

            $cleanName = trim($stName);
            $parts = explode(' ', $cleanName);
            $firstName = $parts[0];
            $lastName = isset($parts[1]) ? $parts[count($parts) - 1] : '';

            $this->db->select('student.id as student_id, student.first_name, student.last_name, enroll.id as enroll_id');
            $this->db->from('student');
            $this->db->join('enroll', 'enroll.student_id = student.id', 'inner');
            if (!empty($className)) {
                $this->db->join('class', 'class.id = enroll.class_id', 'inner');
                $this->db->like('class.name', $className);
            }

            $this->db->group_start();
            $this->db->like("CONCAT(TRIM(student.first_name), ' ', TRIM(student.last_name))", $cleanName);
            $this->db->or_like("student.first_name", $firstName);
            if (!empty($lastName)) {
                $this->db->or_group_start();
                $this->db->like("student.first_name", $firstName);
                $this->db->like("student.last_name", $lastName);
                $this->db->group_end();
            }
            $this->db->group_end();

            $students = $this->db->get()->result_array();

            foreach ($students as $st) {
                $enrollID = $st['enroll_id'];
                
                // Fetch allocations
                $this->db->select('fa.id as allocation_id, fa.group_id');
                $this->db->from('fee_allocation as fa');
                if (!empty($feeGroupName) && empty($months) && empty($monthAmounts)) {
                    $this->db->join('fee_groups as fg', 'fg.id = fa.group_id', 'inner');
                    $this->db->like('fg.name', $feeGroupName);
                }
                $this->db->where('fa.student_id', $enrollID);
                $allocations = $this->db->get()->result_array();

                if (empty($allocations) && !empty($feeGroupName)) {
                    $this->db->select('fa.id as allocation_id, fa.group_id');
                    $this->db->from('fee_allocation as fa');
                    $this->db->where('fa.student_id', $enrollID);
                    $allocations = $this->db->get()->result_array();
                }

                foreach ($allocations as $alloc) {
                    $allocID = $alloc['allocation_id'];
                    $groupID = $alloc['group_id'];

                    $this->db->select('fgd.fee_type_id, fgd.amount as total_amount, ft.name as type_name');
                    $this->db->from('fee_groups_details as fgd');
                    $this->db->join('fees_type as ft', 'ft.id = fgd.fee_type_id', 'inner');
                    $this->db->where('fgd.fee_groups_id', $groupID);
                    $feeTypes = $this->db->get()->result_array();

                    foreach ($feeTypes as $ft) {
                        $typeName = $ft['type_name'];
                        $allocatedAmount = floatval($ft['total_amount']);

                        // Match Month or Fee Group
                        $matchedMonthKey = null;
                        $matchMonth = empty($months) && empty($monthAmounts);
                        
                        if (!$matchMonth) {
                            if (!empty($monthAmounts)) {
                                foreach ($monthAmounts as $mKey => $mAmt) {
                                    if (stripos($typeName, $mKey) !== false || stripos($mKey, $typeName) !== false) {
                                        $matchMonth = true;
                                        $matchedMonthKey = $mKey;
                                        break;
                                    }
                                }
                            }
                            if (!$matchMonth && !empty($months)) {
                                foreach ($months as $m) {
                                    if (stripos($typeName, $m) !== false || stripos($m, $typeName) !== false) {
                                        $matchMonth = true;
                                        $matchedMonthKey = $m;
                                        break;
                                    }
                                }
                            }
                        }

                        if ($matchMonth) {
                            // Calculate existing paid balance to avoid duplicate or partial overflow
                            $this->db->select("IFNULL(SUM(amount + discount), 0) as paid_sum");
                            $this->db->where(array('allocation_id' => $allocID, 'type_id' => $ft['fee_type_id']));
                            $paidRow = $this->db->get('fee_payment_history')->row_array();
                            $alreadyPaid = floatval($paidRow['paid_sum']);
                            $balance = $allocatedAmount - $alreadyPaid;

                            if ($balance > 0) {
                                // Determine payment amount (full or partial)
                                $payAmount = $balance;
                                if ($matchedMonthKey && isset($monthAmounts[$matchedMonthKey]) && is_numeric($monthAmounts[$matchedMonthKey])) {
                                    $payAmount = min(floatval($monthAmounts[$matchedMonthKey]), $balance);
                                } elseif ($singleAmount !== null && is_numeric($singleAmount)) {
                                    $payAmount = min(floatval($singleAmount), $balance);
                                }

                                if ($payAmount > 0) {
                                    $arrayFees = array(
                                        'allocation_id' => $allocID,
                                        'type_id' => $ft['fee_type_id'],
                                        'collect_by' => get_loggedin_user_id() ? get_loggedin_user_id() : 1,
                                        'amount' => $payAmount,
                                        'discount' => 0,
                                        'fine' => 0,
                                        'pay_via' => '1',
                                        'remarks' => isset($p['remarks']) ? $p['remarks'] : 'Paid via AI Assistant',
                                        'date' => isset($p['date']) ? $p['date'] : date('Y-m-d')
                                    );
                                    $this->db->insert('fee_payment_history', $arrayFees);
                                    $paidCount++;
                                    $totalPaidSum += $payAmount;
                                    $remBal = $balance - $payAmount;
                                    $detailsMsg[] = "{$st['first_name']} {$st['last_name']} - {$typeName} (Paid: {$payAmount}, Rem. Balance: {$remBal})";
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($paidCount == 0) {
            return array(
                'success' => false,
                'message' => "No unpaid fee records found matching criteria (or fees already fully paid)."
            );
        }

        return array(
            'success' => true,
            'message' => "Successfully processed {$paidCount} fee payment items (Total Paid: {$totalPaidSum})! " . implode('; ', $detailsMsg)
        );
    }

    private function call_gemini_api($apiKey, $payload)
    {
        $models = array('gemini-3.5-flash-lite', 'gemini-2.5-flash', 'gemini-2.0-flash', 'gemini-1.5-flash');
        $lastError = 'Failed to connect to Gemini API. Please check your API key.';

        foreach ($models as $model) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . urlencode($apiKey);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && !empty($response)) {
                $resData = json_decode($response, true);
                if (isset($resData['candidates'][0]['content']['parts'][0]['text'])) {
                    return array('success' => true, 'response' => $resData);
                }
            } else {
                $err = json_decode($response, true);
                if (isset($err['error']['message'])) {
                    if (strpos($err['error']['message'], 'not found') === false) {
                        $lastError = $err['error']['message'];
                    }
                }
            }
        }

        return array('success' => false, 'error' => $lastError);
    }
}
