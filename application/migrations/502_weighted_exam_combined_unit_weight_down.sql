-- Rollback maps the explicit combined weight back to the old Unit 1 convention.
UPDATE `weighted_exam_subject_components` c
JOIN `weighted_exam_subjects` wes ON wes.id=c.exam_subject_id
SET c.weightage=wes.unit_test_weightage
WHERE c.component='unit1';
ALTER TABLE `weighted_exam_subjects`
  DROP COLUMN IF EXISTS `combined_weight_migrated`,
  DROP COLUMN IF EXISTS `weightage_review_required`,
  DROP COLUMN IF EXISTS `unit_test_weightage`;
