<?php defined('BASEPATH') or exit('No direct script access allowed');

class Weighted_exam extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('weighted_exam_model');
        $this->load->library('weighted_exam_calculator');
        $this->data['main_menu'] = 'weighted_exam';
    }

    private function allow($prefix, $action = 'is_view')
    {
        if (!get_permission($prefix, $action)) access_denied();
    }

    private function baseData($subPage, $title)
    {
        $this->data['sub_page'] = $subPage; $this->data['title'] = $title;
        $this->data['branch_id'] = $this->weighted_exam_model->branchId();
        $this->data['branches'] = $this->db->where('status',1)->order_by('name')->get('branch')->result_array();
        $this->data['sessions'] = $this->db->order_by('id','DESC')->get('schoolyear')->result_array();
        $this->data['exams'] = $this->weighted_exam_model->exams();
        $this->data['classes'] = $this->weighted_exam_model->classes();
        $this->data['sections'] = $this->weighted_exam_model->sections();
    }

    public function index()
    {
        $this->allow('weighted_exam_setup');
        if ($this->input->post('save')) {
            $this->allow('weighted_exam_setup', empty($this->input->post('id')) ? 'is_add' : 'is_edit');
            $this->form_validation->set_rules('name','Examination Name','trim|required|max_length[255]');
            $this->form_validation->set_rules('session_id','Academic Session','required|integer');
            $this->form_validation->set_rules('class_ids[]','Classes','required');
            if ($this->form_validation->run()) {
                try { $this->weighted_exam_model->saveExam($this->input->post(null,true)); set_alert('success','Weighted examination saved successfully.'); redirect('weighted_exam?branch_id='.$this->weighted_exam_model->branchId()); }
                catch (Exception $e) { set_alert('error',$e->getMessage()); }
            }
        }
        $this->baseData('weighted_exam/setup','Weighted Exam Setup');
        $this->data['edit_exam'] = null;
        if ($this->input->get('edit')) $this->data['edit_exam'] = $this->weighted_exam_model->exam((int)$this->input->get('edit'));
        $this->load->view('layout/index',$this->data);
    }

    public function delete($id)
    {
        $this->allow('weighted_exam_setup','is_delete');if(!$this->input->post('confirm'))show_error('Invalid request.',400);$exam=$this->weighted_exam_model->exam($id);
        if ($exam && $exam['status']==='draft') {$this->db->where(['id'=>(int)$id,'branch_id'=>$this->weighted_exam_model->branchId()])->delete('weighted_exams');set_alert('success','Draft examination deleted.');}
        else set_alert('error','Only draft examinations can be deleted.');redirect('weighted_exam?branch_id='.$this->weighted_exam_model->branchId());
    }

    public function status($id,$status)
    {
        $this->allow('weighted_exam_setup','is_edit');if(!$this->input->post('confirm'))show_error('Invalid request.',400);$exam=$this->weighted_exam_model->exam($id);
        $allowed=['draft','active','published'];
        if($exam&&in_array($status,$allowed,true)){
            if($status==='published'&&$exam['status']!=='finalized')set_alert('error','Finalize results before publication.');
            else{$this->db->where(['id'=>(int)$id,'branch_id'=>$this->weighted_exam_model->branchId()])->update('weighted_exams',['status'=>$status]);set_alert('success','Examination status updated.');}
        }redirect('weighted_exam?branch_id='.$this->weighted_exam_model->branchId());
    }

    public function subjects()
    {
        $this->allow('weighted_exam_subjects');$examId=(int)$this->input->post_get('exam_id');$classId=(int)$this->input->post_get('class_id');$sectionId=(int)$this->input->post_get('section_id');
        if($this->input->post('save_subjects')){$this->allow('weighted_exam_subjects','is_edit');try{$this->weighted_exam_model->saveSubjects($examId,$classId,(array)$this->input->post('subjects'));set_alert('success','Subject configuration saved.');redirect('weighted_exam/subjects?'.http_build_query(['branch_id'=>$this->weighted_exam_model->branchId(),'exam_id'=>$examId,'class_id'=>$classId,'section_id'=>$sectionId]));}catch(Exception $e){set_alert('error',$e->getMessage());}}
        $this->baseData('weighted_exam/subjects','Weighted Exam Subjects');$this->data += ['exam_id'=>$examId,'class_id'=>$classId,'section_id'=>$sectionId,'available_subjects'=>($examId&&$classId)?$this->weighted_exam_model->assignedSubjects($examId,$classId,$sectionId):[],'configured_subjects'=>($examId&&$classId)?$this->weighted_exam_model->configuredSubjects($examId,$classId):[]];$this->load->view('layout/index',$this->data);
    }

    public function schedule()
    {
        $this->allow('weighted_exam_schedule');$examId=(int)$this->input->post_get('exam_id');$classId=(int)$this->input->post_get('class_id');$sectionId=(int)$this->input->post_get('section_id');
        if($this->input->post('save_schedule')){$this->allow('weighted_exam_schedule','is_edit');try{$this->weighted_exam_model->saveSchedule($examId,$classId,$sectionId,(array)$this->input->post('schedule'));set_alert('success','Examination schedule saved successfully.');redirect('weighted_exam/schedule?'.http_build_query(['branch_id'=>$this->weighted_exam_model->branchId(),'exam_id'=>$examId,'class_id'=>$classId,'section_id'=>$sectionId]));}catch(Exception $e){set_alert('error',$e->getMessage());}}
        $subjects=($examId&&$classId)?$this->weighted_exam_model->configuredSubjects($examId,$classId):[];$schedule=($examId&&$classId&&$sectionId)?$this->weighted_exam_model->schedules($examId,$classId,$sectionId):[];
        $this->baseData('weighted_exam/schedule','Weighted Exam Schedule');$this->data += compact('examId','classId','sectionId','subjects','schedule');$this->load->view('layout/index',$this->data);
    }

    public function marks()
    {
        $this->allow('weighted_exam_marks');$sessionId=(int)$this->input->post_get('session_id');$examId=(int)$this->input->post_get('exam_id');$classId=(int)$this->input->post_get('class_id');$sectionId=(int)$this->input->post_get('section_id');$examSubjectId=(int)$this->input->post_get('exam_subject_id');$component=(string)$this->input->post_get('component');
        if($this->input->post('save_marks')){$this->allow('weighted_exam_marks','is_add');try{$this->weighted_exam_model->saveMarks($examId,$examSubjectId,$sectionId,$component,(array)$this->input->post('marks'));set_alert('success','Marks saved successfully.');$branch_id=$this->weighted_exam_model->branchId();redirect('weighted_exam/marks?'.http_build_query(compact('branch_id','sessionId','examId','classId','sectionId','examSubjectId','component')));}catch(Exception $e){set_alert('error',$e->getMessage());}}
        // Accept both friendly query keys and redirect keys.
        $sessionId=$sessionId?:((int)$this->input->get('sessionId'));$examId=$examId?:((int)$this->input->get('examId'));$classId=$classId?:((int)$this->input->get('classId'));$sectionId=$sectionId?:((int)$this->input->get('sectionId'));$examSubjectId=$examSubjectId?:((int)$this->input->get('examSubjectId'));$component=$component?:((string)$this->input->get('component'));
        $exam=$examId?$this->weighted_exam_model->exam($examId):null;if(!$sessionId&&$exam)$sessionId=(int)$exam['session_id'];
        $subjects=($examId&&$classId&&$sectionId)?$this->weighted_exam_model->markEntrySubjects($examId,$classId,$sectionId):[];$validSubject=false;$validComponent=false;foreach($subjects as $subject)if((int)$subject['id']===$examSubjectId){$validSubject=true;$validComponent=!empty($subject['components'][$component]['enabled']);break;}$students=($examId&&$classId&&$sectionId&&$validSubject&&$validComponent)?$this->weighted_exam_model->students($examId,$classId,$sectionId):[];$ids=array_map(function($r){return$r['enroll_id'];},$students);$marks=$ids?$this->weighted_exam_model->marksFor($examSubjectId,$component,$ids):[];
        $this->baseData('weighted_exam/marks','Weighted Marks Entry');$this->data += compact('sessionId','examId','classId','sectionId','examSubjectId','component','subjects','students','marks');$this->load->view('layout/index',$this->data);
    }

    public function subjects_json()
    {
        $this->allow('weighted_exam_marks');$examId=(int)$this->input->get('exam_id');$classId=(int)$this->input->get('class_id');$sectionId=(int)$this->input->get('section_id');
        try{$subjects=$this->weighted_exam_model->markEntrySubjects($examId,$classId,$sectionId);$configured=$this->weighted_exam_model->configuredSubjects($examId,$classId);$configs=[];foreach($configured as $subject)$configs[$subject['id']]=$subject['components'];$data=[];foreach($subjects as $subject){$components=[];foreach(['unit1','unit2','half_yearly','annual'] as $key)if(!empty($configs[$subject['id']][$key]['enabled']))$components[]=['id'=>$key,'label'=>ucwords(str_replace('_',' ',$key)),'full_marks'=>$configs[$subject['id']][$key]['full_marks']];$data[]=['id'=>$subject['id'],'subject_id'=>$subject['subject_id'],'name'=>$subject['subject_name'],'components'=>$components];}$this->output->set_content_type('application/json')->set_output(json_encode(['status'=>'success','subjects'=>$data,'message'=>$data?'':'No subjects are configured for the selected examination, class and section.']));}
        catch(Exception $e){$this->output->set_status_header(422)->set_content_type('application/json')->set_output(json_encode(['status'=>'error','subjects'=>[],'message'=>$e->getMessage()]));}
    }

    public function overview()
    {
        $this->allow('weighted_exam_overview');$examId=(int)$this->input->post_get('exam_id');$classId=(int)$this->input->post_get('class_id');$sectionId=(int)$this->input->post_get('section_id');$rows=[];
        if($examId&&$classId&&$sectionId)foreach($this->weighted_exam_model->students($examId,$classId,$sectionId) as $student){$calc=$this->weighted_exam_model->calculateEnrollment($examId,$student['enroll_id']);$calc['student']=$student;$rows[]=$calc;}
        $this->baseData('weighted_exam/overview','Weighted Marks Overview');$this->data += compact('examId','classId','sectionId','rows');$this->load->view('layout/index',$this->data);
    }

    public function processing()
    {
        $this->allow('weighted_exam_processing');$examId=(int)$this->input->post_get('exam_id');$classId=(int)$this->input->post_get('class_id');$sectionId=(int)$this->input->post_get('section_id');$rows=[];
        if($this->input->post('process')&&$examId&&$classId&&$sectionId){try{$finalize=$this->input->post('process')==='finalize';if($finalize)$this->allow('weighted_exam_processing','is_add');$rows=$this->weighted_exam_model->process($examId,$classId,$sectionId,$finalize);set_alert('success',$finalize?'Results finalized successfully.':'Result preview calculated.');}catch(Exception $e){set_alert('error',$e->getMessage());}}
        $this->baseData('weighted_exam/processing','Weighted Result Processing');$this->data += compact('examId','classId','sectionId','rows');$this->load->view('layout/index',$this->data);
    }

    public function marksheet()
    {
        $this->allow('weighted_exam_marksheet');$examId=(int)$this->input->post_get('exam_id');$classId=(int)$this->input->post_get('class_id');$sectionId=(int)$this->input->post_get('section_id');$students=($examId&&$classId&&$sectionId)?$this->weighted_exam_model->students($examId,$classId,$sectionId):[];
        $this->baseData('weighted_exam/marksheet','Generate Weighted Marksheet');$this->data += compact('examId','classId','sectionId','students');$this->load->view('layout/index',$this->data);
    }

    public function class_marksheets($examId,$classId,$sectionId,$pdf=0)
    {
        $this->allow('weighted_exam_marksheet');$students=$this->weighted_exam_model->students($examId,$classId,$sectionId);$documents=[];
        foreach($students as $s){$result=$this->weighted_exam_model->result($examId,$s['enroll_id']);if(!$result)continue;$enroll=$this->studentRecord($s['enroll_id']);if(!$enroll)continue;$data=['result'=>$result,'record'=>$result['data'],'student'=>$enroll,'pdf'=>(bool)$pdf];$documents[]=['cover'=>$this->load->view('weighted_exam/marksheet_cover',$data,true),'marks'=>$this->load->view('weighted_exam/marksheet_print',$data,true)];}
        if(!$documents)show_error('No finalized marksheets are available for this selection.',409);
        if($pdf){$this->load->library('html2pdf',['format'=>'A4-L','margin_left'=>6,'margin_right'=>6,'margin_top'=>6,'margin_bottom'=>6]);$this->html2pdf->mpdf->autoScriptToLang=true;$this->html2pdf->mpdf->autoLangToFont=true;foreach($documents as $i=>$document){if($i)$this->html2pdf->mpdf->AddPage('L','','','','',6,6,6,6);$this->html2pdf->mpdf->WriteHTML($document['cover']);$this->html2pdf->mpdf->AddPage('L','','','','',8,8,8,8);$this->html2pdf->mpdf->WriteHTML($document['marks']);}$filename='weighted-class-marksheets.pdf';$this->output->set_content_type('application/pdf')->set_header('Content-Disposition: attachment; filename="'.$filename.'"')->set_output($this->html2pdf->mpdf->Output($filename,'S'));return;}
        echo $this->load->view('weighted_exam/marksheet_document',['documents'=>$documents,'autoPrint'=>(bool)$this->input->get('print')],true);
    }

    public function preview($examId,$enrollId,$pdf=0)
    {
        $this->allow('weighted_exam_marksheet');$result=$this->weighted_exam_model->result($examId,$enrollId);if(!$result)show_error('Process and finalize this result before generating a marksheet.',409);
        $enroll=$this->studentRecord($enrollId);if(!$enroll)show_404();
        $data=['result'=>$result,'record'=>$result['data'],'student'=>$enroll,'pdf'=>(bool)$pdf];$cover=$this->load->view('weighted_exam/marksheet_cover',$data,true);$marks=$this->load->view('weighted_exam/marksheet_print',$data,true);
        if($pdf){$filename='weighted-marksheet-'.$enroll['register_no'].'.pdf';$this->load->library('html2pdf',['format'=>'A4-L','margin_left'=>6,'margin_right'=>6,'margin_top'=>6,'margin_bottom'=>6]);$this->html2pdf->mpdf->autoScriptToLang=true;$this->html2pdf->mpdf->autoLangToFont=true;$this->html2pdf->mpdf->WriteHTML($cover);$this->html2pdf->mpdf->AddPage('L','','','','',8,8,8,8);$this->html2pdf->mpdf->WriteHTML($marks);$this->html2pdf->mpdf->SetDisplayMode('fullpage');$this->output->set_content_type('application/pdf')->set_header('Content-Disposition: attachment; filename="'.$filename.'"')->set_output($this->html2pdf->mpdf->Output($filename,'S'));return;}
        echo $this->load->view('weighted_exam/marksheet_document',['documents'=>[['cover'=>$cover,'marks'=>$marks]],'autoPrint'=>(bool)$this->input->get('print')],true);
    }

    public function individual_results()
    {
        $this->allow('weighted_exam_individual_results');
        $sessionId=(int)$this->input->post_get('session_id');$examId=(int)$this->input->post_get('exam_id');$classId=(int)$this->input->post_get('class_id');$sectionId=(int)$this->input->post_get('section_id');$component=(string)$this->input->post_get('component');$enrollId=(int)$this->input->post_get('enroll_id');
        if($component==='final_weighted'&&$examId&&$classId&&$sectionId)redirect('weighted_exam/marksheet?'.http_build_query(['branch_id'=>$this->weighted_exam_model->branchId(),'exam_id'=>$examId,'class_id'=>$classId,'section_id'=>$sectionId]));
        if($this->input->post('action')){
            $action=(string)$this->input->post('action');$this->allow('weighted_exam_individual_results',in_array($action,['publish_one','publish_all','unpublish_one','unpublish_all'],true)?'is_edit':'is_add');
            try{
                if($action==='process_one'||$action==='process_all'){$out=$this->weighted_exam_model->processIndividual($examId,$classId,$sectionId,$component,$action==='process_one'?$enrollId:0);$message=count($out['processed']).' result(s) processed.';if($out['incomplete']){$this->session->set_flashdata('individual_result_errors',$out['incomplete']);$message.=' '.count($out['incomplete']).' student(s) require attention.';}set_alert($out['processed']?'success':'error',$message);}
                elseif(in_array($action,['publish_one','publish_all','unpublish_one','unpublish_all'],true)){$publish=strpos($action,'unpublish_')!==0;$changed=$this->weighted_exam_model->setIndividualPublication($examId,$classId,$sectionId,$component,$publish,strpos($action,'_one')!==false?$enrollId:0);set_alert($changed?'success':'warning',$changed.' result(s) '.($publish?'published.':'returned to draft.'));}
            }catch(Exception $e){set_alert('error',$e->getMessage());}
            redirect('weighted_exam/individual_results?'.http_build_query(['branch_id'=>$this->weighted_exam_model->branchId(),'session_id'=>$sessionId,'exam_id'=>$examId,'class_id'=>$classId,'section_id'=>$sectionId,'component'=>$component,'enroll_id'=>$action==='process_one'||strpos($action,'_one')!==false?$enrollId:0]));
        }
        $exam=$examId?$this->weighted_exam_model->exam($examId):null;if(!$sessionId&&$exam)$sessionId=(int)$exam['session_id'];
        $authorized=$examId&&$classId&&$sectionId&&$this->weighted_exam_model->canAccessClassSection($examId,$classId,$sectionId);$students=$authorized?$this->weighted_exam_model->students($examId,$classId,$sectionId):[];$rows=($authorized&&in_array($component,['unit1','unit2','half_yearly','annual'],true))?$this->weighted_exam_model->individualRows($examId,$classId,$sectionId,$component,$enrollId):[];$applicableSubjects=[];if($authorized&&in_array($component,['unit1','unit2','half_yearly','annual'],true))foreach($this->weighted_exam_model->configuredSubjects($examId,$classId) as $subject)if(!empty($subject['components'][$component]['enabled']))$applicableSubjects[]=$subject;
        $this->baseData('weighted_exam/individual_results','Individual Exam Results');$this->data+=compact('sessionId','examId','classId','sectionId','component','enrollId','students','rows','applicableSubjects');$this->data['processing_errors']=$this->session->flashdata('individual_result_errors')?:[];$this->load->view('layout/index',$this->data);
    }

    public function individual_preview($examId,$component,$enrollId,$pdf=0)
    {
        $this->allow('weighted_exam_individual_results');if(!in_array($component,['unit1','unit2','half_yearly','annual'],true))show_error('Invalid examination type.',400);
        $result=$this->weighted_exam_model->individualResult($examId,$enrollId,$component);
        if(!$result){try{$calc=$this->weighted_exam_model->calculateIndividualEnrollment($examId,$enrollId,$component);}catch(Exception $e){show_error($e->getMessage(),409);return;}if(!$calc['overall']['complete'])show_error('This provisional result is incomplete. Enter all required marks before generating its marksheet.',409);$result=['id'=>null,'overall_percentage'=>$calc['overall']['percentage'],'total_full_marks'=>$calc['overall']['total_full'],'total_obtained_marks'=>$calc['overall']['total_obtained'],'result_label'=>$calc['subject_fail']?'Fail':($calc['grade']?$calc['grade']['name']:''),'result_status'=>$calc['result_status'],'publication_status'=>'draft','class_position'=>null,'working_days'=>$this->weighted_exam_model->workingDays($calc['enroll']['class_id'],$calc['enroll']['section_id'],$calc['exam']['session_id']),'remarks'=>'','data'=>$calc];}
        $student=$this->studentRecord($enrollId);if(!$student)show_404();$html=$this->load->view('weighted_exam/individual_marksheet',['result'=>$result,'record'=>$result['data'],'student'=>$student,'pdf'=>(bool)$pdf],true);
        if($pdf){$filename='individual-'.$component.'-'.$student['register_no'].'.pdf';$this->load->library('html2pdf',['format'=>'A4','margin_left'=>7,'margin_right'=>7,'margin_top'=>7,'margin_bottom'=>7]);$this->html2pdf->mpdf->autoScriptToLang=true;$this->html2pdf->mpdf->autoLangToFont=true;$this->html2pdf->mpdf->WriteHTML($html);$this->output->set_content_type('application/pdf')->set_header('Content-Disposition: attachment; filename="'.$filename.'"')->set_output($this->html2pdf->mpdf->Output($filename,'S'));return;}
        echo $this->load->view('weighted_exam/individual_document',['documents'=>[$html],'autoPrint'=>(bool)$this->input->get('print')],true);
    }

    public function individual_class_marksheets($examId,$classId,$sectionId,$component,$pdf=0)
    {
        $this->allow('weighted_exam_individual_results');if(!in_array($component,['unit1','unit2','half_yearly','annual'],true))show_error('Invalid examination type.',400);$documents=[];
        foreach($this->weighted_exam_model->individualRows($examId,$classId,$sectionId,$component) as $row){if(!$row['result'])continue;$student=$this->studentRecord($row['student']['enroll_id']);if(!$student)continue;$documents[]=$this->load->view('weighted_exam/individual_marksheet',['result'=>$row['result'],'record'=>$row['result']['data'],'student'=>$student,'pdf'=>(bool)$pdf],true);}
        if(!$documents)show_error('No processed individual marksheets are available for this selection.',409);
        if($pdf){$filename='individual-'.$component.'-class-marksheets.pdf';$this->load->library('html2pdf',['format'=>'A4','margin_left'=>7,'margin_right'=>7,'margin_top'=>7,'margin_bottom'=>7]);$this->html2pdf->mpdf->autoScriptToLang=true;$this->html2pdf->mpdf->autoLangToFont=true;foreach($documents as $i=>$html){if($i)$this->html2pdf->mpdf->AddPage('P','','','','',7,7,7,7);$this->html2pdf->mpdf->WriteHTML($html);}$this->output->set_content_type('application/pdf')->set_header('Content-Disposition: attachment; filename="'.$filename.'"')->set_output($this->html2pdf->mpdf->Output($filename,'S'));return;}
        echo $this->load->view('weighted_exam/individual_document',['documents'=>$documents,'autoPrint'=>(bool)$this->input->get('print')],true);
    }

    private function studentRecord($enrollId)
    {
        return $this->db->select("e.*,s.register_no,CONCAT_WS(' ',s.first_name,s.last_name) student_name,COALESCE(NULLIF(p.father_name,''),p.name,'') guardian_name,c.name class_name,se.name section_name,b.name branch_name,b.school_name,b.address,b.city,b.state,b.email,b.mobileno,b.institution_code,sy.school_year")
            ->from('enroll e')->join('student s','s.id=e.student_id')->join('parent p','p.id=s.parent_id','left')->join('class c','c.id=e.class_id')->join('section se','se.id=e.section_id')->join('branch b','b.id=e.branch_id')->join('schoolyear sy','sy.id=e.session_id')->where(['e.id'=>(int)$enrollId,'e.branch_id'=>$this->weighted_exam_model->branchId()])->get()->row_array();
    }

    public function reports()
    {
        $this->allow('weighted_exam_reports');$examId=(int)$this->input->post_get('exam_id');$classId=(int)$this->input->post_get('class_id');$sectionId=(int)$this->input->post_get('section_id');$rows=[];
        if($examId&&$classId&&$sectionId){$students=$this->weighted_exam_model->students($examId,$classId,$sectionId);foreach($students as $s){$r=$this->weighted_exam_model->result($examId,$s['enroll_id']);$calc=$this->weighted_exam_model->calculateEnrollment($examId,$s['enroll_id']);$missing=0;$absent=0;foreach($calc['subjects'] as $subject)foreach($subject['result']['details'] as $key=>$detail){if($key==='final_unit')continue;if($detail['status']==='not_entered')$missing++;if($detail['status']==='absent')$absent++;}$rows[]=['student'=>$s,'result'=>$r,'calculation'=>$calc,'missing'=>$missing,'absent'=>$absent];}}
        if($this->input->get('export')==='csv'){$this->output->set_content_type('text/csv')->set_header('Content-Disposition: attachment; filename="weighted-exam-report.csv"');$f=fopen('php://output','w');fputcsv($f,['Roll','Register No','Student','Percentage','Result','Position','Completion','Missing Components','Absent Components']);foreach($rows as $r)fputcsv($f,[$r['student']['roll'],$r['student']['register_no'],$r['student']['student_name'],$r['result']?$r['result']['overall_percentage']:'Not Processed',$r['result']?$r['result']['result_label']:'',$r['result']?$r['result']['class_position']:'',$r['calculation']['overall']['complete']?'Complete':'Incomplete',$r['missing'],$r['absent']]);fclose($f);return;}
        $this->baseData('weighted_exam/reports','Weighted Exam Reports');$this->data += compact('examId','classId','sectionId','rows');$this->load->view('layout/index',$this->data);
    }

    public function grades()
    {
        $this->allow('weighted_exam_grades');if($this->input->post('save')){$this->allow('weighted_exam_grades','is_add');$rows=(array)$this->input->post('rules');$this->db->trans_start();$this->db->where('branch_id',$this->weighted_exam_model->branchId())->delete('weighted_exam_grade_rules');foreach($rows as $i=>$r)if(trim($r['name'])!=='')$this->db->insert('weighted_exam_grade_rules',['branch_id'=>$this->weighted_exam_model->branchId(),'name'=>trim($r['name']),'minimum_percentage'=>(float)$r['min'],'maximum_percentage'=>(float)$r['max'],'is_pass'=>empty($r['pass'])?0:1,'sort_order'=>$i]);$this->db->trans_complete();set_alert('success','Grade rules saved.');redirect('weighted_exam/grades');}
        $this->baseData('weighted_exam/grades','Weighted Exam Grade Rules');$this->data['rules']=$this->db->where('branch_id',$this->weighted_exam_model->branchId())->order_by('sort_order')->get('weighted_exam_grade_rules')->result_array();$this->load->view('layout/index',$this->data);
    }
}
