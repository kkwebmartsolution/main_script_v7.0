-- Makes the combined Final Unit Test weight explicit without touching entered marks.
ALTER TABLE `weighted_exam_subjects`
  ADD COLUMN IF NOT EXISTS `unit_test_weightage` decimal(7,4) NOT NULL DEFAULT 20.0000 AFTER `pass_marks`,
  ADD COLUMN IF NOT EXISTS `weightage_review_required` tinyint(1) NOT NULL DEFAULT 0 AFTER `unit_test_weightage`,
  ADD COLUMN IF NOT EXISTS `combined_weight_migrated` tinyint(1) NOT NULL DEFAULT 0 AFTER `weightage_review_required`;

-- No unit tests: the combined unit weight is unambiguously zero.
UPDATE `weighted_exam_subjects` wes
JOIN `weighted_exam_subject_components` u1 ON u1.exam_subject_id=wes.id AND u1.component='unit1'
JOIN `weighted_exam_subject_components` u2 ON u2.exam_subject_id=wes.id AND u2.component='unit2'
SET wes.unit_test_weightage=0, wes.weightage_review_required=0, wes.combined_weight_migrated=1
WHERE u1.enabled=0 AND u2.enabled=0 AND wes.combined_weight_migrated=0;

-- Legacy module convention: exactly one unit row held the combined weight.
UPDATE `weighted_exam_subjects` wes
JOIN `weighted_exam_subject_components` u1 ON u1.exam_subject_id=wes.id AND u1.component='unit1'
JOIN `weighted_exam_subject_components` u2 ON u2.exam_subject_id=wes.id AND u2.component='unit2'
SET wes.unit_test_weightage=CASE WHEN u1.weightage>0 THEN u1.weightage ELSE u2.weightage END,
    wes.weightage_review_required=0, wes.combined_weight_migrated=1
WHERE (u1.enabled=1 OR u2.enabled=1)
  AND wes.combined_weight_migrated=0
  AND ((u1.weightage>0 AND u2.weightage=0) OR (u2.weightage>0 AND u1.weightage=0));

-- Both-positive or both-zero legacy values are ambiguous. Preserve the previous
-- calculator behaviour (MAX), but require an administrator to review and save.
UPDATE `weighted_exam_subjects` wes
JOIN `weighted_exam_subject_components` u1 ON u1.exam_subject_id=wes.id AND u1.component='unit1'
JOIN `weighted_exam_subject_components` u2 ON u2.exam_subject_id=wes.id AND u2.component='unit2'
SET wes.unit_test_weightage=GREATEST(u1.weightage,u2.weightage),
    wes.weightage_review_required=1, wes.combined_weight_migrated=1
WHERE (u1.enabled=1 OR u2.enabled=1)
  AND wes.combined_weight_migrated=0
  AND ((u1.weightage>0 AND u2.weightage>0) OR (u1.weightage=0 AND u2.weightage=0));

-- Defensive fallback for incomplete legacy component sets.
UPDATE `weighted_exam_subjects`
SET `weightage_review_required`=1, `combined_weight_migrated`=1
WHERE `combined_weight_migrated`=0;

-- Component rows no longer own a weight. Marks and full marks remain untouched.
UPDATE `weighted_exam_subject_components`
SET `weightage`=0
WHERE `component` IN ('unit1','unit2');

