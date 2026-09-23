1/ Without this file just single line change fees Pay/ invoice mai branch schools kaa student no data available show ho raha tha-

     -application/models/fees_modeles.php


2/ loading animation change
   assets\css\custom-style.css
   assets\frontend\css\style.css
   assets\frontend\css\saas_main.css

3/ Fees all select button group wise
  application\views\fees\collect.php

4/ AI Assistant (Google Gemini Integration)
   application\controllers\Ai_assistant.php
   application\views\layout\ai_assistant.php
   application\views\layout\topbar.php
   application\views\layout\index.php



5/ discount/and total ammount for selected fee

   application\views\fees\selectedFeesCollect.php
   application\models\Fees_model.php
   application\views\fees\collect.php
   application\views\fees\invoicePrint.php


6/ school frontend default
   
   application\views\home\schools\default\index.php


7/ Carry Fees Tab & Individual Student Allocation System
   - application/models/Fees_model.php: Added getStudentPreviousSessionDues(), getStudentCarryFeesAllocation(), saveStudentCarryFees(), and deleteStudentCarryFees() methods to calculate previous session dues and allocate Carry Fees isolated to a single student.
   - application/controllers/Fees.php: Updated invoice() method to fetch past session dues & carry allocation, and added carry_fees_save() controller method to process the Carry Fees save action.
   - application/views/fees/collect.php: Added "Carry Fees" tab beside Invoice, Collect Fees, Fully Paid, displaying previous session dues summary, auto-fill shortcut button, and AJAX submission form.


8/ Inventory POS Sales UI, Instant Customer, Units Non-Mandatory & Direct Bill Print
   - Files Modified / Created:
     - application/views/inventory/sales.php: Modern POS Terminal UI with product search, category pills, live cart, quick cash chips, instant customer text box, quick customer modal (name + phone), and direct print trigger.
     - application/views/inventory/sales_invoice_print.php [NEW]: Clean receipt/invoice print template for browser print dialog.
     - application/views/inventory/sales_invoice.php: Updated preview and print sections to display customer name and phone.
     - application/views/inventory/sales_report.php: Updated customer column to display customer_name.
     - application/views/inventory/product.php: Removed mandatory (*) from Product Code, Purchase Unit, Sales Unit, and Unit Ratio.
     - application/views/inventory/product_edit.php: Removed mandatory (*) from Product Code, Purchase Unit, Sales Unit, and Unit Ratio.
     - application/controllers/Inventory.php: Updated product_validation() (non-mandatory units/code), sales_validation() (support instant customer), sales_save() (returns invoice_url & bill_id), getPosProducts() (AJAX products), getSaleslistDT() (customer name & direct print button), and added invoicePrint() method for direct printing without page redirection.
     - application/models/Inventory_model.php: Updated save_product() with safe defaults, getProductByBranch() with joins, and save_sales() to save customer_name and customer_phone.
     - application/models/Application_model.php: Added null/zero safety in getUserNameByRoleID() for role_id 0 (Walk-in Customer).

   - SQL Queries (Database Updates):
     ```sql
     -- 1. Sales Bill table: Add customer name & phone columns for instant customer billing
     ALTER TABLE `sales_bill` ADD COLUMN `customer_name` VARCHAR(255) NULL DEFAULT '' AFTER `user_id`;
     ALTER TABLE `sales_bill` ADD COLUMN `customer_phone` VARCHAR(50) NULL DEFAULT '' AFTER `customer_name`;

     -- 2. Product table: Make Code, Purchase Unit, Sales Unit, Ratio, and Remarks optional (nullable/default)
     ALTER TABLE `product` MODIFY COLUMN `code` VARCHAR(255) NULL DEFAULT '';
     ALTER TABLE `product` MODIFY COLUMN `purchase_unit_id` INT(11) NULL DEFAULT 0;
     ALTER TABLE `product` MODIFY COLUMN `sales_unit_id` INT(11) NULL DEFAULT 0;
     ALTER TABLE `product` MODIFY COLUMN `unit_ratio` DOUBLE NULL DEFAULT 1;
     ```


9/ Automated WhatsApp Fee Payment Notification & Per-School API Integration with Fast HTTP Response
   - Files Created:
     - application/models/Whatsapp_model.php [NEW]: WhatsApp Business API (WABA) model. Handles template parameter mapping (payment_rechived), 10-digit Indian phone number formatting (+91), real date (Asia/Kolkata), invoice/receipt number matching, fast HTTP response detachment (finish_request_fast), direct cURL delivery, and database queue logging.
     - application/models/whatsapp_worker.php [NEW]: Standalone background worker script.

   - Files Modified:
     - application/controllers/Fees.php:
       - fee_add(): Real invoice number #1298, instant HTTP response flush via finish_request_fast(), background WhatsApp notification.
       - selectedFeesPay(): Multi-month payments consolidated into a single total bill WhatsApp message, instant HTTP response flush, background WhatsApp notification.
       - fee_fully_paid(): Real invoice number, instant HTTP response flush, background WhatsApp notification.
     - application/controllers/Feespayment.php: Online payment success callback updated to trigger WhatsApp fee receipt notification.
     - application/controllers/School_settings.php: Added validation & save handler (saveWhatsappApiConfig) for branch WhatsApp API settings.
     - application/models/School_model.php: Updated branchUpdate() to save branch WhatsApp API credentials and notification switches.
     - application/views/school_settings/school.php: Added WhatsApp API Settings card to School Settings main view.
     - application/views/school_settings/whatsapp_settings.php: Added school WhatsApp API configuration card to WhatsApp Settings view.

   - SQL Queries (Database Updates):
     ```sql
     -- 1. Add WhatsApp API configuration columns to branch table (School Settings)
     ALTER TABLE `branch` 
       ADD COLUMN `whatsapp_api_url` VARCHAR(255) NULL DEFAULT 'https://waba.kkwebmart.in/api/v1' AFTER `symbol_position`,
       ADD COLUMN `whatsapp_api_key` VARCHAR(255) NULL DEFAULT 'kkwaba_live_6Zl4DD-P-JylxkORDszcDvDg_jreH6RS0OdPGdWKx_0' AFTER `whatsapp_api_url`,
       ADD COLUMN `whatsapp_template_name` VARCHAR(100) NULL DEFAULT 'payment_rechived' AFTER `whatsapp_api_key`,
       ADD COLUMN `whatsapp_status` TINYINT(1) DEFAULT 1 COMMENT '1=Enabled, 0=Disabled' AFTER `whatsapp_template_name`,
       ADD COLUMN `whatsapp_student_notification` TINYINT(1) DEFAULT 1 COMMENT '1=Send to Student, 0=No' AFTER `whatsapp_status`,
       ADD COLUMN `whatsapp_parent_notification` TINYINT(1) DEFAULT 1 COMMENT '1=Send to Parent, 0=No' AFTER `whatsapp_student_notification`;

     -- 2. Create WhatsApp Queue & Log table
     CREATE TABLE IF NOT EXISTS `whatsapp_queue` (
       `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
       `branch_id` INT(11) NULL,
       `recipient` VARCHAR(30) NOT NULL,
       `template_name` VARCHAR(100) NOT NULL,
       `parameters` TEXT NOT NULL,
       `api_url` VARCHAR(255) NOT NULL,
       `api_key` VARCHAR(255) NOT NULL,
       `status` TINYINT(1) DEFAULT 0 COMMENT '0=pending, 1=processing, 2=sent, 3=failed',
       `response` TEXT NULL,
       `created_at` DATETIME NOT NULL,
       `updated_at` DATETIME NULL
     ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

     -- 3. Set Timezone to Indian Standard Time (Asia/Kolkata) so payment dates reflect real current date
     UPDATE `global_settings` SET `timezone` = 'Asia/Kolkata';
     UPDATE `branch` SET `timezone` = 'Asia/Kolkata';
     ```


10/ Exam Schedule Timetable Chronological Sorting (Exam Date & Time)
   - Reason: Admit card and schedule modal me exam subjects random order me display ho rahe the. Unhe exam date aur start time ke hisaab se ascending chronological order me sort kiya gaya.
   - Files Modified:
     - application/models/Timetable_model.php: getExamTimetableByModal() method me SQL order by `t.exam_date ASC` aur `STR_TO_DATE(t.time_start, '%h:%i %p') ASC` add kiya.
     - application/models/Card_manage_model.php: tableHtml() method me PHP `usort()` add kiya jo exam timetable records ko `exam_date` aur `time_start` ke mutabiq sort karta hai.


11/ Admit Card Generation - A4 Landscape (2 Cards Side-by-Side), Inner Border Fit & Auto-Scale
   - Reason: Admit card generate karne ke baad single A4 portrait page par 1 card aa raha tha aur bada content hone par cut ya overflow ho raha tha. Ab A4 landscape paper par 2 admit cards side-by-side (left-right) aayenge, decorative border ke andar strictly aligned rahenge, natural proper height maintain karenge, aur agar subjects zyada ho toh auto-adjust ho kar next page par jaane se rokenge.
   - Files Modified:
     - application/views/card_manage/admitCardprintFn.php:
       - Students array ko pairs me divide kiya (`array_chunk($user_array, 2)`).
       - Page layout `@page { size: A4 landscape; margin: 3mm 4mm; }` configure kiya.
       - Center me dashed vertical cutting line (✂) add ki taaki A4 paper ko beech se cut kiya ja sake.
       - Decorative background image ke inner frame ke hisaab se padding (`padding: 42px 34px 34px 34px !important`) set ki taaki text, tables, QR code, aur signature border lines ko touch ya cross na karein.
       - Summernote hardcoded table width (`width: 1100px;`) ko override karke 100% responsive banaya.
       - JavaScript `autoFitAdmitCards()` add kiya jo natural height retain karta hai aur sirf tabhi scale down karta hai jab content actually page height se zyada ho, taaki content kabhi second page par overflow na ho.
     - application/views/userrole/admitCardprintFn.php: Student/Parent panel admit card print ko same A4 landscape single-slot container, inner border padding aur auto-fit layout ke saath update kiya.
     - application/views/home/admitCardprintFn.php: Public website admit card print view ko same A4 landscape layout aur border alignment ke saath update kiya.
     - assets/js/certificate.js: `certificate_printElem()` print function ko update kiya taaki images load hone aur auto-scale calculate hone ke baad `window.print()` trigger ho.


12/ Fees Payment Receipt - A4 Landscape (2 Side-by-Side Copies, Blank Slot & Auto-Fit Height Scaling)
   - Reason: Fees payment receipt print karte waqt agar 2 copies (Student Copy & Office Copy) nikale toh landscape mode me side-by-side aati thi, lekin single copy nikalne par pura page (100% width) cover ho jata tha jisse receipt distort aur oversized lagti thi. Ab layout strictly A4 landscape 2-column grid par lock hai. Agar single copy (Student Copy Only ya Office Copy Only) nikali jaye toh wo sirf apne left half slot (50% width) me aayegi aur dusra half (50%) bilkul blank/empty rahega bina stretch huye. Saath hi Grand Total aur summary amounts ko direct fees table ke neeche naturally attach kiya gaya hai taaki 1 ya 2 items hone par koi awkward vertical gap na dikhe, jabki Signatures page ke bottom par anchored rahenge. Agar receipt me zyada items hone se receipt lambi ho jaye, toh `autoFitReceipt()` automatically scale down karke single page par hi fit rakhega.
   - Files Modified:
     - application/views/fees/paySlipPrint.php:
       - `@page { size: A4 landscape; margin: 4mm 6mm; }` configure kiya aur `page-break-inside: avoid !important;` add kiya.
       - Container `.receipt-page-container` aur `.receipt-slot` ko strict `width: 50% !important; max-width: 50% !important; flex: 0 0 50% !important; float: left !important;` set kiya.
       - Grand Total / Summary section ko fees items table ke theek neeche (`.invoice-body`) attach kiya taaki 1 ya 2 fees types hone par koi gap na aaye.
       - Signatures block ko `.receipt-bottom-bar` me `margin-top: auto` ke saath card ke bottom par clean rakha.
       - Single copy print hone par dusra slot `.empty-slot` (50% blank space) ke taur par render hota hai taaki receipt kabhi full page expand na ho.
       - Dono copies hone par beech me clean dashed divider line (✂) set ki.
       - Top par sleek interactive toggle bar (`.hidden-print`) add kiya jisse user print popup ke andar hi 1-click me Both Copies, Student Copy Only, aur Office Copy Only switch karke Print Now daba sakta hai.
       - `autoFitReceipt()` intelligent JS auto-scale function implement kiya jo content height calculate karke A4 landscape printable area (~725px) ke mutabiq dynamic scale karta hai (normal rehne par 100% natural, zyada lamba hone par auto-fit to single page).
     - application/controllers/Fees.php:
       - `payReceiptPrint()` method me `$copy_type` (both, student, office) parameter receive karke view me pass kiya.
     - application/views/fees/collect.php:
       - Invoice Summary ke upar aur neeche wale "Selected Pay Receipt" buttons ko split dropdown buttons banaya jisme "Both Copies", "Student Copy Only", aur "Office Copy Only" ke instant options hain.
       - Payment History tab ke "Selected Pay Receipt" button ko bhi same dropdown options ke saath update kiya.
       - Fee Payment modal footer me receipt copy selection radio buttons (Both Copies, Student Only, Office Only) add kiye.
       - JavaScript print execution function ko `copy_type` parameter support ke saath refactor kiya.
     - assets/js/app.fn.js:
       - Form submit AJAX handler me fee payment save & print ke waqt selected `modal_receipt_copy` value ko read karke `fees/payReceiptPrint` ko pass kiya.

13/ Fix Hostinger Server "Page Not Working" (HTTP 500) on /fees/invoice_list
   - Problem: Localhost (XAMPP) par fees/invoice_list perfectly chal raha tha, lekin Hostinger server par "This page isn't working" (HTTP 500) error aa raha tha.
   - Root Causes:
     1. Hostinger MySQL 8.0 me ONLY_FULL_GROUP_BY sql_mode default enabled hota hai. Fees_model::getInvoiceList() query me GROUP BY fa.student_id tha jabki SELECT list me non-aggregated columns (e.id, s.first_name, s.last_name, etc.) aur ORDER BY me fa.id tha. MySQL 8.0 isko reject karke fatal mysqli_sql_exception throw kar raha tha.
     2. CodeIgniter 3 ke mysqli driver me stricton => false hone par bhi ONLY_FULL_GROUP_BY ko strip nahi kiya jata tha, jisse shared hosting par jahan my.cnf edit nahi ho sakta, sessions me ONLY_FULL_GROUP_BY active reh jata tha.
     3. Line 253 me $this->db->where('e.section_id', $section_id) likha tha datatables query object ke badle.
     4. PHP 8.1+ me query fail hone par $records null hota tha aur $records->data read karne par fatal error crash hota tha.
   - Fixes Applied:
     - system/database/drivers/mysqli/mysqli_driver.php: MYSQLI_INIT_COMMAND me ONLY_FULL_GROUP_BY ko automatically replace/strip kiya jab stricton false ho.
     - application/core/MY_Controller.php: Har web request ke start me SET SESSION sql_mode query chala kar session se ONLY_FULL_GROUP_BY aur strict modes strip kiye.
     - application/models/Fees_model.php:
       - getInvoiceList() query me GROUP BY clause me all non-aggregated columns add kiye aur ORDER BY ko e.id ASC set kiya.
       - Line 253 me $this->datatables->where('e.section_id', $section_id) fix kiya.
       - Robust PHP 8 null-safety lagayi (!empty($records) && isset($records->data) && is_array($records->data)) taaki kabhi 500 error na aaye.

14/ Fix Server-Side Invoice Fees Payment Page Not Opening (/fees/invoice & /userrole/invoice)
   - Problem: Server side par individual invoice fees payment page open nahi ho raha tha (blank page, silent redirect to dashboard, ya HTTP 500).
   - Root Causes:
     1. `Fees_model::getInvoiceBasic()` me `s.id as student_id` missing tha, jiski wajah se PHP 8.1+ par `$basic['student_id']` undefined key notice throw karke previous session dues calculation me `null` pass kar raha tha.
     2. `Fees_model::getInvoiceBasic()` me hardcoded `$this->db->where('e.session_id', $sessionID)` tha. Agar student ka session ya user session mismatch hota tha toh `$basic` empty ho kar `Fees::invoice()` direct dashboard par redirect kar deta tha.
     3. `Fees_model::getBalance()` me `$totalAmount = $totalAmount['amount']` bina `isset()` check ke call tha, jo missing fee type group detail par PHP 8 TypeError / Fatal Error deta tha.
     4. `Fees::invoice()` aur `Userrole::invoice()` me error handling (try-catch) missing tha.
   - Fixes Applied:
     - application/models/Fees_model.php:
       - `getInvoiceBasic()`: Added `s.id as student_id`, made session/branch filters resilient against strict locks.
       - `getBalance()`: Added safe `isset($getAmt['amount']) ? $getAmt['amount'] : 0` and float conversion.
       - `getStudentPreviousSessionDues()`: Added null/empty safety for `$student_id`.
     - application/controllers/Fees.php:
       - `invoice()`: Wrapped inside `try { ... } catch (Throwable $e) { ... }`, safe fallback for `$student_id`, and graceful redirect with alert message instead of silent dashboard drop.
     - application/controllers/Userrole.php:
       - `invoice()`: Wrapped inside try-catch with graceful fallback to student details.
