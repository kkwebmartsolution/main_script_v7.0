<?php defined('BASEPATH') or exit('No direct script access allowed');

/** Central, side-effect-free weighted result calculator. */
class Weighted_exam_calculator
{
    const COMPONENTS = ['unit1', 'unit2', 'half_yearly', 'annual'];

    public function subject(array $config, array $marks, $absencePolicy = 'incomplete')
    {
        $errors = [];
        $details = [];
        $score = 0.0;
        $unitObtained = 0.0;
        $unitFull = 0.0;
        $unitReady = true;
        $unitEnabled = false;

        foreach (self::COMPONENTS as $component) {
            $c = isset($config[$component]) ? $config[$component] : ['enabled' => 0, 'full_marks' => null, 'weightage' => 0];
            if (empty($c['enabled'])) {
                $details[$component] = ['status' => 'not_applicable', 'full_marks' => null, 'obtained' => null, 'weighted' => null];
                continue;
            }
            $full = (float) $c['full_marks'];
            if ($full <= 0) {
                $errors[] = $component . ': full marks must be greater than zero';
                continue;
            }
            $m = isset($marks[$component]) ? $marks[$component] : ['status' => 'not_entered', 'obtained_marks' => null];
            $status = isset($m['status']) ? $m['status'] : 'not_entered';
            $obtained = $m['obtained_marks'] === null || $m['obtained_marks'] === '' ? null : (float) $m['obtained_marks'];
            if ($status === 'absent' && $absencePolicy === 'zero') {
                $obtained = 0.0;
            } elseif ($status !== 'present') {
                $errors[] = $component . ': ' . str_replace('_', ' ', $status);
            }
            if ($obtained !== null && ($obtained < 0 || $obtained > $full)) {
                $errors[] = $component . ': marks outside 0-' . $full;
            }
            $details[$component] = ['status' => $status, 'full_marks' => $full, 'obtained' => $obtained, 'weighted' => null];

            if ($component === 'unit1' || $component === 'unit2') {
                $unitEnabled = true;
                $unitFull += $full;
                if ($obtained === null || ($status !== 'present' && !($status === 'absent' && $absencePolicy === 'zero'))) {
                    $unitReady = false;
                } else {
                    $unitObtained += $obtained;
                }
            } elseif ($obtained !== null && ($status === 'present' || ($status === 'absent' && $absencePolicy === 'zero'))) {
                $weighted = ($obtained / $full) * (float) $c['weightage'];
                $details[$component]['weighted'] = $weighted;
                $score += $weighted;
            }
        }

        $unitWeight = isset($config['final_unit']['weightage']) ? (float)$config['final_unit']['weightage'] : 0.0;
        // Backward-compatible fallback for snapshots/configurations created before
        // the explicit combined Final Unit Test weight migration.
        if (!isset($config['final_unit'])) foreach (['unit1', 'unit2'] as $u) if (isset($config[$u])) $unitWeight=max($unitWeight,(float)$config[$u]['weightage']);
        $details['final_unit'] = ['status' => !$unitEnabled ? 'not_applicable' : ($unitReady ? 'calculated' : 'incomplete'), 'full_marks' => $unitEnabled ? $unitFull : null, 'obtained' => ($unitEnabled && $unitReady) ? $unitObtained : null, 'weighted' => null];
        if ($unitEnabled && $unitReady && $unitFull > 0) {
            $details['final_unit']['weighted'] = ($unitObtained / $unitFull) * $unitWeight;
            $score += $details['final_unit']['weighted'];
        }

        return ['complete' => empty($errors), 'score' => empty($errors) ? $score : null, 'display_score' => empty($errors) ? number_format($score, 2, '.', '') : null, 'details' => $details, 'errors' => $errors];
    }

    public function overall(array $subjects)
    {
        $sum = 0.0; $count = 0; $errors = [];
        foreach ($subjects as $subject) {
            if (empty($subject['include_in_percentage'])) continue;
            if (empty($subject['result']['complete'])) {
                $errors[] = isset($subject['name']) ? $subject['name'] : 'Incomplete subject';
                continue;
            }
            $sum += (float) $subject['result']['score']; $count++;
        }
        $value = (!$errors && $count) ? $sum / $count : null;
        return ['complete' => empty($errors) && $count > 0, 'total' => $sum, 'subject_count' => $count, 'percentage' => $value, 'display_percentage' => $value === null ? null : number_format($value, 2, '.', ''), 'errors' => $errors];
    }

    public function passes($score, $passMarks)
    {
        if ($score === null) return null;
        return (float)$score >= (float)$passMarks;
    }

    /** Calculate one raw (non-weighted) examination component. */
    public function individualSubject(array $config, array $mark = null, $passPercentage = 33, $absencePolicy = 'incomplete')
    {
        if (empty($config['enabled'])) {
            return ['applicable'=>false,'complete'=>true,'counted'=>false,'status'=>'not_applicable','full_marks'=>null,'obtained'=>null,'percentage'=>null,'passed'=>null,'error'=>null];
        }
        $full=(float)$config['full_marks'];
        if($full<=0)return ['applicable'=>true,'complete'=>false,'counted'=>false,'status'=>'not_entered','full_marks'=>$full,'obtained'=>null,'percentage'=>null,'passed'=>null,'error'=>'Full marks must be greater than zero'];
        $status=$mark&&isset($mark['status'])?$mark['status']:'not_entered';
        $obtained=$mark&&$mark['obtained_marks']!==null&&$mark['obtained_marks']!==''?(float)$mark['obtained_marks']:null;
        if($status==='exempt')return ['applicable'=>true,'complete'=>true,'counted'=>false,'status'=>'exempt','full_marks'=>$full,'obtained'=>null,'percentage'=>null,'passed'=>null,'error'=>null];
        if($status==='absent'&&$absencePolicy==='zero')$obtained=0.0;
        $counted=$status==='present'||($status==='absent'&&$absencePolicy==='zero');
        if(!$counted)return ['applicable'=>true,'complete'=>false,'counted'=>false,'status'=>$status,'full_marks'=>$full,'obtained'=>null,'percentage'=>null,'passed'=>null,'error'=>ucwords(str_replace('_',' ',$status))];
        if($obtained===null||$obtained<0||$obtained>$full)return ['applicable'=>true,'complete'=>false,'counted'=>false,'status'=>$status,'full_marks'=>$full,'obtained'=>$obtained,'percentage'=>null,'passed'=>null,'error'=>'Marks must be between 0 and '.$full];
        $percentage=($obtained/$full)*100;
        return ['applicable'=>true,'complete'=>true,'counted'=>true,'status'=>$status,'full_marks'=>$full,'obtained'=>$obtained,'percentage'=>$percentage,'passed'=>$percentage>=(float)$passPercentage,'error'=>null];
    }

    /** Totals raw marks using their real, subject-specific denominators. */
    public function individualOverall(array $subjects)
    {
        $full=0.0;$obtained=0.0;$errors=[];$count=0;
        foreach($subjects as $subject){$r=$subject['result'];if(!$r['complete'])$errors[]=['subject'=>$subject['name'],'status'=>$r['status'],'message'=>$r['error']];if(empty($subject['include_in_percentage'])||!$r['counted'])continue;$full+=(float)$r['full_marks'];$obtained+=(float)$r['obtained'];$count++;}
        $percentage=(!$errors&&$full>0)?($obtained/$full)*100:null;
        return ['complete'=>empty($errors)&&$full>0,'total_full'=>$full,'total_obtained'=>$obtained,'subject_count'=>$count,'percentage'=>$percentage,'display_percentage'=>$percentage===null?null:number_format($percentage,2,'.',''),'errors'=>$errors];
    }
}
