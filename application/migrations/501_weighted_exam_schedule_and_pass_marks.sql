-- Adds subject-wise pass thresholds and the weighted-exam schedule.
ALTER TABLE `weighted_exam_subjects`
  ADD COLUMN IF NOT EXISTS `pass_marks` decimal(7,2) NOT NULL DEFAULT 33.00 AFTER `include_in_percentage`;

CREATE TABLE IF NOT EXISTS `weighted_exam_schedule` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `exam_id` int(11) NOT NULL,
  `exam_subject_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `component` enum('unit1','unit2','half_yearly','annual') NOT NULL,
  `exam_date` date NULL,
  `start_time` time NULL,
  `end_time` time NULL,
  `room_no` varchar(100) NULL,
  `instructions` varchar(500) NULL,
  `updated_by` int(11) NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_weighted_schedule` (`exam_id`,`exam_subject_id`,`section_id`,`component`),
  KEY `idx_weighted_schedule_date` (`exam_id`,`section_id`,`exam_date`),
  CONSTRAINT `fk_weighted_schedule_exam` FOREIGN KEY (`exam_id`) REFERENCES `weighted_exams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_weighted_schedule_subject` FOREIGN KEY (`exam_subject_id`) REFERENCES `weighted_exam_subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `permission` (`module_id`,`name`,`prefix`,`show_view`,`show_add`,`show_edit`,`show_delete`)
SELECT pm.id,'Exam Schedule','weighted_exam_schedule',1,1,1,1
FROM `permission_modules` pm
WHERE pm.prefix='weighted_exam'
AND NOT EXISTS (SELECT 1 FROM `permission` p WHERE p.prefix='weighted_exam_schedule');

INSERT INTO `staff_privileges` (`role_id`,`permission_id`,`is_add`,`is_edit`,`is_view`,`is_delete`)
SELECT 2,p.id,p.show_add,p.show_edit,p.show_view,p.show_delete FROM `permission` p
WHERE p.prefix='weighted_exam_schedule'
AND EXISTS (SELECT 1 FROM `roles` r WHERE r.id=2)
AND NOT EXISTS (SELECT 1 FROM `staff_privileges` sp WHERE sp.role_id=2 AND sp.permission_id=p.id);

