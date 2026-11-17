<?php defined('BASEPATH') or exit('No direct script access allowed');

class Weighted_exam_model extends CI_Model
{
    private $components = ['unit1', 'unit2', 'half_yearly', 'annual'];

    public function branchId()
    {
        if (!is_superadmin_loggedin()) return (int)get_loggedin_branch_id();
        $requested = (int)$this->input->post_get('branch_id');
        if ($requested > 0 && $this->db->query('SELECT id FROM branch WHERE id = ? LIMIT 1',[$requested])->num_rows()) {
            $this->session->set_userdata('weighted_exam_branch_id',$requested);
            return $requested;
        }
        return (int)$this->session->userdata('weighted_exam_branch_id');
    }

    public function exams($sessionId = null)
    {
        $this->db->select('we.*,sy.school_year')->from('weighted_exams we')->join('schoolyear sy', 'sy.id=we.session_id', 'left');
        $this->db->where('we.branch_id', $this->branchId());
        if ($sessionId) $this->db->where('we.session_id', (int) $sessionId);
        return $this->db->order_by('we.id', 'DESC')->get()->result_array();
    }

    public function exam($id, $lock = false)
    {
        if ($lock) $this->db->query('SELECT id FROM weighted_exams WHERE id=? AND branch_id=? FOR UPDATE', [(int)$id, $this->branchId()]);
        return $this->db->where(['id' => (int)$id, 'branch_id' => $this->branchId()])->get('weighted_exams')->row_array();
    }

    public function saveExam(array $post)
    {
        $id = !empty($post['id']) ? (int)$post['id'] : 0;
        $existing = $id ? $this->exam($id) : null;
        if ($this->branchId() <= 0) throw new RuntimeException('Select a branch first.');
        if ($id && !$existing) throw new RuntimeException('Examination not found in the selected branch.');
        if ($existing && in_array($existing['status'], ['finalized','published'], true)) throw new RuntimeException('Finalized or published exams must be reopened before editing.');
        $data = [
            'branch_id' => $this->branchId(), 'session_id' => (int)$post['session_id'],
            'name' => trim($post['name']), 'description' => trim((string)$post['description']),
            'publication_date' => empty($post['publication_date']) ? null : $post['publication_date'],
            'status' => in_array($post['status'], ['draft','active'], true) ? $post['status'] : 'draft',
            'absence_policy' => isset($post['absence_policy']) && $post['absence_policy'] === 'zero' ? 'zero' : 'incomplete',
            'ranking_enabled' => empty($post['ranking_enabled']) ? 0 : 1,
        ];
        $this->db->trans_start();
        if ($id) $this->db->where(['id'=>$id,'branch_id'=>$this->branchId()])->update('weighted_exams', $data);
        else { $data['created_by'] = get_loggedin_user_id(); $this->db->insert('weighted_exams', $data); $id = $this->db->insert_id(); }
        $classIds = array_values(array_unique(array_filter(array_map('intval', isset($post['class_ids']) ? $post['class_ids'] : []))));
        $sectionIds = array_values(array_unique(array_filter(array_map('intval', isset($post['section_ids']) ? $post['section_ids'] : []))));
        if ($classIds) {
            $this->db->where('exam_id', $id)->delete('weighted_exam_classes');
            foreach ($classIds as $classId) {
                if (!$this->db->where(['id'=>$classId,'branch_id'=>$this->branchId()])->count_all_results('class')) continue;
                $validSections = $this->db->select('sa.section_id')->from('sections_allocation sa')->join('section s','s.id=sa.section_id')->where(['sa.class_id'=>$classId,'s.branch_id'=>$this->branchId()])->get()->result_array();
                $validSections = array_map(function($r){ return (int)$r['section_id']; }, $validSections);
                $selected = $sectionIds ? array_values(array_intersect($validSections, $sectionIds)) : $validSections;
                if (!$selected) $selected = [0];
                foreach ($selected as $sectionId) $this->db->insert('weighted_exam_classes', ['exam_id'=>$id,'class_id'=>$classId,'section_id'=>$sectionId]);
            }
        }
        $this->db->trans_complete();
        if (!$this->db->trans_status()) throw new RuntimeException('Unable to save examination.');
        return $id;
    }

    public function examClasses($examId)
    {
        return $this->db->select('wec.*,c.name class_name,s.name section_name')->from('weighted_exam_classes wec')
            ->join('class c','c.id=wec.class_id')->join('section s','s.id=wec.section_id','left')
            ->where('wec.exam_id',(int)$examId)->order_by('c.name')->order_by('s.name')->get()->result_array();
    }

    public function classes()
    {
        return $this->db->where('branch_id',$this->branchId())->order_by('name')->get('class')->result_array();
    }

    public function sections()
    {
        return $this->db->where('branch_id',$this->branchId())->order_by('name')->get('section')->result_array();
    }

    public function assignedSubjects($examId, $classId, $sectionId = 0)
    {
        $exam = $this->exam($examId); if (!$exam) return [];
        $this->db->select('s.id subject_id,s.name,MIN(sa.id) assign_id,wes.id exam_subject_id,wes.display_order,wes.include_in_percentage')
            ->from('subject_assign sa')->join('subject s','s.id=sa.subject_id')
            ->join('weighted_exam_subjects wes','wes.exam_id='.(int)$examId.' AND wes.class_id=sa.class_id AND wes.subject_id=sa.subject_id','left')
            ->where(['sa.branch_id'=>$this->branchId(),'sa.session_id'=>(int)$exam['session_id'],'sa.class_id'=>(int)$classId]);
        if ($sectionId) $this->db->where('sa.section_id',(int)$sectionId);
        if (is_teacher_loggedin()) $this->db->where('sa.teacher_id',get_loggedin_user_id());
        return $this->db->group_by('s.id')->order_by('COALESCE(wes.display_order,999)')->order_by('s.name')->get()->result_array();
    }

    public function saveSubjects($examId, $classId, array $subjects)
    {
        $exam = $this->exam($examId); if (!$exam || in_array($exam['status'],['finalized','published'],true)) throw new RuntimeException('This examination is locked.');
        $valid = $this->assignedSubjects($examId,$classId);
        $validIds = array_map(function($r){return (int)$r['subject_id'];},$valid);
        $selectedIds = [];
        $this->db->trans_start();
        foreach ($subjects as $subjectId => $row) {
            $subjectId=(int)$subjectId; if (empty($row['selected']) || !in_array($subjectId,$validIds,true)) continue;
            $selectedIds[]=$subjectId;
            $existing=$this->db->where(['exam_id'=>(int)$examId,'class_id'=>(int)$classId,'subject_id'=>$subjectId])->get('weighted_exam_subjects')->row_array();
            $passMarks=isset($row['pass_marks'])?(float)$row['pass_marks']:33.0;
            if($passMarks<0||$passMarks>100){$this->db->trans_rollback();throw new RuntimeException('Subject pass marks must be between 0 and 100%.');}
            $unitEnabled=!empty($row['components']['unit1']['enabled'])||!empty($row['components']['unit2']['enabled']);
            $unitWeight=$unitEnabled?(float)(isset($row['unit_test_weightage'])?$row['unit_test_weightage']:20):0;
            if($unitWeight<0||$unitWeight>100){$this->db->trans_rollback();throw new RuntimeException('Final Unit Test weightage must be between 0 and 100%.');}
            $data=['exam_id'=>(int)$examId,'class_id'=>(int)$classId,'subject_id'=>$subjectId,'display_order'=>(int)$row['display_order'],'include_in_percentage'=>empty($row['include'])?0:1,'pass_marks'=>$passMarks,'unit_test_weightage'=>$unitWeight,'weightage_review_required'=>0,'combined_weight_migrated'=>1];
            if($existing){$this->db->where('id',$existing['id'])->update('weighted_exam_subjects',$data);$esid=$existing['id'];}
            else{$this->db->insert('weighted_exam_subjects',$data);$esid=$this->db->insert_id();}
            foreach($this->components as $component){
                $cr=isset($row['components'][$component])?$row['components'][$component]:[];
                $enabled=empty($cr['enabled'])?0:1; $full=$enabled?(float)$cr['full_marks']:null;
                $weight=in_array($component,['unit1','unit2'],true)?0:(float)(isset($cr['weightage'])?$cr['weightage']:0);
                if($enabled&&$full<=0){$this->db->trans_rollback();throw new RuntimeException(ucwords(str_replace('_',' ',$component)).' full marks must be greater than zero.');}
                if($weight<0||$weight>100){$this->db->trans_rollback();throw new RuntimeException('Every weightage must be between 0 and 100.');}
                $cd=['exam_subject_id'=>$esid,'component'=>$component,'enabled'=>$enabled,'full_marks'=>$full,'weightage'=>$weight];
                $old=$this->db->where(['exam_subject_id'=>$esid,'component'=>$component])->get('weighted_exam_subject_components')->row_array();
                if($old)$this->db->where('id',$old['id'])->update('weighted_exam_subject_components',$cd);else$this->db->insert('weighted_exam_subject_components',$cd);
            }
            $total=0;
            if ($unitEnabled) $total += $unitWeight;
            foreach(['half_yearly','annual'] as $c) if(!empty($row['components'][$c]['enabled'])) $total+=(float)$row['components'][$c]['weightage'];
            if(abs($total-100)>0.0001){$this->db->trans_rollback();throw new RuntimeException('Active weightage for every selected subject must total exactly 100%.');}
        }
        if($selectedIds){$this->db->where(['exam_id'=>(int)$examId,'class_id'=>(int)$classId])->where_not_in('subject_id',$selectedIds)->delete('weighted_exam_subjects');}
        else $this->db->where(['exam_id'=>(int)$examId,'class_id'=>(int)$classId])->delete('weighted_exam_subjects');
        $this->db->trans_complete(); if(!$this->db->trans_status())throw new RuntimeException('Unable to save subject configuration.');
    }

    public function configuredSubjects($examId, $classId)
    {
        if (!$this->exam($examId)) return [];
        $rows=$this->db->select('wes.*,s.name subject_name,s.subject_code')->from('weighted_exam_subjects wes')->join('subject s','s.id=wes.subject_id')
            ->where(['wes.exam_id'=>(int)$examId,'wes.class_id'=>(int)$classId,'s.branch_id'=>$this->branchId()])->order_by('wes.display_order')->order_by('s.name')->get()->result_array();
        foreach($rows as &$r){$r['components']=[];$cs=$this->db->where('exam_subject_id',$r['id'])->get('weighted_exam_subject_components')->result_array();foreach($cs as $c)$r['components'][$c['component']]=$c;$u1=isset($r['components']['unit1'])?$r['components']['unit1']:null;$u2=isset($r['components']['unit2'])?$r['components']['unit2']:null;$unitEnabled=($u1&&!empty($u1['enabled']))||($u2&&!empty($u2['enabled']));$r['components']['final_unit']=['enabled'=>$unitEnabled?1:0,'full_marks'=>$unitEnabled?(float)($u1&&$u1['enabled']?$u1['full_marks']:0)+(float)($u2&&$u2['enabled']?$u2['full_marks']:0):null,'weightage'=>(float)$r['unit_test_weightage']];}
        return $rows;
    }

    public function markEntrySubjects($examId,$classId,$sectionId)
    {
        $exam=$this->exam($examId);if(!$exam)return [];
        if(!$this->db->where(['exam_id'=>(int)$examId,'class_id'=>(int)$classId,'section_id'=>(int)$sectionId])->count_all_results('weighted_exam_classes'))return [];
        $this->db->select('wes.id,wes.subject_id,s.name subject_name,s.subject_code,wes.display_order')
            ->from('weighted_exam_subjects wes')->join('subject s','s.id=wes.subject_id')
            ->join('subject_assign sa','sa.subject_id=wes.subject_id AND sa.class_id=wes.class_id')
            ->where(['wes.exam_id'=>(int)$examId,'wes.class_id'=>(int)$classId,'sa.section_id'=>(int)$sectionId,'sa.session_id'=>(int)$exam['session_id'],'sa.branch_id'=>$this->branchId(),'s.branch_id'=>$this->branchId()]);
        if(is_teacher_loggedin())$this->db->where('sa.teacher_id',get_loggedin_user_id());
        $rows=$this->db->group_by('wes.id')->order_by('wes.display_order')->order_by('s.name')->get()->result_array();
        $configured=$this->configuredSubjects($examId,$classId);$byId=[];foreach($configured as $subject)$byId[$subject['id']]=$subject;
        foreach($rows as &$row)$row['components']=isset($byId[$row['id']])?$byId[$row['id']]['components']:[];
        return $rows;
    }

    public function students($examId,$classId,$sectionId)
    {
        $exam=$this->exam($examId); if(!$exam)return [];
        return $this->db->select("e.id enroll_id,e.student_id,e.roll,s.register_no,CONCAT_WS(' ',s.first_name,s.last_name) student_name")
            ->from('enroll e')->join('student s','s.id=e.student_id')->where(['e.branch_id'=>$this->branchId(),'e.session_id'=>$exam['session_id'],'e.class_id'=>(int)$classId,'e.section_id'=>(int)$sectionId,'s.active'=>1])->order_by('e.roll')->get()->result_array();
    }

    public function canTeach($examSubjectId,$sectionId)
    {
        $es=$this->db->where('id',(int)$examSubjectId)->get('weighted_exam_subjects')->row_array(); if(!$es)return false;
        $exam=$this->exam($es['exam_id']);
        if(!$exam)return false;
        $this->db->where(['class_id'=>$es['class_id'],'section_id'=>(int)$sectionId,'subject_id'=>$es['subject_id'],'session_id'=>$exam['session_id'],'branch_id'=>$this->branchId()]);
        if(is_teacher_loggedin())$this->db->where('teacher_id',get_loggedin_user_id());
        return $this->db->count_all_results('subject_assign')>0;
    }

    public function marksFor($examSubjectId,$component,$enrollIds=[])
    {
        $this->db->where(['exam_subject_id'=>(int)$examSubjectId,'component'=>$component]);
        if($enrollIds)$this->db->where_in('enroll_id',$enrollIds);
        $rows=$this->db->get('weighted_exam_marks')->result_array();$out=[];foreach($rows as $r)$out[$r['enroll_id']]=$r;return $out;
    }

    public function schedules($examId,$classId,$sectionId)
    {
        if(!$this->exam($examId))return [];
        $rows=$this->db->select('ws.*')->from('weighted_exam_schedule ws')->join('weighted_exam_subjects wes','wes.id=ws.exam_subject_id')->where(['ws.exam_id'=>(int)$examId,'wes.class_id'=>(int)$classId,'ws.section_id'=>(int)$sectionId])->get()->result_array();
        $out=[];foreach($rows as $row)$out[$row['exam_subject_id']][$row['component']]=$row;return $out;
    }

    public function saveSchedule($examId,$classId,$sectionId,array $rows)
    {
        $exam=$this->exam($examId);if(!$exam||in_array($exam['status'],['finalized','published'],true))throw new RuntimeException('This examination schedule is locked.');
        if(!$this->db->where(['exam_id'=>(int)$examId,'class_id'=>(int)$classId,'section_id'=>(int)$sectionId])->count_all_results('weighted_exam_classes'))throw new RuntimeException('This class and section are not assigned to the examination.');
        $subjects=$this->configuredSubjects($examId,$classId);$allowed=[];foreach($subjects as $subject)$allowed[$subject['id']]=$subject;
        $this->db->trans_start();
        foreach($rows as $examSubjectId=>$components){$examSubjectId=(int)$examSubjectId;if(!isset($allowed[$examSubjectId]))continue;foreach($components as $component=>$row){if(!in_array($component,$this->components,true)||empty($allowed[$examSubjectId]['components'][$component]['enabled']))continue;$date=trim((string)$row['exam_date']);$start=trim((string)$row['start_time']);$end=trim((string)$row['end_time']);$parsedDate=$date!==''?DateTime::createFromFormat('!Y-m-d',$date):null;if($date!==''&&(!$parsedDate||$parsedDate->format('Y-m-d')!==$date)){$this->db->trans_rollback();throw new RuntimeException('Invalid schedule date.');}if(($start!==''&&!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/',$start))||($end!==''&&!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/',$end))){$this->db->trans_rollback();throw new RuntimeException('Invalid schedule time.');}if(($start===''&&$end!=='')||($start!==''&&$end==='')){$this->db->trans_rollback();throw new RuntimeException('Both start and end time are required.');}if($start!==''&&$end!==''&&$end<=$start){$this->db->trans_rollback();throw new RuntimeException('Schedule end time must be after start time.');}$where=['exam_id'=>(int)$examId,'exam_subject_id'=>$examSubjectId,'section_id'=>(int)$sectionId,'component'=>$component];if($date===''){$this->db->where($where)->delete('weighted_exam_schedule');continue;}$old=$this->db->where($where)->get('weighted_exam_schedule')->row_array();if($start!==''&&$end!==''){$this->db->from('weighted_exam_schedule')->where(['exam_id'=>(int)$examId,'section_id'=>(int)$sectionId,'exam_date'=>$date])->where('start_time <',$end)->where('end_time >',$start);if($old)$this->db->where('id !=',$old['id']);if($this->db->count_all_results()>0){$this->db->trans_rollback();throw new RuntimeException('Schedule conflict: this section already has another examination during the selected time.');}}$data=$where+['exam_date'=>$date,'start_time'=>$start?:null,'end_time'=>$end?:null,'room_no'=>trim((string)$row['room_no']),'instructions'=>trim((string)$row['instructions']),'updated_by'=>get_loggedin_user_id()];if($old)$this->db->where('id',$old['id'])->update('weighted_exam_schedule',$data);else$this->db->insert('weighted_exam_schedule',$data);}}
        $this->db->trans_complete();if(!$this->db->trans_status())throw new RuntimeException('Unable to save examination schedule.');
    }

    public function saveMarks($examId,$examSubjectId,$sectionId,$component,array $marks)
    {
        $exam=$this->exam($examId); if(!$exam||in_array($exam['status'],['finalized','published'],true))throw new RuntimeException('Marks are locked for this examination.');
        if(!in_array($component,$this->components,true))throw new RuntimeException('Invalid component.');
        if(!$this->canTeach($examSubjectId,$sectionId))throw new RuntimeException('You are not assigned to this class and subject.');
        $es=$this->db->where(['id'=>(int)$examSubjectId,'exam_id'=>(int)$examId])->get('weighted_exam_subjects')->row_array();
        if(!$es||!$this->db->where(['exam_id'=>(int)$examId,'class_id'=>$es['class_id'],'section_id'=>(int)$sectionId])->count_all_results('weighted_exam_classes'))throw new RuntimeException('The selected subject/class/section is not assigned to this examination.');
        $cfg=$this->db->where(['exam_subject_id'=>(int)$examSubjectId,'component'=>$component,'enabled'=>1])->get('weighted_exam_subject_components')->row_array();
        if(!$es||!$cfg)throw new RuntimeException('This component is not applicable.');
        $students=$this->students($examId,$es['class_id'],$sectionId);$allowed=array_map(function($r){return(int)$r['enroll_id'];},$students);
        $this->db->trans_start();
        foreach($marks as $enrollId=>$row){$enrollId=(int)$enrollId;if(!in_array($enrollId,$allowed,true))continue;$status=isset($row['status'])?$row['status']:'not_entered';if(!in_array($status,['present','absent','exempt','not_entered'],true))$status='not_entered';$value=($status==='present'&&$row['obtained_marks']!=='')?(float)$row['obtained_marks']:null;if($status==='present'&&($value<0||$value>(float)$cfg['full_marks']))throw new RuntimeException('Marks must be between 0 and '.$cfg['full_marks'].'.');
            $where=['exam_id'=>(int)$examId,'exam_subject_id'=>(int)$examSubjectId,'enroll_id'=>$enrollId,'component'=>$component];$old=$this->db->where($where)->get('weighted_exam_marks')->row_array();$data=$where+['obtained_marks'=>$value,'status'=>$status,'updated_by'=>get_loggedin_user_id()];
            if($old){$changed=(string)$old['obtained_marks']!==(string)$value||$old['status']!==$status;if($changed&&$this->db->where(['exam_id'=>(int)$examId,'enroll_id'=>$enrollId,'component'=>$component,'publication_status'=>'published'])->count_all_results('weighted_exam_individual_results'))throw new RuntimeException('Unpublish the affected individual result before correcting its marks.');if($changed)$this->db->insert('weighted_exam_mark_audit',['mark_id'=>$old['id']]+$where+['old_marks'=>$old['obtained_marks'],'new_marks'=>$value,'old_status'=>$old['status'],'new_status'=>$status,'changed_by'=>get_loggedin_user_id()]);$this->db->where('id',$old['id'])->update('weighted_exam_marks',$data);}else{$data['entered_by']=get_loggedin_user_id();$this->db->insert('weighted_exam_marks',$data);}
        }
        $this->db->trans_complete();if(!$this->db->trans_status())throw new RuntimeException('Unable to save marks.');
    }

    public function calculateEnrollment($examId,$enrollId)
    {
        $exam=$this->exam($examId);if(!$exam)return null;$enroll=$this->db->where(['id'=>(int)$enrollId,'branch_id'=>$this->branchId(),'session_id'=>$exam['session_id']])->get('enroll')->row_array();if(!$enroll)return null;
        $subjects=$this->configuredSubjects($examId,$enroll['class_id']);$calculated=[];
        foreach($subjects as $s){$rows=$this->db->where(['exam_subject_id'=>$s['id'],'enroll_id'=>(int)$enrollId])->get('weighted_exam_marks')->result_array();$marks=[];foreach($rows as $m)$marks[$m['component']]=$m;$result=$this->weighted_exam_calculator->subject($s['components'],$marks,$exam['absence_policy']);$result['passed']=$this->weighted_exam_calculator->passes($result['score'],$s['pass_marks']);$calculated[]=['id'=>$s['id'],'subject_id'=>$s['subject_id'],'name'=>$s['subject_name'],'include_in_percentage'=>$s['include_in_percentage'],'pass_marks'=>$s['pass_marks'],'config'=>$s['components'],'marks'=>$marks,'result'=>$result];}
        return ['exam'=>$exam,'enroll'=>$enroll,'subjects'=>$calculated,'overall'=>$this->weighted_exam_calculator->overall($calculated)];
    }

    public function gradeFor($percentage)
    {
        if($percentage===null)return null;return $this->db->where('branch_id',$this->branchId())->where('minimum_percentage <=',(float)$percentage)->where('maximum_percentage >=',(float)$percentage)->order_by('sort_order')->get('weighted_exam_grade_rules')->row_array();
    }

    public function workingDays($classId,$sectionId,$sessionId)
    {
        $row=$this->db->select('COUNT(DISTINCT sa.date) total',false)->from('student_attendance sa')->join('enroll e','e.id=sa.enroll_id')->where(['e.branch_id'=>$this->branchId(),'e.session_id'=>(int)$sessionId,'e.class_id'=>(int)$classId,'e.section_id'=>(int)$sectionId])->get()->row_array();
        return (int)$row['total'];
    }

    public function process($examId,$classId,$sectionId,$finalize=false)
    {
        $exam=$this->exam($examId);if(!$exam)throw new RuntimeException('Examination not found.');if($exam['status']==='published')throw new RuntimeException('Published results must be reopened first.');
        $students=$this->students($examId,$classId,$sectionId);$workingDays=$this->workingDays($classId,$sectionId,$exam['session_id']);$output=[];$this->db->trans_start();
        foreach($students as $student){$calc=$this->calculateEnrollment($examId,$student['enroll_id']);$grade=$this->gradeFor($calc['overall']['percentage']);$complete=$calc['overall']['complete'];$subjectFail=false;foreach($calc['subjects'] as $subject)if($subject['result']['passed']===false)$subjectFail=true;$status=$complete&&$grade?(($grade['is_pass']&&!$subjectFail)?'pass':'fail'):'incomplete';$calc['student']=$student;$calc['grade']=$grade;$calc['subject_fail']=$subjectFail;$calc['result_status']=$status;$output[]=$calc;if(!$finalize)continue;
            if(!$complete){$this->db->trans_rollback();throw new RuntimeException('Cannot finalize: '.$student['student_name'].' has incomplete marks.');}
            if(!$grade){$this->db->trans_rollback();throw new RuntimeException('Cannot finalize: no grade rule matches '.$student['student_name'].'.');}
            $data=['exam_id'=>(int)$examId,'enroll_id'=>$student['enroll_id'],'overall_percentage'=>$calc['overall']['percentage'],'grade_rule_id'=>$grade?$grade['id']:null,'result_label'=>$subjectFail?'Fail':($grade?$grade['name']:null),'result_status'=>$status,'working_days'=>$workingDays,'snapshot'=>json_encode($calc),'processed_by'=>get_loggedin_user_id()];$old=$this->db->where(['exam_id'=>(int)$examId,'enroll_id'=>$student['enroll_id']])->get('weighted_exam_results')->row_array();if($old){$this->db->where('id',$old['id'])->update('weighted_exam_results',$data);$rid=$old['id'];$this->db->where('result_id',$rid)->delete('weighted_exam_subject_results');}else{$this->db->insert('weighted_exam_results',$data);$rid=$this->db->insert_id();}foreach($calc['subjects'] as $s)$this->db->insert('weighted_exam_subject_results',['result_id'=>$rid,'exam_subject_id'=>$s['id'],'subject_score'=>$s['result']['score'],'calculation'=>json_encode($s['result']),'is_complete'=>$s['result']['complete']?1:0]);
        }
        if($finalize){$ranked=[];foreach($output as $i=>$r)$ranked[]=['i'=>$i,'p'=>$r['overall']['percentage']];usort($ranked,function($a,$b){return$b['p']<=>$a['p'];});$position=0;$last=null;$seen=0;foreach($ranked as $r){$seen++;if($last===null||abs($last-$r['p'])>0.000001)$position=$seen;$last=$r['p'];$output[$r['i']]['class_position']=$position;$this->db->where(['exam_id'=>(int)$examId,'enroll_id'=>$output[$r['i']]['student']['enroll_id']])->update('weighted_exam_results',['class_position'=>$exam['ranking_enabled']?$position:null]);}$this->db->where(['id'=>(int)$examId,'branch_id'=>$this->branchId()])->update('weighted_exams',['status'=>'finalized']);}
        $this->db->trans_complete();if(!$this->db->trans_status())throw new RuntimeException('Result processing failed.');return $output;
    }

    public function canAccessClassSection($examId,$classId,$sectionId)
    {
        $exam=$this->exam($examId);if(!$exam)return false;
        if(!$this->db->where(['exam_id'=>(int)$examId,'class_id'=>(int)$classId,'section_id'=>(int)$sectionId])->count_all_results('weighted_exam_classes'))return false;
        if(!is_teacher_loggedin())return true;
        return $this->db->where(['branch_id'=>$this->branchId(),'session_id'=>$exam['session_id'],'class_id'=>(int)$classId,'section_id'=>(int)$sectionId,'teacher_id'=>get_loggedin_user_id()])->count_all_results('subject_assign')>0;
    }

    public function calculateIndividualEnrollment($examId,$enrollId,$component)
    {
        if(!in_array($component,$this->components,true))throw new RuntimeException('Invalid examination type.');
        $exam=$this->exam($examId);if(!$exam)throw new RuntimeException('Examination not found.');
        $enroll=$this->db->where(['id'=>(int)$enrollId,'branch_id'=>$this->branchId(),'session_id'=>$exam['session_id']])->get('enroll')->row_array();
        if(!$enroll||!$this->canAccessClassSection($examId,$enroll['class_id'],$enroll['section_id']))throw new RuntimeException('Student is not assigned to this examination or is outside your access.');
        $subjects=[];$latestSource=null;
        foreach($this->configuredSubjects($examId,$enroll['class_id']) as $subject){
            $config=isset($subject['components'][$component])?$subject['components'][$component]:['enabled'=>0];
            if(empty($config['enabled']))continue;
            $mark=$this->db->where(['exam_id'=>(int)$examId,'exam_subject_id'=>$subject['id'],'enroll_id'=>(int)$enrollId,'component'=>$component])->get('weighted_exam_marks')->row_array();
            if($mark&&!empty($mark['updated_at'])&&($latestSource===null||$mark['updated_at']>$latestSource))$latestSource=$mark['updated_at'];
            $result=$this->weighted_exam_calculator->individualSubject($config,$mark?:null,$subject['pass_marks'],$exam['absence_policy']);
            $result['grade']=$this->gradeFor($result['percentage']);
            $subjects[]=['id'=>$subject['id'],'subject_id'=>$subject['subject_id'],'name'=>$subject['subject_name'],'include_in_percentage'=>$subject['include_in_percentage'],'pass_percentage'=>$subject['pass_marks'],'config'=>$config,'mark'=>$mark,'result'=>$result];
        }
        if(!$subjects)throw new RuntimeException('No subjects participate in the selected examination type.');
        $overall=$this->weighted_exam_calculator->individualOverall($subjects);$grade=$this->gradeFor($overall['percentage']);$subjectFail=false;
        foreach($subjects as $subject)if($subject['result']['passed']===false)$subjectFail=true;
        $status=$overall['complete']&&$grade?(($grade['is_pass']&&!$subjectFail)?'pass':'fail'):'incomplete';
        return ['exam'=>$exam,'enroll'=>$enroll,'component'=>$component,'component_name'=>$this->componentName($component),'subjects'=>$subjects,'overall'=>$overall,'grade'=>$grade,'subject_fail'=>$subjectFail,'result_status'=>$status,'source_updated_at'=>$latestSource];
    }

    public function componentName($component)
    {
        $names=['unit1'=>'Unit 1 Result','unit2'=>'Unit 2 Result','half_yearly'=>'Half-Yearly Result','annual'=>'Annual Examination Result'];
        return isset($names[$component])?$names[$component]:'';
    }

    public function processIndividual($examId,$classId,$sectionId,$component,$enrollId=0)
    {
        if(!in_array($component,$this->components,true))throw new RuntimeException('Invalid examination type.');
        if(!$this->canAccessClassSection($examId,$classId,$sectionId))throw new RuntimeException('This class and section are not assigned to the examination or are outside your access.');
        $exam=$this->exam($examId);$students=$this->students($examId,$classId,$sectionId);if($enrollId)$students=array_values(array_filter($students,function($s)use($enrollId){return(int)$s['enroll_id']===(int)$enrollId;}));
        if(!$students)throw new RuntimeException('No matching students were found.');
        $workingDays=$this->workingDays($classId,$sectionId,$exam['session_id']);$processed=[];$incomplete=[];$this->db->trans_begin();
        try{foreach($students as $student){
            $calc=$this->calculateIndividualEnrollment($examId,$student['enroll_id'],$component);$calc['student']=$student;
            if(!$calc['overall']['complete']){$incomplete[]=['student'=>$student,'errors'=>$calc['overall']['errors']];continue;}
            if(!$calc['grade']){$incomplete[]=['student'=>$student,'errors'=>[['subject'=>'Overall','status'=>'no_grade','message'=>'No grade rule matches '.$calc['overall']['display_percentage'].'%']]];continue;}
            $where=['exam_id'=>(int)$examId,'enroll_id'=>(int)$student['enroll_id'],'component'=>$component];$old=$this->db->where($where)->get('weighted_exam_individual_results')->row_array();
            if($old&&$old['publication_status']==='published'){$incomplete[]=['student'=>$student,'errors'=>[['subject'=>'Result','status'=>'published','message'=>'Unpublish this result before reprocessing it']]];continue;}
            $data=$where+['total_full_marks'=>$calc['overall']['total_full'],'total_obtained_marks'=>$calc['overall']['total_obtained'],'overall_percentage'=>$calc['overall']['percentage'],'grade_rule_id'=>$calc['grade']['id'],'result_label'=>$calc['subject_fail']?'Fail':$calc['grade']['name'],'result_status'=>$calc['result_status'],'publication_status'=>'draft','working_days'=>$workingDays,'snapshot'=>json_encode($calc),'processed_by'=>get_loggedin_user_id(),'processed_at'=>date('Y-m-d H:i:s'),'published_by'=>null,'published_at'=>null];
            if($old){$this->db->where('id',$old['id'])->update('weighted_exam_individual_results',$data);$id=$old['id'];}else{$this->db->insert('weighted_exam_individual_results',$data);$id=$this->db->insert_id();}
            $processed[]=['id'=>$id,'student'=>$student,'calculation'=>$calc];
        }}catch(Exception $e){$this->db->trans_rollback();throw $e;}
        if($this->db->trans_status()===false){$this->db->trans_rollback();throw new RuntimeException('Individual result processing failed.');}
        $this->db->trans_commit();$this->rankIndividual($examId,$classId,$sectionId,$component);
        return ['processed'=>$processed,'incomplete'=>$incomplete];
    }

    private function rankIndividual($examId,$classId,$sectionId,$component)
    {
        $exam=$this->exam($examId);if(!$exam||empty($exam['ranking_enabled']))return;
        $rows=$this->db->select('r.id,r.overall_percentage')->from('weighted_exam_individual_results r')->join('enroll e','e.id=r.enroll_id')->where(['r.exam_id'=>(int)$examId,'r.component'=>$component,'e.class_id'=>(int)$classId,'e.section_id'=>(int)$sectionId,'e.session_id'=>$exam['session_id'],'e.branch_id'=>$this->branchId()])->order_by('r.overall_percentage','DESC')->order_by('r.id')->get()->result_array();
        $position=0;$seen=0;$last=null;foreach($rows as $row){$seen++;$value=(float)$row['overall_percentage'];if($last===null||abs($last-$value)>0.000001)$position=$seen;$last=$value;$this->db->where('id',$row['id'])->update('weighted_exam_individual_results',['class_position'=>$position]);}
    }

    public function individualResult($examId,$enrollId,$component)
    {
        $row=$this->db->select('r.*,e.class_id access_class_id,e.section_id access_section_id')->from('weighted_exam_individual_results r')->join('weighted_exams we','we.id=r.exam_id')->join('enroll e','e.id=r.enroll_id')->where(['r.exam_id'=>(int)$examId,'r.enroll_id'=>(int)$enrollId,'r.component'=>$component,'we.branch_id'=>$this->branchId(),'e.branch_id'=>$this->branchId()])->get()->row_array();
        if(!$row||!$this->canAccessClassSection($examId,$row['access_class_id'],$row['access_section_id']))return null;unset($row['access_class_id'],$row['access_section_id']);$row['data']=json_decode($row['snapshot'],true);return $row;
    }

    public function individualRows($examId,$classId,$sectionId,$component,$enrollId=0)
    {
        if(!$this->canAccessClassSection($examId,$classId,$sectionId))return [];$rows=[];
        foreach($this->students($examId,$classId,$sectionId) as $student){if($enrollId&&(int)$student['enroll_id']!==(int)$enrollId)continue;$result=$this->individualResult($examId,$student['enroll_id'],$component);$rows[]=['student'=>$student,'result'=>$result];}
        return $rows;
    }

    public function setIndividualPublication($examId,$classId,$sectionId,$component,$publish,$enrollId=0)
    {
        if(!$this->canAccessClassSection($examId,$classId,$sectionId))throw new RuntimeException('This selection is outside your access.');
        $rows=$this->individualRows($examId,$classId,$sectionId,$component,$enrollId);$changed=0;$this->db->trans_start();
        foreach($rows as $row){$result=$row['result'];if(!$result)continue;$new=$publish?'published':'draft';if($result['publication_status']===$new)continue;$this->db->where('id',$result['id'])->update('weighted_exam_individual_results',['publication_status'=>$new,'published_by'=>$publish?get_loggedin_user_id():null,'published_at'=>$publish?date('Y-m-d H:i:s'):null]);$this->db->insert('weighted_exam_individual_result_audit',['result_id'=>$result['id'],'old_status'=>$result['publication_status'],'new_status'=>$new,'changed_by'=>get_loggedin_user_id()]);$changed++;}
        $this->db->trans_complete();if(!$this->db->trans_status())throw new RuntimeException('Unable to update publication status.');return $changed;
    }

    public function result($examId,$enrollId)
    {
        $row=$this->db->select('wer.*')->from('weighted_exam_results wer')->join('weighted_exams we','we.id=wer.exam_id')->where(['wer.exam_id'=>(int)$examId,'wer.enroll_id'=>(int)$enrollId,'we.branch_id'=>$this->branchId()])->get()->row_array();if(!$row)return null;$row['data']=json_decode($row['snapshot'],true);return $row;
    }
}
