-- Independent raw-component result snapshots and publication history.
-- This migration does not alter the annual weighted result tables.
CREATE TABLE IF NOT EXISTS `weighted_exam_individual_results` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `exam_id` int(11) NOT NULL,
  `enroll_id` int(11) NOT NULL,
  `component` enum('unit1','unit2','half_yearly','annual') NOT NULL,
  `total_full_marks` decimal(12,2) NOT NULL,
  `total_obtained_marks` decimal(12,2) NOT NULL,
  `overall_percentage` decimal(10,4) NOT NULL,
  `grade_rule_id` int(11) NULL,
  `result_label` varchar(100) NULL,
  `result_status` enum('pass','fail') NOT NULL,
  `publication_status` enum('draft','published') NOT NULL DEFAULT 'draft',
  `class_position` int(11) NULL,
  `working_days` int(11) NULL,
  `remarks` text NULL,
  `snapshot` longtext NOT NULL,
  `processed_by` int(11) NULL,
  `processed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `published_by` int(11) NULL,
  `published_at` datetime NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_weir_result` (`exam_id`,`enroll_id`,`component`),
  KEY `idx_weir_rank` (`exam_id`,`component`,`overall_percentage`),
  KEY `idx_weir_publication` (`exam_id`,`component`,`publication_status`),
  CONSTRAINT `fk_weir_exam` FOREIGN KEY (`exam_id`) REFERENCES `weighted_exams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `weighted_exam_individual_result_audit` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `result_id` bigint(20) NOT NULL,
  `old_status` varchar(20) NULL,
  `new_status` varchar(20) NOT NULL,
  `changed_by` int(11) NULL,
  `changed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_weira_result` (`result_id`),
  CONSTRAINT `fk_weira_result` FOREIGN KEY (`result_id`) REFERENCES `weighted_exam_individual_results` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `permission` (`module_id`,`name`,`prefix`,`show_view`,`show_add`,`show_edit`,`show_delete`)
SELECT pm.id,'Individual Exam Results','weighted_exam_individual_results',1,1,1,0
FROM `permission_modules` pm
WHERE pm.prefix='weighted_exam'
AND NOT EXISTS (SELECT 1 FROM `permission` p WHERE p.prefix='weighted_exam_individual_results');

INSERT INTO `staff_privileges` (`role_id`,`permission_id`,`is_add`,`is_edit`,`is_view`,`is_delete`)
SELECT 2,p.id,p.show_add,p.show_edit,p.show_view,p.show_delete FROM `permission` p
WHERE p.prefix='weighted_exam_individual_results'
AND EXISTS (SELECT 1 FROM `roles` r WHERE r.id=2)
AND NOT EXISTS (SELECT 1 FROM `staff_privileges` sp WHERE sp.role_id=2 AND sp.permission_id=p.id);
