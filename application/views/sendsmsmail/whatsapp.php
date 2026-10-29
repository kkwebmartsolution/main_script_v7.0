<style>
.wa-page { --wa-border:#e3e9e7; --wa-muted:#65766e; border-radius:14px; overflow:hidden; }
.wa-page .panel-heading {background:#f1faf6;border-bottom:1px solid #dcece3;padding:24px;}
.wa-page .panel-title {font-size:21px;color:#174c38;font-weight:700;line-height:1.4;}
.wa-page .wa-intro {margin:7px 0 0;color:#587165;}
.wa-page .panel-body {padding:24px;}
.wa-page .wa-branch {max-width:440px;margin-bottom:24px;}
.wa-page .wa-step {padding:0 0 23px;margin-bottom:23px;border-bottom:1px solid var(--wa-border);}
.wa-page .wa-step-title {display:flex;align-items:center;gap:10px;font-size:16px;font-weight:600;margin:0 0 18px;}
.wa-page .wa-number {display:inline-flex;align-items:center;justify-content:center;width:29px;height:29px;border-radius:50%;background:#eaf6ef;color:#23734c;font-size:13px;flex-shrink:0;}
.wa-page label {font-weight:600;margin-bottom:8px;}
.wa-page .form-control {min-height:43px;border-radius:7px;}
.wa-page textarea.form-control {min-height:180px;line-height:1.7;}
.wa-page .form-group {margin-bottom:18px;}
.wa-page .help-block,.wa-page .text-muted {font-size:12px;line-height:1.65;color:var(--wa-muted);}
.wa-page .wa-note {background:#f5f8f6;border-radius:8px;padding:12px 14px;margin:12px 0;color:#53675b;font-size:12px;line-height:1.7;}
.wa-page .wa-actions {display:flex;gap:10px;flex-wrap:wrap;margin-top:18px;}
.wa-page .btn {border-radius:7px;padding:10px 16px;}
.wa-page .wa-send-btn {background:#167c50;border-color:#167c50;color:#fff;font-weight:600;}
.wa-page .wa-send-btn:hover {background:#11633f;border-color:#11633f;}
.wa-page .wa-preview-card {border:1px solid var(--wa-border);border-radius:12px;overflow:hidden;margin-bottom:22px;}
.wa-page .wa-preview-head {background:#205c47;color:white;padding:17px 20px;display:flex;gap:12px;align-items:center;}
.wa-page .wa-preview-head i {font-size:27px;}
.wa-page .wa-preview-head strong {display:block;font-size:15px;}
.wa-page .wa-preview-head small {color:#d0e7da;}
.wa-page .wa-preview-content {padding:20px;background:#edf3ef;min-height:270px;}
.wa-page #wa-body {white-space:pre-wrap;overflow-wrap:anywhere;word-break:normal;background:white;color:#263d31;font-family:inherit;font-size:13px;line-height:1.8;border:0;border-radius:0 10px 10px;box-shadow:0 2px 5px #1836260c;padding:17px;margin:0;}
.wa-page #wa-body:empty {display:none;}
.wa-page #wa-preview-label {padding:13px 18px;margin:0;border-top:1px solid var(--wa-border);background:transparent;}
.wa-page .wa-empty {text-align:center;padding:35px 12px;color:#6b8073;}
.wa-page .wa-empty i {display:block;font-size:32px;margin-bottom:14px;color:#8fa99a;}
.wa-page .wa-empty strong {display:block;margin-bottom:7px;font-size:15px;}
.wa-page #wa-progress-box {border:1px solid var(--wa-border);padding:18px;border-radius:10px;margin-bottom:20px;}
.wa-page #wa-results p {padding:9px 0;margin:0;border-bottom:1px solid var(--wa-border);font-size:12px;}
.wa-page .progress {height:7px;border-radius:8px;margin:12px 0 16px;background:#e8efeb;}
.wa-page .progress-bar {background:#208658;}
.wa-page #wa-editor {border:1px solid var(--wa-border);border-radius:10px;margin-top:12px;}
.wa-page #wa-editor summary {cursor:pointer;font-weight:600;padding:18px;font-size:14px;}
.wa-page #wa-editor[open] summary {border-bottom:1px solid var(--wa-border);margin-bottom:18px;}
.wa-page .wa-editor-content {padding:0 20px 20px;}
.wa-page .wa-link {background:none;border:0;padding:0;color:#19784e;text-decoration:underline;font-size:12px;cursor:pointer;}
.wa-page :focus-visible {outline:2px solid #269461;outline-offset:3px;}
html.dark .wa-page {--wa-border:#414947;--wa-muted:#acbdb4;}
html.dark .wa-page .wa-note {background:#293d32;color:#d5e6dc;}
@media(max-width:767px) {.wa-page .panel-body,.wa-page .panel-heading {padding:18px;}.wa-page .wa-actions .btn {flex:1;}.wa-page .wa-preview-card {margin-top:24px;}.wa-page .wa-editor-content {padding:0 14px 16px;}}
</style>
<section class="panel wa-page">
    <header class="panel-heading"><h4 class="panel-title"><i class="fab fa-whatsapp"></i> WhatsApp Broadcast</h4><p class="wa-intro">Choose a message, select your students and send in a few simple steps.</p></header>
    <div class="panel-body">
        <div id="wa-error" class="alert alert-danger" role="alert" style="display:none"></div>
        <div class="form-group wa-branch">
            <label for="wa-branch">Branch</label>
            <?php if (is_superadmin_loggedin()): ?>
            <select id="wa-branch" class="form-control"><option value="">Select branch</option>
                <?php foreach ($branches as $b): ?><option value="<?= (int) $b['id'] ?>"><?= html_escape($b['name']) ?></option><?php endforeach; ?>
            </select>
            <?php else: ?>
            <input id="wa-branch" type="hidden" value="<?= (int) $branch_id ?>">
            <p><?= html_escape(get_type_name_by_id('branch', $branch_id)) ?></p>
            <?php endif; ?>
        </div>
        <div class="row">
            <div class="col-md-7">
                <form id="wa-send">
                    <div class="wa-step">
                    <h5 class="wa-step-title"><span class="wa-number">1</span> Choose your message</h5>
                    <div class="form-group"><label for="wa-template">Approved template</label><select id="wa-template" name="template_id" class="form-control" required><option value="">Select template</option></select></div>
                    <p class="text-muted" id="wa-template-help">Choose a saved template or <button type="button" class="wa-link" id="wa-add-template">add an approved template</button>.</p>
                    <div id="wa-date-group" class="form-group" style="display:none"><label for="wa-date">Payment due date</label><input type="date" id="wa-date" name="due_date" class="form-control"><p class="help-block">Choose or type the date for this broadcast. Fee records are not changed.</p></div>
                    </div>
                    <div class="wa-step">
                    <h5 class="wa-step-title"><span class="wa-number">2</span> Select recipients</h5>
                    <div class="form-group"><label for="wa-class">Class</label><select id="wa-class" name="class_id" class="form-control" required><option value="">Select class</option></select><p class="help-block">Students from the current academic session.</p></div>
                    <div class="form-group"><label for="wa-mode">Students</label><select id="wa-mode" name="student_mode" class="form-control"><option value="all">All students in this class</option><option value="selected">Select particular students</option></select></div>
                    <div id="wa-students-group" class="form-group" style="display:none"><label for="wa-students">Select students</label><select id="wa-students" name="students[]" class="form-control" multiple data-plugin-selectTwo style="width:100%"></select></div>
                    <div class="form-group"><label for="wa-recipient">Send to</label><select id="wa-recipient" name="recipient" class="form-control"><option value="parent">Parent / guardian WhatsApp number</option><option value="student">Student WhatsApp number</option></select></div>
                    <p id="wa-audience" class="wa-note" role="status" aria-live="polite">Select a class to see available students.</p>
                    </div>
                    <h5 class="wa-step-title"><span class="wa-number">3</span> Review &amp; send</h5>
                    <p class="text-muted">Preview a student's message to check the details before sending.</p>
                    <div class="wa-actions">
                    <button type="button" class="btn btn-default" id="wa-preview">Preview message</button>
                    <button type="submit" class="btn wa-send-btn" id="wa-start"><i class="fab fa-whatsapp"></i> Send messages</button>
                    </div>
                    <p class="text-muted" style="margin-top:14px">Fee reminders skip students with no pending fees. Pending amount includes fees and transport, after payments and discounts; fines are excluded.</p>
                </form>
            </div>
            <div class="col-md-5">
                <div class="wa-preview-card">
                    <div class="wa-preview-head"><i class="fab fa-whatsapp"></i><div><strong>Message preview</strong><small>Personalised for each student</small></div></div>
                    <div class="wa-preview-content"><div id="wa-empty-preview" class="wa-empty"><i class="far fa-comment-dots"></i><strong>Your message appears here</strong><span>Start by choosing an approved template.</span></div><pre id="wa-body"></pre></div>
                    <p id="wa-preview-label" class="text-muted">Student details will be filled automatically.</p>
                </div>
                <div id="wa-progress-box" style="display:none">
                    <strong>Sending progress</strong>
                    <div class="progress"><div id="wa-progress-bar" class="progress-bar" role="progressbar" aria-label="Messages processed" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" style="width:0%"></div></div>
                    <p id="wa-progress" role="status" aria-live="polite"></p>
                    <button id="wa-resume" class="btn btn-default" type="button" style="display:none">Resume sending</button>
                    <button id="wa-cancel" class="btn btn-default" type="button" style="display:none">Cancel remaining messages</button>
                    <p class="text-muted">Keep this page open while sending. Provider acceptance is not delivery confirmation.</p>
                    <div id="wa-results" style="max-height:220px;overflow:auto" aria-live="polite"></div>
                </div>
            </div>
        </div>
        <details id="wa-editor"><summary><i class="fas fa-plus-circle"></i> Add / update an approved template</summary>
            <div class="wa-editor-content">
            <p class="wa-note">Already have an approved template? Copy its exact name, language and message below. Get approval in your WhatsApp provider account first; saving here does not request approval.</p>
            <p class="text-muted">Use templates with text body variables. Media, header variables, OTP and dynamic buttons are not supported.</p>
            <form id="wa-template-form">
                <div class="row"><div class="col-md-6 form-group"><label for="wa-name">Exact approved template name</label><input id="wa-name" name="name" class="form-control" placeholder="fee_payment_reminder" pattern="[a-z0-9_]+" maxlength="150" required></div>
                <div class="col-md-6 form-group"><label for="wa-language">Exact language code</label><input id="wa-language" name="language" class="form-control" value="en" placeholder="en or en_US" required></div></div>
                <div class="form-group"><label for="wa-template-body">Approved message body</label><textarea id="wa-template-body" name="body" rows="12" class="form-control" required>🔔 *Fee Payment Reminder*
Dear *{{1}}*,
This is a gentle reminder that your fee payment of *₹{{2}}* is still pending.
💰 *Pending Amount:* ₹{{2}}
📅 *Payment Due Date:* {{3}}
We kindly request you to clear your outstanding fees at the earliest.
If you have already made the payment, please ignore this message.
Thank you for your cooperation.
*Regards,*
*{{4}}*</textarea></div>
                <h5 class="wa-step-title">Personalise the message</h5><p class="text-muted">Choose what fills each variable. Repeated variables use the same value.</p>
                <div id="wa-mapping" class="row"></div>
                <div class="checkbox"><label><input type="checkbox" name="fee_reminder" value="1" checked> Fee reminder: skip students with zero pending fees</label></div>
                <div class="checkbox"><label><input type="checkbox" name="approved" value="1" required> I confirm this exact template and language are already approved by my WhatsApp provider and require only body variables.</label></div>
                <button type="submit" class="btn btn-default">Save approved template</button>
                <span id="wa-saved" class="text-success" role="status"></span>
            </form>
            </div>
        </details>
    </div>
</section>
<script>
$(function () {
    'use strict';
    var endpoint = <?= json_encode(base_url('sendsmsmail/whatsapp_action')) ?>;
    var csrfName = <?= json_encode($this->security->get_csrf_token_name()) ?>;
    var csrfHash = <?= json_encode($this->security->get_csrf_hash()) ?>;
    var fields = <?= json_encode($fields) ?>;
    var templates = [], job = null, running = false, generation = 0;
    var studentsLoading = false;
    function audience() {
        var total = $('#wa-students option').length;
        var count = $('#wa-mode').val() === 'selected' ? ($('#wa-students').val() || []).length : total;
        var recipient = $('#wa-recipient').val() === 'parent' ? 'parent / guardian' : 'student';
        $('#wa-audience').text(studentsLoading ? 'Loading students...' : !$('#wa-class').val() ? 'Select a class to see available students.' : !total ? 'No active students found in this class.' : count + ' student' + (count === 1 ? '' : 's') + ' selected · Sending to ' + recipient + ' numbers.');
        if (!running) $('#wa-start, #wa-preview').prop('disabled', studentsLoading || !count || !selectedTemplate());
    }
    function resetPreview() {
        var t = selectedTemplate();
        $('#wa-body').text(t ? t.body : '');
        $('#wa-empty-preview').toggle(!t);
        $('#wa-preview-label').text(t ? 'Template only. Preview to see a student’s actual details.' : 'Student details will be filled automatically.');
    }
    function error(message) { $('#wa-error').text(message).toggle(!!message); }
    function request(action, data) {
        data = data || [];
        data.push({name:'action', value:action}, {name:'branch_id', value:$('#wa-branch').val()}, {name:csrfName, value:csrfHash});
        return $.ajax({url:endpoint, type:'POST', data:data, dataType:'json'});
    }
    function failed(xhr) { error(xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'Request failed. Check your connection and reload if your login expired.'); }
    function options(select, rows, prompt) {
        select.empty().append($('<option>').val('').text(prompt));
        rows.forEach(function(r) { select.append($('<option>').val(r.id).text(r.name)); });
    }
    function load() {
        generation++;
        var version = generation;
        templates = []; job = null;
        options($('#wa-template'), [], 'Select template'); options($('#wa-class'), [], 'Select class');
        $('#wa-students').empty().trigger('change'); $('#wa-progress-box').hide();
        studentsLoading = false; $('#wa-template').trigger('change'); audience();
        if (!$('#wa-branch').val()) return;
        request('load').done(function(r) {
            if (version !== generation) return;
            templates = r.templates;
            options($('#wa-template'), templates.map(function(t) {return {id:t.id, name:t.name + ' (' + t.language + ')'};}), templates.length ? 'Select template' : 'No saved templates — add an approved template below');
            options($('#wa-class'), r.classes, 'Select class');
            job = r.job; if (job) progress(job);
            if (templates.length === 1) $('#wa-template').val(templates[0].id).trigger('change');
            audience();
        }).fail(failed);
    }
    function selectedTemplate() { return templates.filter(function(t) {return String(t.id) === $('#wa-template').val();})[0]; }
    $('#wa-template').on('change', function() {
        var t = selectedTemplate(), needsDate = t && JSON.parse(t.field_map).indexOf('due_date') !== -1;
        $('#wa-date-group').toggle(!!needsDate); $('#wa-date').prop('required', !!needsDate);
        resetPreview(); audience();
    });
    $('#wa-class').on('change', function() {
        var classId = $(this).val(), version = generation;
        studentsLoading = !!classId;
        $('#wa-students').empty().trigger('change');
        resetPreview(); audience();
        if (!classId) return;
        request('students', [{name:'class_id', value:classId}]).done(function(r) {
            if (version !== generation || $('#wa-class').val() !== classId) return;
            r.students.forEach(function(s) { $('#wa-students').append($('<option>').val(s.id).text(s.student_name + ' — ' + s.register_no + ' / ' + (s.section_name || ''))); });
            studentsLoading = false;
            $('#wa-students').trigger('change');
        }).fail(function(xhr) {if (version !== generation || $('#wa-class').val() !== classId) return; studentsLoading = false; audience(); failed(xhr);});
    });
    $('#wa-mode').on('change', function() { var selected = $(this).val() === 'selected'; $('#wa-students-group').toggle(selected); $('#wa-students').prop('required', selected); });
    $('#wa-mode, #wa-students, #wa-recipient, #wa-date').on('change', function() { resetPreview(); audience(); });
    $('#wa-add-template').on('click', function() {
        document.getElementById('wa-editor').open = true;
        document.getElementById('wa-editor').scrollIntoView({behavior:'smooth', block:'start'});
        $('#wa-name').trigger('focus');
    });
    $('#wa-preview').on('click', function() {
        error(''); if (!document.getElementById('wa-send').reportValidity()) return;
        request('preview', $('#wa-send').serializeArray()).done(function(r) { $('#wa-body').text(r.body); $('#wa-preview-label').text('Example: ' + r.student + '. Selected students: ' + r.count + '.'); }).fail(failed);
    });
    function progress(r) {
        job = r; $('#wa-progress-box').show();
        var percent = r.total ? Math.round(r.next / r.total * 100) : 0;
        $('#wa-progress-bar').css('width', percent + '%').attr('aria-valuenow', percent);
        $('#wa-progress').text(r.next + ' / ' + r.total + ' processed · ' + r.accepted + ' accepted · ' + r.failed + ' failed/unconfirmed · ' + r.skipped + ' skipped');
        $('#wa-resume').toggle(!running && r.next < r.total);
        $('#wa-cancel').toggle(!running && r.next < r.total);
    }
    function lock(value) {
        running = value;
        $('#wa-send :input, #wa-branch, #wa-template-form :input').prop('disabled', value);
        $('#wa-start').html(value ? '<i class="fas fa-spinner fa-spin"></i> Sending...' : '<i class="fab fa-whatsapp"></i> Send messages');
        $('#wa-resume, #wa-cancel').hide();
        if (!value) audience();
    }
    function step() {
        if (!job || job.next >= job.total) { lock(false); if (job) progress(job); return; }
        request('step', [{name:'token',value:job.token},{name:'next',value:job.next}]).done(function(r) {
            if (r.next > job.next && r.last) $('#wa-results').prepend($('<p>').text(r.last));
            progress(r); step();
        }).fail(function(xhr) { lock(false); progress(job); failed(xhr); });
    }
    $('#wa-send').on('submit', function(e) {
        e.preventDefault(); if (running) return; error('');
        var data = $(this).serializeArray(); lock(true);
        request('start', data).done(function(r) { $('#wa-results').empty(); progress(r); step(); }).fail(function(xhr) {lock(false); failed(xhr); if (job) progress(job);});
    });
    $('#wa-resume').on('click', function() { error(''); lock(true); step(); });
    $('#wa-cancel').on('click', function() {
        if (!job || running) return;
        request('cancel', [{name:'token',value:job.token}]).done(function() {
            job = null; $('#wa-resume, #wa-cancel').hide(); $('#wa-progress').append(' — Remaining messages cancelled.');
        }).fail(failed);
    });
    $('#wa-branch').on('change', load);
    function mapping() {
        var previous = $('#wa-mapping select').map(function() {return $(this).val();}).get();
        var matches = $('#wa-template-body').val().match(/\{\{\s*\d+\s*\}\}/g) || [], ids = [];
        matches.forEach(function(m) {var n = parseInt(m.replace(/\D/g,''),10); if (ids.indexOf(n) < 0) ids.push(n);});
        ids.sort(function(a,b){return a-b;}); $('#wa-mapping').empty();
        var defaults = ['student_name','pending_amount','due_date','school_name'];
        ids.forEach(function(id,i) {
            var select = $('<select class="form-control" name="mapping[]" required>').attr('id','wa-map-' + id);
            $.each(fields, function(value,label) {select.append($('<option>').val(value).text(label));});
            select.val(previous[i] || defaults[i] || 'student_name');
            $('#wa-mapping').append($('<div class="col-md-6 form-group">').append($('<label>').attr('for','wa-map-' + id).text('{{' + id + '}} value from'), select));
        });
    }
    $('#wa-template-body').on('input', mapping);
    $('#wa-template-form').on('submit', function(e) {
        e.preventDefault(); error(''); $('#wa-saved').text('');
        var button = $(this).find(':submit').prop('disabled',true);
        request('save_template', $(this).serializeArray()).done(function() {$('#wa-saved').text('Saved. Select the template above to send.'); load();}).fail(failed).always(function(){button.prop('disabled',false);});
    });
    mapping(); load();
});
</script>
