-- Weighted Based Exam module (safe, additive migration)
-- Apply once to the configured Ramom database. No legacy exam table is changed.
CREATE TABLE IF NOT EXISTS `weighted_exams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NULL,
  `publication_date` date NULL,
  `status` enum('draft','active','finalized','published') NOT NULL DEFAULT 'draft',
  `absence_policy` enum('incomplete','zero') NOT NULL DEFAULT 'incomplete',
  `ranking_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`), KEY `idx_we_branch_session` (`branch_id`,`session_id`),
  UNIQUE KEY `uq_we_name` (`branch_id`,`session_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `weighted_exam_classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT, `exam_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL, `section_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`), UNIQUE KEY `uq_wec` (`exam_id`,`class_id`,`section_id`),
  KEY `idx_wec_class` (`class_id`,`section_id`),
  CONSTRAINT `fk_wec_exam` FOREIGN KEY (`exam_id`) REFERENCES `weighted_exams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `weighted_exam_subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT, `exam_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL, `subject_id` int(11) NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0, `include_in_percentage` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`), UNIQUE KEY `uq_wes` (`exam_id`,`class_id`,`subject_id`),
  KEY `idx_wes_subject` (`subject_id`),
  CONSTRAINT `fk_wes_exam` FOREIGN KEY (`exam_id`) REFERENCES `weighted_exams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `weighted_exam_subject_components` (
  `id` int(11) NOT NULL AUTO_INCREMENT, `exam_subject_id` int(11) NOT NULL,
  `component` enum('unit1','unit2','half_yearly','annual') NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1, `full_marks` decimal(10,2) NULL,
  `weightage` decimal(7,4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`), UNIQUE KEY `uq_wesc` (`exam_subject_id`,`component`),
  CONSTRAINT `fk_wesc_subject` FOREIGN KEY (`exam_subject_id`) REFERENCES `weighted_exam_subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `weighted_exam_marks` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT, `exam_id` int(11) NOT NULL,
  `exam_subject_id` int(11) NOT NULL, `enroll_id` int(11) NOT NULL,
  `component` enum('unit1','unit2','half_yearly','annual') NOT NULL,
  `obtained_marks` decimal(10,2) NULL,
  `status` enum('present','absent','exempt','not_entered') NOT NULL DEFAULT 'not_entered',
  `entered_by` int(11) NULL, `updated_by` int(11) NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`), UNIQUE KEY `uq_wem` (`exam_id`,`exam_subject_id`,`enroll_id`,`component`),
  KEY `idx_wem_enroll` (`enroll_id`),
  CONSTRAINT `fk_wem_exam` FOREIGN KEY (`exam_id`) REFERENCES `weighted_exams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wem_subject` FOREIGN KEY (`exam_subject_id`) REFERENCES `weighted_exam_subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `weighted_exam_mark_audit` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT, `mark_id` bigint(20) NULL,
  `exam_id` int(11) NOT NULL, `exam_subject_id` int(11) NOT NULL, `enroll_id` int(11) NOT NULL,
  `component` varchar(20) NOT NULL, `old_marks` decimal(10,2) NULL, `new_marks` decimal(10,2) NULL,
  `old_status` varchar(20) NULL, `new_status` varchar(20) NULL,
  `changed_by` int(11) NULL, `changed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`), KEY `idx_wema_lookup` (`exam_id`,`enroll_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `weighted_exam_results` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT, `exam_id` int(11) NOT NULL, `enroll_id` int(11) NOT NULL,
  `overall_percentage` decimal(10,4) NULL, `grade_rule_id` int(11) NULL,
  `result_label` varchar(100) NULL, `result_status` enum('pass','fail','incomplete') NOT NULL DEFAULT 'incomplete',
  `class_position` int(11) NULL, `working_days` int(11) NULL, `remarks` text NULL,
  `snapshot` longtext NULL, `processed_by` int(11) NULL,
  `processed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP, `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`), UNIQUE KEY `uq_wer` (`exam_id`,`enroll_id`), KEY `idx_wer_rank` (`exam_id`,`overall_percentage`),
  CONSTRAINT `fk_wer_exam` FOREIGN KEY (`exam_id`) REFERENCES `weighted_exams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `weighted_exam_subject_results` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT, `result_id` bigint(20) NOT NULL, `exam_subject_id` int(11) NOT NULL,
  `subject_score` decimal(10,4) NULL, `calculation` longtext NULL, `is_complete` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`), UNIQUE KEY `uq_wesr` (`result_id`,`exam_subject_id`),
  CONSTRAINT `fk_wesr_result` FOREIGN KEY (`result_id`) REFERENCES `weighted_exam_results` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `weighted_exam_grade_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT, `branch_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL, `minimum_percentage` decimal(7,2) NOT NULL,
  `maximum_percentage` decimal(7,2) NOT NULL, `is_pass` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0, PRIMARY KEY (`id`),
  UNIQUE KEY `uq_wegr` (`branch_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `permission_modules` (`name`,`prefix`,`system`,`sorted`,`in_module`)
SELECT 'Weighted Based Exam','weighted_exam',1,9,1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permission_modules` WHERE `prefix`='weighted_exam');

INSERT INTO `permission` (`module_id`,`name`,`prefix`,`show_view`,`show_add`,`show_edit`,`show_delete`)
SELECT pm.id, p.name, p.prefix, p.sv, p.sa, p.se, p.sd
FROM `permission_modules` pm JOIN (
 SELECT 'Exam Setup' name,'weighted_exam_setup' prefix,1 sv,1 sa,1 se,1 sd UNION ALL
 SELECT 'Exam Subjects','weighted_exam_subjects',1,1,1,0 UNION ALL
 SELECT 'Marks Entry','weighted_exam_marks',1,1,1,0 UNION ALL
 SELECT 'Marks Overview','weighted_exam_overview',1,0,1,0 UNION ALL
 SELECT 'Result Processing','weighted_exam_processing',1,1,1,0 UNION ALL
 SELECT 'Generate Marksheet','weighted_exam_marksheet',1,1,0,0 UNION ALL
 SELECT 'Exam Reports','weighted_exam_reports',1,0,0,0 UNION ALL
 SELECT 'Grade Rules','weighted_exam_grades',1,1,1,1
) p ON 1=1
WHERE pm.prefix='weighted_exam' AND NOT EXISTS (SELECT 1 FROM `permission` x WHERE x.prefix=p.prefix);

-- The built-in Administrator role receives the module by default. Other roles,
-- including teachers, remain opt-in through the existing Role Permission screen.
INSERT INTO `staff_privileges` (`role_id`,`permission_id`,`is_add`,`is_edit`,`is_view`,`is_delete`)
SELECT 2,p.id,p.show_add,p.show_edit,p.show_view,p.show_delete FROM `permission` p
WHERE p.prefix LIKE 'weighted_exam_%'
AND EXISTS (SELECT 1 FROM `roles` r WHERE r.id=2)
AND NOT EXISTS (SELECT 1 FROM `staff_privileges` sp WHERE sp.role_id=2 AND sp.permission_id=p.id);

