<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2026-11-18 17:01:51 --> Severity: Warning --> mysqli::real_connect(): (HY000/2002): No connection could be made because the target machine actively refused it C:\xampp\htdocs\ramom-v7.0\main_script_v7.0\system\database\drivers\mysqli\mysqli_driver.php 214
ERROR - 2026-11-18 17:01:51 --> Unable to connect to the database
ERROR - 2026-11-18 17:41:07 --> Query error: Table 'sss_ramom.custom_domain' doesn't exist in engine - Invalid query: SELECT count(id) as cid
FROM `custom_domain`
WHERE `status` = 1
AND `url` = 'localhost'
ERROR - 2026-11-18 17:41:07 --> Severity: error --> Exception: Call to a member function row() on bool C:\xampp\htdocs\ramom-v7.0\main_script_v7.0\application\config\routes.php 63
ERROR - 2026-11-18 17:41:09 --> Query error: Table 'sss_ramom.custom_domain' doesn't exist in engine - Invalid query: SELECT count(id) as cid
FROM `custom_domain`
WHERE `status` = 1
AND `url` = 'localhost'
ERROR - 2026-11-18 17:41:09 --> Severity: error --> Exception: Call to a member function row() on bool C:\xampp\htdocs\ramom-v7.0\main_script_v7.0\application\config\routes.php 63
