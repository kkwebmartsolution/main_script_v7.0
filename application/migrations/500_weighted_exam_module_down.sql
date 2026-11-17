-- Explicit rollback for 500_weighted_exam_module.sql.
-- WARNING: this permanently removes weighted-exam data. It does not touch legacy exam data.
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `weighted_exam_subject_results`;
DROP TABLE IF EXISTS `weighted_exam_results`;
DROP TABLE IF EXISTS `weighted_exam_mark_audit`;
DROP TABLE IF EXISTS `weighted_exam_marks`;
DROP TABLE IF EXISTS `weighted_exam_subject_components`;
DROP TABLE IF EXISTS `weighted_exam_subjects`;
DROP TABLE IF EXISTS `weighted_exam_classes`;
DROP TABLE IF EXISTS `weighted_exam_grade_rules`;
DROP TABLE IF EXISTS `weighted_exams`;
SET FOREIGN_KEY_CHECKS=1;

DELETE sp FROM `staff_privileges` sp JOIN `permission` p ON p.id=sp.permission_id WHERE p.prefix LIKE 'weighted_exam_%';
DELETE FROM `permission` WHERE `prefix` LIKE 'weighted_exam_%';
DELETE FROM `permission_modules` WHERE `prefix`='weighted_exam';
