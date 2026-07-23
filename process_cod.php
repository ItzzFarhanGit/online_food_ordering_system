<?php
session_start();

$order = $_SESSION['pending_order'] ?? null;
if (!$order) {
    header("Location: menu.php");
    exit;
}

$_SESSION['final_order'] = array_merge($order, [
    'payment_method' => 'COD',
    'payment_status' => 'Pending (Cash)',
    'transaction_ref' => null,
]);

unset($_SESSION['pending_order']);
header("Location: order_success.php");
exit;
