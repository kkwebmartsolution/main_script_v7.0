<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Server-side credentials; environment variables can override these values.
$config['api_key'] = getenv('FAST2SMS_API_KEY') ?: 'x6FSy3L0Rt8ADfE5QWKqlMiuN7zdgjHmpwIJobhZ1naOXVc2TB10zSnBDxcN4LH2Qv5jb9CtfEYVMhTK';
$config['otp_id'] = getenv('FAST2SMS_OTP_ID') ?: 'd7c22117c4';
$config['variables_values'] = getenv('FAST2SMS_OTP_VARIABLES') ?: '';
