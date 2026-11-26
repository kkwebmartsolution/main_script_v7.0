<?php
defined('BASEPATH') or exit('No direct script access allowed');
$isStudent = (int) $card_user_type === 1;
$accent = $isStudent ? '#175a96' : '#08766e';
?>
<div style="box-sizing:border-box;width:100%;height:100%;border:1px solid #cbd5e1;border-radius:3mm;overflow:hidden;background:#fff;color:#172b45;font-family:Arial,sans-serif;text-align:center;line-height:1.2;position:relative;">
    <div aria-hidden="true" style="position:absolute;left:50%;top:55%;transform:translate(-50%,-50%);width:88%;height:52%;opacity:0.13;pointer-events:none;z-index:0;">{watermark}</div>
    <div style="position:relative;z-index:1;background:<?=$accent?>;color:#fff;height:18mm;box-sizing:border-box;padding:3mm 2mm 2mm;">
        <div style="font-size:10px;font-weight:700;line-height:1.25;overflow-wrap:anywhere;">{institute_name}</div>
        <div style="font-size:7px;letter-spacing:1.5px;margin-top:2mm;"><?=$isStudent ? 'STUDENT' : 'STAFF'?> IDENTITY CARD</div>
    </div>
    <div style="position:relative;z-index:1;padding:2mm 3mm;">
        <div style="height:20mm;margin:0 auto 1.5mm;display:flex;justify-content:center;align-items:center;"><?=$isStudent ? '{student_photo}' : '{staff_photo}'?></div>
        <div style="font-size:11px;font-weight:700;line-height:1.15;overflow-wrap:anywhere;">{name}</div>
        <div style="font-size:8px;font-weight:700;color:<?=$accent?>;margin:1mm 0 2mm;">ID: <?=$isStudent ? '{register_no}' : '{staff_id}'?></div>
        <table style="width:100%;border-collapse:collapse;text-align:left;font-size:8px;line-height:1.4;">
            <?php if ($isStudent): ?>
            <tr><td style="color:#64748b;width:17mm;">Class / Sec.</td><td style="font-weight:700;">{class} / {section}</td></tr>
            <tr><td style="color:#64748b;">Roll no.</td><td>{roll}</td></tr>
            <?php else: ?>
            <tr><td style="color:#64748b;width:17mm;">Designation</td><td style="font-weight:700;">{designation}</td></tr>
            <tr><td style="color:#64748b;">Department</td><td>{department}</td></tr>
            <?php endif; ?>
            <tr><td style="color:#64748b;">Blood group</td><td>{blood_group}</td></tr>
            <tr><td style="color:#64748b;">Contact</td><td>{mobileno}</td></tr>
        </table>
    </div>
    <div style="position:absolute;z-index:1;bottom:0;left:0;right:0;padding:1.5mm 3mm 2mm;border-top:1px solid #e2e8f0;background:#f8fafc;display:flex;align-items:center;gap:2mm;text-align:left;">
        <div style="width:14mm;height:14mm;flex-shrink:0;">{qr_code}</div>
        <div style="font-size:6.5px;line-height:1.45;overflow-wrap:anywhere;">
            <strong style="color:<?=$accent?>;">Issued: {print_date}</strong><br>
            Valid until: {expiry_date}<br>
            School: {institute_mobile_no}
        </div>
    </div>
</div>
