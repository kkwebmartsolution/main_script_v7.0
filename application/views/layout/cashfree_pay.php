<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashfree Payment</title>
    <script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f4f7f6; }
        .loader { border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; width: 40px; height: 40px; animation: spin 2s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .container { text-align: center; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container">
        <div class="loader" style="margin: 0 auto 20px;"></div>
        <h3>Processing Payment...</h3>
        <p>Please do not refresh the page or click back button.</p>
    </div>

    <script>
        const cashfree = Cashfree({
            mode: "<?php echo $environment; ?>"
        });
        cashfree.checkout({
            paymentSessionId: "<?php echo $payment_session_id; ?>"
        });
    </script>
</body>
</html>
