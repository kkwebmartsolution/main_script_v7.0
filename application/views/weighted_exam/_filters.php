<?php
$filterAction = isset($filter_action) ? $filter_action : current_url();
$showSubject = !empty($show_subject);
$showComponent = !empty($show_component);
$showSession = !empty($show_session);
?>
<?=form_open($filterAction,['method'=>'get','class'=>'validate'])?>
<section class="panel">
 <header class="panel-heading"><h4 class="panel-title"><i class="fas fa-filter"></i> Select Criteria</h4></header>
 <div class="panel-body"><div class="row">
  <?php if(is_superadmin_loggedin()):?><div class="col-md-2 mb-sm"><label>Branch <span class="required">*</span></label><select name="branch_id" class="form-control" data-plugin-selectTwo required onchange="if(this.value)window.location='<?=current_url()?>?branch_id='+this.value"><option value="">Select</option><?php foreach($branches as $b):?><option value="<?=$b['id']?>" <?=$branch_id==$b['id']?'selected':''?>><?=html_escape($b['name'])?></option><?php endforeach?></select></div><?php endif?>
  <?php if($showSession):?><div class="col-md-2 mb-sm"><label>Academic Session <span class="required">*</span></label><select id="session_id" name="session_id" class="form-control" data-plugin-selectTwo required><option value="">Select</option><?php foreach($sessions as $session):?><option value="<?=$session['id']?>" <?=$sessionId==$session['id']?'selected':''?>><?=html_escape($session['school_year'])?></option><?php endforeach?></select></div><?php endif?>
  <div class="col-md-3 mb-sm"><label>Examination <span class="required">*</span></label><select id="exam_id" name="exam_id" class="form-control" data-plugin-selectTwo required><option value="">Select</option><?php foreach($exams as $e):?><option value="<?=$e['id']?>" data-session="<?=$e['session_id']?>" <?=$examId==$e['id']?'selected':''?>><?=html_escape($e['name'])?> (<?=html_escape($e['school_year'])?>)</option><?php endforeach?></select></div>
  <div class="col-md-2 mb-sm"><label>Class <span class="required">*</span></label><select id="class_id" name="class_id" class="form-control" data-plugin-selectTwo required><option value="">Select</option><?php foreach($classes as $c):?><option value="<?=$c['id']?>" <?=$classId==$c['id']?'selected':''?>><?=html_escape($c['name'])?></option><?php endforeach?></select></div>
  <div class="col-md-2 mb-sm"><label>Section <span class="required">*</span></label><select id="section_id" name="section_id" class="form-control" data-plugin-selectTwo required><option value="">Select</option><?php foreach($sections as $s):?><option value="<?=$s['id']?>" <?=$sectionId==$s['id']?'selected':''?>><?=html_escape($s['name'])?></option><?php endforeach?></select></div>
  <?php if($showSubject):?><div class="col-md-3 mb-sm"><label>Subject <span class="required">*</span></label><select id="exam_subject_id" name="exam_subject_id" class="form-control" data-plugin-selectTwo required><option value="">Select</option><?php foreach($subjects as $s):?><option value="<?=$s['id']?>" <?=$examSubjectId==$s['id']?'selected':''?>><?=html_escape($s['subject_name'])?></option><?php endforeach?></select></div><?php endif?>
  <?php if($showComponent):?><div class="col-md-2 mb-sm"><label>Component <span class="required">*</span></label><select id="component" name="component" class="form-control" required><option value="">Select</option><?php foreach(['unit1'=>'Unit 1','unit2'=>'Unit 2','half_yearly'=>'Half-Yearly','annual'=>'Annual'] as $k=>$v):?><option value="<?=$k?>" <?=$component===$k?'selected':''?>><?=$v?></option><?php endforeach?></select></div><?php endif?>
 </div></div>
 <footer class="panel-footer text-right"><button class="btn btn-default"><i class="fas fa-filter"></i> Load</button></footer>
</section><?=form_close()?>

