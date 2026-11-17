<?php
$schoolName = trim((string)$student['school_name']);
$district = trim((string)$student['city']);
$state = trim((string)$student['state']);
$institutionCode = trim((string)$student['institution_code']);
$passingYear = preg_replace('/[^0-9].*$/', '', (string)$student['school_year']);
$topLogo = !empty($pdf) ? str_replace('\\', '/', FCPATH.'uploads/weighted_exam/education-for-all.jpg') : base_url('uploads/weighted_exam/education-for-all.jpg');
$assamLogo = !empty($pdf) ? str_replace('\\', '/', FCPATH.'uploads/weighted_exam/assam-education-logo.jpg') : base_url('uploads/weighted_exam/assam-education-logo.jpg');
?>
<style>
.front-page{font-family:dejavusans,Arial,sans-serif;color:#111;height:188mm;border:.3mm solid #111;padding:4mm;background:#fff}.front-layout{width:100%;height:100%;border-collapse:collapse;table-layout:fixed}.front-layout>tbody>tr>td{vertical-align:top}.front-left{width:45%;padding:11mm 4mm 2mm 2mm}.front-gap{width:5%}.front-right{width:50%}.activity-frame{border:.3mm solid #111;padding:2mm}.activity-table{width:100%;border-collapse:collapse;table-layout:fixed;font-size:8.2pt;line-height:9pt;text-align:center}.activity-table td,.activity-table th{border:.25mm solid #111;padding:.75mm .8mm;vertical-align:middle}.activity-table .main-title{font-size:11pt;line-height:12pt;font-weight:bold;padding:1.3mm}.activity-table .grade-title{font-size:9pt;line-height:10pt}.activity-table .subject-col{width:30%}.activity-table .assessment-col{width:24%}.activity-table .grade-guide{width:22%;padding:2mm 1mm;font-size:9pt;line-height:11pt;font-weight:normal}.activity-table .section-title{font-size:9.3pt;font-weight:bold;padding:.8mm}.activity-table .subject-name{text-align:left;padding-left:3mm}.activity-table .long{font-size:7.3pt;line-height:8pt}.guide-block{margin-bottom:8mm}.guide-block:last-child{margin-bottom:0}.signature-layout{width:100%;border-collapse:collapse;font-size:9pt;line-height:14pt}.signature-layout td{border:0}.signature-layout strong{font-size:10pt}.cover-card{height:178mm;border:.35mm solid #333;padding:5mm 7mm 4mm;text-align:center}.cover-upper{width:100%;border-collapse:collapse;text-align:center}.cover-upper td{text-align:center;border:0}.department{font-size:9.5pt;line-height:16pt;font-weight:bold}.report-heading{font-size:16pt;line-height:20pt;font-weight:bold}.detail-line{font-size:10pt;line-height:17pt}.report-title{display:inline-block;min-width:60mm;border:.3mm solid #222;padding:1.5mm 4mm;font-size:10pt;text-align:center}.student-panel{border:.35mm solid #222;padding:3.5mm;width:94%;margin:0 auto;text-align:left}.student-table{width:100%;border-collapse:separate;border-spacing:0 2mm;font-size:9pt;text-align:left}.student-table td{border:.28mm solid #333;padding:2mm 2.2mm;text-align:left}.student-table .label,.student-table .value{font-weight:bold}.student-table .third{width:33.33%}
</style>
<div class="front-page"><table class="front-layout"><tr><td class="front-left">
 <div class="activity-frame"><table class="activity-table">
  <tr><th colspan="3" class="main-title">Subject-wise Marks</th><th class="grade-title">Class (Grade)</th></tr>
  <tr><th class="subject-col">Subject</th><th class="assessment-col">Formative<br>Assessment<br>(FA)<br>Class (Grade)</th><th class="assessment-col">Summative<br>Assessment<br>(SA)<br>Class (Grade)</th><td rowspan="17" class="grade-guide"><div class="guide-block">75% and<br>above<br><strong>Outstanding</strong></div><div class="guide-block">60% to<br>74%<br><strong>Very Good</strong></div><div class="guide-block">45% to<br>59%<br><strong>Good</strong></div><div class="guide-block">35% to 44%<br><strong>Needs<br>Improvement</strong><br>(Can Do Better)</div><div class="guide-block">30% to<br>34%<br><strong>Satisfactory</strong><br>(With Effort<br>Can Improve)</div></td></tr>
  <tr><th colspan="3" class="section-title">Scholastic Areas</th></tr>
  <tr><td class="subject-name">Language</td><td><strong>A+</strong></td><td></td></tr>
  <tr><td class="subject-name">Mathematics</td><td><strong>A+</strong></td><td></td></tr>
  <tr><td class="subject-name">Science</td><td><strong>A+</strong></td><td></td></tr>
  <tr><td class="subject-name">Social Science</td><td></td><td></td></tr>
  <tr><td class="subject-name">Environmental Studies</td><td></td><td></td></tr>
  <tr><th colspan="3" class="section-title">Co-Scholastic Areas</th></tr>
  <tr><th>Subject</th><th>Formative<br>Assessment<br>Grade</th><th>Summative<br>Assessment<br>Grade</th></tr>
  <tr><td class="subject-name">Discipline</td><td></td><td></td></tr>
  <tr><td class="subject-name">Attitude</td><td></td><td></td></tr>
  <tr><td class="subject-name">Punctuality</td><td></td><td></td></tr>
  <tr><td class="subject-name">Cleanliness</td><td></td><td></td></tr>
  <tr><td class="subject-name">Initiative</td><td></td><td></td></tr>
  <tr><td class="subject-name">Physical<br>Fitness</td><td></td><td></td></tr>
  <tr><td class="subject-name long">Participation in<br>Co-curricular Activities</td><td></td><td></td></tr>
  <tr><td class="subject-name">Esthetic Sense</td><td></td><td></td></tr>
  <tr><td class="subject-name">General Meeting</td><td></td><td></td></tr>
 </table></div>
 <table class="signature-layout" width="100%" cellpadding="0" cellspacing="0">
  <tr><td style="height:14mm">&nbsp;</td></tr>
  <tr><td align="right" style="text-align:right;padding-right:5mm"><strong>Head Teacher</strong><br>Department of School Education, Government of Assam</td></tr>
  <tr><td style="height:10mm">&nbsp;</td></tr>
  <tr><td align="left" style="text-align:left;padding-left:2mm;font-size:7.5pt">Computer generated marksheet</td></tr>
 </table>
</td><td class="front-gap"></td><td class="front-right"><div class="cover-card">
 <table class="cover-upper" width="100%" cellpadding="0" cellspacing="0">
  <tr><td align="center" style="text-align:center;padding-bottom:5mm"><img src="<?=html_escape($topLogo)?>" alt="Education for All" style="height:18mm;width:12.1mm"></td></tr>
  <tr><td align="center" class="department" style="text-align:center;padding-bottom:3mm">Department of School Education, Government of Assam<br>Unified District Level Examination Committee<br>for Elementary Education</td></tr>
  <tr><td align="center" class="report-heading" style="text-align:center;padding:3mm 0 2mm">Statement of Marks, Grade Sheet</td></tr>
  <tr><td align="center" class="detail-line" style="text-align:center">District: <?=html_escape($district)?><?=($district && $state) ? ', ' : ''?><?=html_escape($state)?><br>Registration No.: <?=html_escape($institutionCode)?><br>Year of Passing: <?=html_escape($passingYear)?></td></tr>
  <tr><td align="center" style="text-align:center;padding:3mm 0 0"><img src="<?=html_escape($assamLogo)?>" alt="Assam Education" style="width:16mm;height:16mm"></td></tr>
  <tr><td align="center" style="text-align:center;padding:3mm 0 5mm"><span class="report-title">Annual Progress Report <?=html_escape($passingYear)?></span></td></tr>
 </table>
 <div class="student-panel"><table class="student-table">
  <tr><td colspan="3" align="left"><span class="label">Name:</span>&nbsp;&nbsp; <span class="value"><?=html_escape(trim($student['student_name']))?></span></td></tr>
  <tr><td class="third" align="left"><span class="label">Class:</span>&nbsp; <?=html_escape($student['class_name'])?></td><td class="third" align="left"><span class="label">Section:</span>&nbsp; <?=html_escape($student['section_name'])?></td><td class="third" align="left"><span class="label">Roll No.:</span>&nbsp; <?=html_escape($student['roll'])?></td></tr>
  <tr><td colspan="3" align="left"><span class="label">Name of the Institution:</span>&nbsp; <?=html_escape($schoolName)?></td></tr>
  <tr><td colspan="3" align="left"><span class="label">Address:</span>&nbsp; <?=html_escape($student['address'])?></td></tr>
 </table></div>
</div></td></tr></table></div>
