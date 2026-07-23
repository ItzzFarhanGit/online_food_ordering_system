<?php
session_start();
include 'db.php';

$session_id = $_GET['session_id'] ?? '';

if ($session_id === '') {
    header("Location: menu.php");
    exit;
}

$ch = curl_init('https://api.stripe.com/v1/checkout/sessions/' . urlencode($session_id));
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . STRIPE_SECRET_KEY],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 20,
]);
$response = curl_exec($ch);
curl_close($ch);
$data = $response ? json_decode($response, true) : null;

if (!$data || ($data['payment_status'] ?? '') !== 'paid') {
    header("Location: payment_cancel.php");
    exit;
}

$order = $_SESSION['stripe_pending_' . $session_id] ?? ($_SESSION['pending_order'] ?? null);

if (!$order) {
    // Payment succeeded but we lost the order details (e.g. session expired) -
    // still show a confirmation instead of leaving the customer stranded.
    $order = [
        'dish_name' => 'Your Order',
        'price' => ($data['amount_total'] ?? 0) / 100,
        'quantity' => 1,
        'customer_name' => $data['customer_details']['name'] ?? 'Customer',
        'phone' => '',
        'address' => '',
    ];
}

$_SESSION['final_order'] = array_merge($order, [
    'payment_method' => 'Card',
    'payment_status' => 'Paid',
    'transaction_ref' => $session_id,
]);

unset($_SESSION['pending_order'], $_SESSION['stripe_pending_' . $session_id]);
header("Location: order_success.php");
exit;
