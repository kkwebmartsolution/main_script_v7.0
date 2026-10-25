<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fast2sms_otp
{
    public function send($mobile, $otp)
    {
        $CI = &get_instance();
        $CI->config->load('fast2sms', true);
        $config = $CI->config->item('fast2sms');
        if (empty($config['api_key']) || empty($config['otp_id']) || !function_exists('curl_init')) {
            return array('status' => false, 'message' => 'SMS OTP is not configured or PHP cURL is unavailable.');
        }
        $mobile = preg_replace('/[^0-9]/', '', $mobile);
        if (strlen($mobile) === 12 && substr($mobile, 0, 2) === '91') {
            $mobile = substr($mobile, 2);
        } elseif (strlen($mobile) === 11 && substr($mobile, 0, 1) === '0') {
            $mobile = substr($mobile, 1);
        }
        if (!preg_match('/^[6-9][0-9]{9}$/', $mobile)) {
            return array('status' => false, 'message' => 'Please configure a valid Indian admin mobile number in School Settings.');
        }
        $payload = array('mobile' => $mobile, 'otp_id' => $config['otp_id'], 'otp_expiry' => 5, 'otp_length' => 6, 'otp' => $otp);
        if (!empty($config['variables_values'])) {
            $payload['variables_values'] = $config['variables_values'];
        }
        $ch = curl_init('https://www.fast2sms.com/dev/otp/send');
        curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => array('authorization: ' . $config['api_key'], 'Content-Type: application/json', 'Accept: application/json'),
            CURLOPT_POSTFIELDS => json_encode($payload),
        ));
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_errno($ch);
        curl_close($ch);
        $data = is_string($response) ? json_decode($response, true) : null;
        if (!$curlError && $httpCode >= 200 && $httpCode < 300 && is_array($data) && isset($data['return']) && $data['return'] === true) {
            return array('status' => true);
        }
        // Never log credentials, the OTP, or the raw provider response.
        log_message('error', 'Fee revert SMS failed: HTTP ' . $httpCode . ', cURL ' . $curlError);
        return array('status' => false, 'message' => 'SMS OTP could not be sent (HTTP ' . $httpCode . '). Please check Fast2SMS balance, credentials and OTP template settings, then try again.');
    }
}
