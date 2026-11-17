DELETE sp FROM `staff_privileges` sp JOIN `permission` p ON p.id=sp.permission_id WHERE p.prefix='weighted_exam_individual_results';
DELETE FROM `permission` WHERE `prefix`='weighted_exam_individual_results';
DROP TABLE IF EXISTS `weighted_exam_individual_result_audit`;
DROP TABLE IF EXISTS `weighted_exam_individual_results`;
