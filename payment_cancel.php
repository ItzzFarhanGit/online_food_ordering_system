<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Cancelled</title>
<style>
body{font-family:Arial,sans-serif;background:#f8f0e3;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;}
.box{background:#fff;padding:40px;border-radius:12px;max-width:480px;text-align:center;box-shadow:0 10px 30px rgba(0,0,0,0.15);}
.box h2{color:#c0392b;}
a.btn{display:inline-block;margin-top:20px;background:#c0392b;color:#fff;padding:10px 22px;border-radius:6px;text-decoration:none;}
</style>
</head>
<body>
<div class="box">
    <h2>❌ Payment Cancelled</h2>
    <p>Your card payment was cancelled or didn't go through. No charge was made. You can try again or choose a different payment method.</p>
    <a class="btn" href="payment.php">&larr; Back to Payment Options</a>
</div>
</body>
</html>
