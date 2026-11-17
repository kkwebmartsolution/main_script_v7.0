<?php
define('BASEPATH', __DIR__);
require_once __DIR__ . '/../application/libraries/Weighted_exam_calculator.php';

$calculator = new Weighted_exam_calculator();
$failures = 0;
function expectNear($actual, $expected, $message) {
    global $failures;
    if ($actual === null || abs($actual - $expected) > 0.00001) {
        $failures++; fwrite(STDERR, "FAIL: {$message}; expected {$expected}, got " . var_export($actual, true) . PHP_EOL);
    }
}
function expectTrue($actual, $message) {
    global $failures;
    if (!$actual) { $failures++; fwrite(STDERR, "FAIL: {$message}" . PHP_EOL); }
}
function standardConfig($unitFull = 50, $otherFull = 100) {
    return [
        'unit1'=>['enabled'=>1,'full_marks'=>$unitFull,'weightage'=>20],
        'unit2'=>['enabled'=>1,'full_marks'=>$unitFull,'weightage'=>0],
        'half_yearly'=>['enabled'=>1,'full_marks'=>$otherFull,'weightage'=>30],
        'annual'=>['enabled'=>1,'full_marks'=>$otherFull,'weightage'=>50],
    ];
}
function presentMarks($u1,$u2,$half,$annual) {
    return ['unit1'=>['status'=>'present','obtained_marks'=>$u1],'unit2'=>['status'=>'present','obtained_marks'=>$u2],'half_yearly'=>['status'=>'present','obtained_marks'=>$half],'annual'=>['status'=>'present','obtained_marks'=>$annual]];
}

$r=$calculator->subject(standardConfig(),presentMarks(48,50,100,100)); expectNear($r['score'],99.60,'Mother Tongue reference');
$r=$calculator->subject(standardConfig(),presentMarks(43,35,58,88)); expectNear($r['score'],77.00,'Mathematics reference');
$explicit=standardConfig();$explicit['unit1']['weightage']=0;$explicit['unit2']['weightage']=0;$explicit['final_unit']=['enabled'=>1,'full_marks'=>100,'weightage'=>20];$r=$calculator->subject($explicit,presentMarks(43,35,58,88));expectNear($r['details']['final_unit']['obtained'],78,'Explicit final unit obtained');expectNear($r['details']['final_unit']['full_marks'],100,'Explicit final unit full marks');expectNear($r['details']['final_unit']['weighted'],15.60,'Explicit combined unit weight');expectNear($r['score'],77.00,'Explicit combined unit final score');
$r=$calculator->subject(standardConfig(25,50),presentMarks(22,25,48,48)); expectNear($r['score'],95.60,'Moral Education reference');
$computer=['unit1'=>['enabled'=>0,'full_marks'=>null,'weightage'=>0],'unit2'=>['enabled'=>0,'full_marks'=>null,'weightage'=>0],'half_yearly'=>['enabled'=>1,'full_marks'=>50,'weightage'=>50],'annual'=>['enabled'=>1,'full_marks'=>50,'weightage'=>50]];
$r=$calculator->subject($computer,['half_yearly'=>['status'=>'present','obtained_marks'=>49],'annual'=>['status'=>'present','obtained_marks'=>43]]); expectNear($r['score'],92.00,'Computer without unit tests');
$computer['final_unit']=['enabled'=>0,'full_marks'=>null,'weightage'=>0];$r=$calculator->subject($computer,['half_yearly'=>['status'=>'present','obtained_marks'=>49],'annual'=>['status'=>'present','obtained_marks'=>43]]);expectTrue($r['details']['final_unit']['status']==='not_applicable','Final Unit Test is N/A when both units are disabled');expectNear($r['score'],92.00,'No-unit subject with explicit zero unit weight');
$scores=[85.60,71.20,89.50,89.30,99.20,84.30,82.20,95.60,92.00];$subjects=[];foreach($scores as $i=>$score)$subjects[]=['name'=>'S'.$i,'include_in_percentage'=>1,'result'=>['complete'=>true,'score'=>$score]];$o=$calculator->overall($subjects);expectNear($o['total'],788.90,'Overall total (the prompt incorrectly states 704.60)');expectNear($o['percentage'],87.6555555556,'Overall percentage from the supplied scores');
$r=$calculator->subject(standardConfig(),presentMarks(51,20,50,50));expectTrue(!$r['complete'],'Marks above full marks rejected');
$m=presentMarks(40,40,80,80);unset($m['annual']);$r=$calculator->subject(standardConfig(),$m);expectTrue(!$r['complete']&&$r['score']===null,'Missing component remains incomplete');
$m=presentMarks(40,40,80,80);$m['annual']=['status'=>'absent','obtained_marks'=>null];$r=$calculator->subject(standardConfig(),$m,'incomplete');expectTrue(!$r['complete'],'Absent is distinct and blocks by default');$r=$calculator->subject(standardConfig(),$m,'zero');expectTrue($r['complete'],'Explicit zero absence policy calculates');
$subjects[0]['include_in_percentage']=0;$o=$calculator->overall($subjects);expectTrue($o['subject_count']===8,'Excluded subject omitted from divisor');
expectTrue($calculator->passes(33,33)===true,'Score equal to subject pass marks passes');
expectTrue($calculator->passes(32.99,33)===false,'Score below subject pass marks fails');
expectTrue($calculator->passes(null,33)===null,'Incomplete score has no pass/fail state');

// Individual examination calculations use raw marks and actual denominators.
$raw=[];
foreach([['Assamese',50,48],['Mathematics',50,50],['English',50,50],['Moral Education',25,25],['Environmental Science',25,24]] as $row){
    $result=$calculator->individualSubject(['enabled'=>1,'full_marks'=>$row[1]],['status'=>'present','obtained_marks'=>$row[2]],33,'incomplete');
    $raw[]=['name'=>$row[0],'include_in_percentage'=>1,'result'=>$result];
}
$individual=$calculator->individualOverall($raw);expectNear($individual['total_full'],200,'Unit 1 actual total full marks');expectNear($individual['total_obtained'],197,'Unit 1 actual obtained marks');expectNear($individual['percentage'],98.5,'Unit 1 raw percentage');
$half=[];foreach([[100,100],[100,100],[100,100],[50,50],[50,49]] as $i=>$row)$half[]=['name'=>'S'.$i,'include_in_percentage'=>1,'result'=>$calculator->individualSubject(['enabled'=>1,'full_marks'=>$row[0]],['status'=>'present','obtained_marks'=>$row[1]],33,'incomplete')];$individual=$calculator->individualOverall($half);expectNear($individual['total_full'],400,'Half-yearly mixed full marks');expectNear($individual['total_obtained'],399,'Half-yearly obtained total');expectNear($individual['percentage'],99.75,'Half-yearly raw percentage');
$missing=$calculator->individualSubject(['enabled'=>1,'full_marks'=>50],null,33,'incomplete');expectTrue(!$missing['complete']&&$missing['status']==='not_entered','Individual missing mark remains distinct');
$absent=$calculator->individualSubject(['enabled'=>1,'full_marks'=>50],['status'=>'absent','obtained_marks'=>null],33,'incomplete');expectTrue(!$absent['complete'],'Individual absence blocks under incomplete policy');$absent=$calculator->individualSubject(['enabled'=>1,'full_marks'=>50],['status'=>'absent','obtained_marks'=>null],33,'zero');expectTrue($absent['complete']&&$absent['obtained']===0.0&&!$absent['passed'],'Individual absence follows zero policy');
$na=$calculator->individualSubject(['enabled'=>0,'full_marks'=>null],null,33,'incomplete');expectTrue(!$na['applicable']&&$na['status']==='not_applicable','Disabled individual component is not applicable');
$exempt=$calculator->individualSubject(['enabled'=>1,'full_marks'=>50],['status'=>'exempt','obtained_marks'=>null],33,'incomplete');expectTrue($exempt['complete']&&!$exempt['counted'],'Exempt subject is distinct and excluded from totals');

if ($failures) exit(1);
echo "Weighted exam calculator: all tests passed" . PHP_EOL;
