-- WARNING: removes weighted-exam schedule data and subject pass thresholds.
DROP TABLE IF EXISTS `weighted_exam_schedule`;
ALTER TABLE `weighted_exam_subjects` DROP COLUMN IF EXISTS `pass_marks`;
DELETE sp FROM `staff_privileges` sp JOIN `permission` p ON p.id=sp.permission_id WHERE p.prefix='weighted_exam_schedule';
DELETE FROM `permission` WHERE `prefix`='weighted_exam_schedule';

