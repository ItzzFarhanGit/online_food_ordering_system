<?php
session_start();
include 'db.php';

// Same login requirement as orders.php - guards against someone posting
// directly to this page and bypassing the order form.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=orders.php");
    exit;
}

// Step 1: order details arrive via POST from orders.php - store them in the
// session as a "pending order" so we can carry them through whichever
// payment method the customer picks next.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dish_name'])) {
    $_SESSION['pending_order'] = [
        'user_id'       => $_SESSION['user_id'] ?? null,
        'dish_name'     => trim($_POST['dish_name'] ?? ''),
        'price'         => (float) ($_POST['price'] ?? 0),
        'quantity'      => max(1, (int) ($_POST['quantity'] ?? 1)),
        'customer_name' => trim($_POST['customer_name'] ?? ''),
        'phone'         => trim($_POST['phone'] ?? ''),
        'address'       => trim($_POST['address'] ?? ''),
    ];
}

$order = $_SESSION['pending_order'] ?? null;

if (!$order || $order['dish_name'] === '' || $order['customer_name'] === '') {
    header("Location: menu.php");
    exit;
}

$total = $order['price'] * $order['quantity'];

// Build the UPI deep link (works with any UPI app on a phone, e.g. GPay/PhonePe/Paytm)
$upi_link = "upi://pay?pa=" . urlencode(UPI_VPA)
          . "&pn=" . urlencode(UPI_PAYEE_NAME)
          . "&am=" . urlencode(number_format($total, 2, '.', ''))
          . "&cu=INR&tn=" . urlencode("Order: " . $order['dish_name']);
$qr_img = "https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=" . urlencode($upi_link);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Choose Payment Method</title>
<link rel="stylesheet" href="orders.css">
<style>
.pay-wrap { max-width: 700px; margin: 40px auto; padding: 0 20px; font-family: Arial, sans-serif; }
.order-summary { background:#fff; border-radius:10px; padding:20px 25px; box-shadow:0 5px 15px rgba(0,0,0,0.1); margin-bottom:25px; }
.order-summary h3 { margin-top:0; color:#c0392b; }
.pay-methods { display:flex; flex-direction:column; gap:18px; }
.pay-card { background:#fff; border-radius:10px; padding:20px 25px; box-shadow:0 5px 15px rgba(0,0,0,0.08); }
.pay-card h4 { margin:0 0 10px 0; color:#333; }
.pay-card p.desc { color:#666; font-size:0.92rem; margin-bottom:14px; }
.pay-card button, .pay-card input[type=submit] {
  background:#c0392b; color:#fff; border:none; padding:12px 22px; border-radius:6px;
  font-size:1rem; cursor:pointer;
}
.pay-card button:hover { background:#96281b; }
.upi-box { display:flex; gap:20px; align-items:flex-start; flex-wrap:wrap; }
.upi-box img { border-radius:8px; border:1px solid #eee; }
.upi-ref-form input[type=text] { padding:10px; border-radius:6px; border:1px solid #ccc; width:100%; margin-bottom:10px; box-sizing:border-box; }
</style>
</head>
<body>

<nav>
    <div class="logo">🍴 Food Ordering System</div>
    <div class="links">
        <a href="home.php">Home</a>
        <a href="menu.php">Menu</a>
    </div>
</nav>

<div class="pay-wrap">

  <div class="order-summary">
    <h3>Order Summary</h3>
    <p><b>Dish:</b> <?= htmlspecialchars($order['dish_name']) ?> &times; <?= (int)$order['quantity'] ?></p>
    <p><b>Deliver to:</b> <?= htmlspecialchars($order['customer_name']) ?>, <?= htmlspecialchars($order['phone']) ?></p>
    <p><b>Address:</b> <?= htmlspecialchars($order['address']) ?></p>
    <p style="font-size:1.2rem;"><b>Total: <?= htmlspecialchars(SITE_CURRENCY_SYMBOL) ?> <?= number_format($total, 2) ?></b></p>
  </div>

  <h3>Choose a Payment Method</h3>
  <div class="pay-methods">

    <div class="pay-card">
      <h4>💵 Cash on Delivery</h4>
      <p class="desc">Pay in cash when your order arrives at your doorstep.</p>
      <form method="POST" action="process_cod.php">
        <button type="submit">Place Order (Cash on Delivery)</button>
      </form>
    </div>

    <div class="pay-card">
      <h4>💳 Pay by Card</h4>
      <p class="desc">Secure card payment powered by Stripe Checkout.</p>
      <form method="POST" action="create_stripe_session.php">
        <button type="submit">Pay <?= htmlspecialchars(SITE_CURRENCY_SYMBOL) ?> <?= number_format($total, 2) ?> with Card</button>
      </form>
    </div>

    <div class="pay-card">
      <h4>📱 UPI Payment</h4>
      <p class="desc">Scan the QR code or tap the link below with any UPI app, then enter your UPI reference number to confirm.</p>
      <div class="upi-box">
        <img src="<?= htmlspecialchars($qr_img) ?>" alt="UPI QR Code" width="180" height="180">
        <div style="flex:1; min-width:220px;">
          <p><a href="<?= htmlspecialchars($upi_link) ?>">Tap to pay with a UPI app</a> (on mobile)</p>
          <form method="POST" action="process_upi.php" class="upi-ref-form">
            <input type="text" name="upi_ref" placeholder="Enter UPI transaction ref. no." required>
            <button type="submit">I've Paid - Confirm Order</button>
          </form>
        </div>
      </div>
    </div>

  </div>
</div>

</body>
</html>
