<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cashfree_payment
{
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    public function payment($params)
    {
        $config = $this->CI->get_payment_config();
        if ($config['cashfree_sandbox'] == 1) {
            $url = "https://sandbox.cashfree.com/pg/orders";
        } else {
            $url = "https://api.cashfree.com/pg/orders";
        }

        $order_id = "ORDER_" . uniqid();
        $postData = array(
            "order_id" => $order_id,
            "order_amount" => $params['amount'],
            "order_currency" => $params['currency'] == 'INR' ? 'INR' : $params['currency'],
            "customer_details" => array(
                "customer_id" => "CUST_" . $params['student_id'],
                "customer_email" => $params['student_email'],
                "customer_phone" => $params['student_phone'] ? $params['student_phone'] : "9999999999"
            ),
            "order_meta" => array(
                "return_url" => (isset($params['return_url']) ? $params['return_url'] : base_url("feespayment/cashfree_verify?order_id={order_id}")),
                "notify_url" => (isset($params['notify_url']) ? $params['notify_url'] : base_url("feespayment/cashfree_webhook"))
            )
        );

        $headers = array(
            'Content-Type: application/json',
            'x-api-version: 2023-08-01',
            'x-client-id: ' . $config['cashfree_app_id'],
            'x-client-secret: ' . $config['cashfree_secret_key']
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function verify($order_id)
    {
        $config = $this->CI->get_payment_config();
        if ($config['cashfree_sandbox'] == 1) {
            $url = "https://sandbox.cashfree.com/pg/orders/" . $order_id;
        } else {
            $url = "https://api.cashfree.com/pg/orders/" . $order_id;
        }

        $headers = array(
            'Content-Type: application/json',
            'x-api-version: 2023-08-01',
            'x-client-id: ' . $config['cashfree_app_id'],
            'x-client-secret: ' . $config['cashfree_secret_key']
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}
