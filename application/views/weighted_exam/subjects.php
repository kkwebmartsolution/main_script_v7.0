<section class="panel">
<?=form_open('weighted_exam/subjects',['method'=>'get'])?>
 <header class="panel-heading"><h4 class="panel-title">Select Examination and Class</h4></header>
 <div class="panel-body"><div class="row">
  <?php if(is_superadmin_loggedin()):?><div class="col-md-2"><label>Branch</label><select name="branch_id" class="form-control" data-plugin-selectTwo required><option value="">Select</option><?php foreach($branches as $b):?><option value="<?=$b['id']?>" <?=$branch_id==$b['id']?'selected':''?>><?=html_escape($b['name'])?></option><?php endforeach?></select></div><?php endif?>
  <div class="col-md-3"><label>Examination</label><select name="exam_id" class="form-control" data-plugin-selectTwo required><option value="">Select</option><?php foreach($exams as $e):?><option value="<?=$e['id']?>" <?=$exam_id==$e['id']?'selected':''?>><?=html_escape($e['name'])?></option><?php endforeach?></select></div>
  <div class="col-md-2"><label>Class</label><select name="class_id" class="form-control" data-plugin-selectTwo required><option value="">Select</option><?php foreach($classes as $c):?><option value="<?=$c['id']?>" <?=$class_id==$c['id']?'selected':''?>><?=html_escape($c['name'])?></option><?php endforeach?></select></div>
  <div class="col-md-3"><label>Section (subject allocation source)</label><select name="section_id" class="form-control" data-plugin-selectTwo><option value="0">All sections</option><?php foreach($sections as $s):?><option value="<?=$s['id']?>" <?=$section_id==$s['id']?'selected':''?>><?=html_escape($s['name'])?></option><?php endforeach?></select></div>
  <div class="col-md-2"><label>&nbsp;</label><button class="btn btn-default btn-block">Load</button></div>
 </div></div>
<?=form_close()?></section>

<?php if($exam_id&&$class_id):$configured=[];foreach($configured_subjects as $subject)$configured[$subject['subject_id']]=$subject;?>
<?=form_open('weighted_exam/subjects')?>
<?php foreach(['branch_id'=>$branch_id,'exam_id'=>$exam_id,'class_id'=>$class_id,'section_id'=>$section_id] as $key=>$value):?><input type="hidden" name="<?=$key?>" value="<?=$value?>"><?php endforeach?>
<section class="panel">
 <header class="panel-heading"><h4 class="panel-title">Subject Applicability, Pass Marks, Full Marks and Weightages</h4></header>
 <div class="panel-body table-responsive">
  <div class="alert alert-info">Subjects Academic → Subjects → Class Assign se load hote hain. Final Unit Test ke marks Unit 1 + Unit 2 se automatically calculate hote hain; teacher iske marks enter nahi karta.</div>
  <?php if(!$available_subjects):?><div class="alert alert-warning">Is class/section ke liye koi subject assigned nahi hai. Pehle Academic → Subjects → Class Assign mein subject assign karein.</div><?php else:?>
  <table class="table table-bordered table-condensed">
   <thead><tr><th rowspan="2">Include</th><th rowspan="2">Subject</th><th rowspan="2">Order</th><th rowspan="2">Final %</th><th rowspan="2">Pass Marks<br>(%)</th><th>Unit 1</th><th>Unit 2</th><th colspan="2">Final Unit Test</th><th colspan="2">Half-Yearly</th><th colspan="2">Annual</th></tr><tr><th>Enabled / Full</th><th>Enabled / Full</th><th>Full Marks</th><th>Weight %</th><th>Enabled / Full</th><th>Weight %</th><th>Enabled / Full</th><th>Weight %</th></tr></thead>
   <tbody>
   <?php foreach($available_subjects as $index=>$available):$existing=isset($configured[$available['subject_id']])?$configured[$available['subject_id']]:null;$u1=$existing&&isset($existing['components']['unit1'])?$existing['components']['unit1']:null;$u2=$existing&&isset($existing['components']['unit2'])?$existing['components']['unit2']:null;$half=$existing&&isset($existing['components']['half_yearly'])?$existing['components']['half_yearly']:null;$annual=$existing&&isset($existing['components']['annual'])?$existing['components']['annual']:null;$unitWeight=$existing?$existing['unit_test_weightage']:20;$review=$existing&&!empty($existing['weightage_review_required']);?>
    <tr>
     <td><input type="checkbox" name="subjects[<?=$available['subject_id']?>][selected]" value="1" <?=$existing?'checked':''?>></td>
     <td><?=html_escape($available['name'])?></td>
     <td><input type="number" class="form-control input-sm" name="subjects[<?=$available['subject_id']?>][display_order]" value="<?=$existing?$existing['display_order']:$index+1?>" min="0"></td>
     <td><input type="checkbox" name="subjects[<?=$available['subject_id']?>][include]" value="1" <?=!$existing||$existing['include_in_percentage']?'checked':''?>></td>
     <td><input type="number" step="0.01" min="0" max="100" class="form-control input-sm" name="subjects[<?=$available['subject_id']?>][pass_marks]" value="<?=$existing?$existing['pass_marks']:'33.00'?>"></td>
     <td><label><input type="checkbox" class="unit-enabled" name="subjects[<?=$available['subject_id']?>][components][unit1][enabled]" value="1" <?=!$existing||($u1&&$u1['enabled'])?'checked':''?>> Enabled</label><input type="number" step="0.01" min="0.01" class="form-control input-sm unit-full" name="subjects[<?=$available['subject_id']?>][components][unit1][full_marks]" value="<?=$u1?$u1['full_marks']:50?>"></td>
     <td><label><input type="checkbox" class="unit-enabled" name="subjects[<?=$available['subject_id']?>][components][unit2][enabled]" value="1" <?=!$existing||($u2&&$u2['enabled'])?'checked':''?>> Enabled</label><input type="number" step="0.01" min="0.01" class="form-control input-sm unit-full" name="subjects[<?=$available['subject_id']?>][components][unit2][full_marks]" value="<?=$u2?$u2['full_marks']:50?>"></td>
     <td><input type="text" class="form-control input-sm final-unit-full" readonly value=""></td>
     <td><input type="number" step="0.0001" min="0" max="100" class="form-control input-sm unit-weight" name="subjects[<?=$available['subject_id']?>][unit_test_weightage]" value="<?=$unitWeight?>"><?php if($review):?><small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Review required</small><?php endif?></td>
     <?php foreach(['half_yearly'=>[$half,100,30],'annual'=>[$annual,100,50]] as $component=>$values):$config=$values[0];?>
      <td><label><input type="checkbox" name="subjects[<?=$available['subject_id']?>][components][<?=$component?>][enabled]" value="1" <?=!$existing||($config&&$config['enabled'])?'checked':''?>> Enabled</label><input type="number" step="0.01" min="0.01" class="form-control input-sm" name="subjects[<?=$available['subject_id']?>][components][<?=$component?>][full_marks]" value="<?=$config?$config['full_marks']:$values[1]?>"></td>
      <td><input type="number" step="0.0001" min="0" max="100" class="form-control input-sm" name="subjects[<?=$available['subject_id']?>][components][<?=$component?>][weightage]" value="<?=$config?$config['weightage']:$values[2]?>"></td>
     <?php endforeach?>
    </tr>
   <?php endforeach?>
   </tbody>
  </table><?php endif?>
 </div>
 <?php if($available_subjects):?><footer class="panel-footer text-right"><button name="save_subjects" value="1" class="btn btn-default"><i class="fas fa-save"></i> Save Configuration</button></footer><?php endif?>
</section><?=form_close()?><?php endif?>
<script>$(function(){function units($row){var enabled=$row.find('.unit-enabled:checked'),total=0;enabled.each(function(){total+=parseFloat($(this).closest('td').find('.unit-full').val())||0;});$row.find('.final-unit-full').val(enabled.length?total.toFixed(2):'N/A');$row.find('.unit-weight').val(enabled.length?($row.find('.unit-weight').val()||20):0).prop('readonly',!enabled.length);}$('.unit-enabled,.unit-full').on('change input',function(){units($(this).closest('tr'));});$('tbody tr').each(function(){units($(this));});<?php if(is_superadmin_loggedin()):?>$('select[name="branch_id"]').on('change',function(){if(this.value)window.location='<?=base_url('weighted_exam/subjects')?>?branch_id='+this.value;});<?php endif?>});</script>
