<?php $filter_action=base_url('weighted_exam/schedule');$this->load->view('weighted_exam/_filters',get_defined_vars());?>
<?php if($examId&&$classId&&$sectionId):?>
<?=form_open('weighted_exam/schedule')?>
<?php foreach(['branch_id'=>$branch_id,'exam_id'=>$examId,'class_id'=>$classId,'section_id'=>$sectionId] as $key=>$value):?><input type="hidden" name="<?=$key?>" value="<?=$value?>"><?php endforeach?>
<section class="panel">
 <header class="panel-heading"><h4 class="panel-title"><i class="fas fa-calendar-alt"></i> Subject-wise Examination Schedule</h4></header>
 <div class="panel-body table-responsive">
  <?php if(!$subjects):?><div class="alert alert-warning">Pehle Exam Subjects page par subjects configure karein.</div><?php else:?>
  <table class="table table-bordered table-hover table-condensed">
   <thead><tr><th>Subject</th><th>Component</th><th>Date</th><th>Start Time</th><th>End Time</th><th>Room</th><th>Instructions</th></tr></thead>
   <tbody>
   <?php foreach($subjects as $subject):foreach(['unit1'=>'Unit 1','unit2'=>'Unit 2','half_yearly'=>'Half-Yearly','annual'=>'Annual'] as $component=>$label):if(empty($subject['components'][$component]['enabled']))continue;$row=isset($schedule[$subject['id']][$component])?$schedule[$subject['id']][$component]:[];?>
    <tr>
     <td><?=html_escape($subject['subject_name'])?></td><td><?=$label?></td>
     <td><input type="date" class="form-control" name="schedule[<?=$subject['id']?>][<?=$component?>][exam_date]" value="<?=html_escape(isset($row['exam_date'])?$row['exam_date']:'')?>"></td>
     <td><input type="time" class="form-control" name="schedule[<?=$subject['id']?>][<?=$component?>][start_time]" value="<?=html_escape(isset($row['start_time'])?substr($row['start_time'],0,5):'')?>"></td>
     <td><input type="time" class="form-control" name="schedule[<?=$subject['id']?>][<?=$component?>][end_time]" value="<?=html_escape(isset($row['end_time'])?substr($row['end_time'],0,5):'')?>"></td>
     <td><input type="text" class="form-control" maxlength="100" name="schedule[<?=$subject['id']?>][<?=$component?>][room_no]" value="<?=html_escape(isset($row['room_no'])?$row['room_no']:'')?>"></td>
     <td><input type="text" class="form-control" maxlength="500" name="schedule[<?=$subject['id']?>][<?=$component?>][instructions]" value="<?=html_escape(isset($row['instructions'])?$row['instructions']:'')?>"></td>
    </tr>
   <?php endforeach;endforeach?>
   </tbody>
  </table><?php endif?>
 </div>
 <?php if($subjects):?><footer class="panel-footer text-right"><button name="save_schedule" value="1" class="btn btn-default"><i class="fas fa-save"></i> Save Schedule</button></footer><?php endif?>
</section><?=form_close()?>
<?php endif?>
