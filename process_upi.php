<?php
session_start();

$order = $_SESSION['pending_order'] ?? null;
if (!$order) {
    header("Location: menu.php");
    exit;
}

$upi_ref = trim($_POST['upi_ref'] ?? '');
if ($upi_ref === '') {
    header("Location: payment.php");
    exit;
}

$_SESSION['final_order'] = array_merge($order, [
    'payment_method' => 'UPI',
    'payment_status' => 'Pending Verification',
    'transaction_ref' => $upi_ref,
]);

unset($_SESSION['pending_order']);
header("Location: order_success.php");
exit;
