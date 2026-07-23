<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config.php';

$total_orders = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) c FROM orders"))['c'];
$pending_orders = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) c FROM orders WHERE order_status NOT IN ('Delivered','Cancelled')"))['c'];
$total_revenue = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COALESCE(SUM(total),0) s FROM orders WHERE payment_status IN ('Paid','Pending (Cash)','Pending Verification')"))['s'];
$total_users = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) c FROM users"))['c'];
$total_menu_items = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) c FROM menu_items"))['c'];
$total_messages = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) c FROM contact_messages"))['c'];

$status_breakdown = ['Placed' => 0, 'Preparing' => 0, 'Out for Delivery' => 0, 'Delivered' => 0, 'Cancelled' => 0];
$status_result = mysqli_query($connect, "SELECT order_status, COUNT(*) c FROM orders GROUP BY order_status");
if ($status_result) {
    while ($row = mysqli_fetch_assoc($status_result)) {
        $status_breakdown[$row['order_status']] = (int) $row['c'];
    }
}
$max_status_count = max(1, max($status_breakdown));

$recent_orders = mysqli_query($connect, "SELECT * FROM orders ORDER BY id DESC LIMIT 5");

$active_page = 'dashboard';
require __DIR__ . '/includes/header.php';
?>

<div class="topbar">
  <h1>Dashboard</h1>
  <a class="logout" href="logout.php">Logout (<?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?>)</a>
</div>

<div class="card-stats">
  <div class="stat-card"><div class="stat-icon">🧾</div><div class="stat-text"><h3><?= $total_orders ?></h3><p>Total Orders</p></div></div>
  <div class="stat-card"><div class="stat-icon">⏳</div><div class="stat-text"><h3><?= $pending_orders ?></h3><p>In Progress</p></div></div>
  <div class="stat-card"><div class="stat-icon">💰</div><div class="stat-text"><h3 title="<?= htmlspecialchars(SITE_CURRENCY_SYMBOL) ?> <?= number_format($total_revenue,2) ?>"><?= htmlspecialchars(SITE_CURRENCY_SYMBOL) ?> <?= number_format($total_revenue,0) ?></h3><p>Revenue (incl. pending)</p></div></div>
  <div class="stat-card"><div class="stat-icon">👥</div><div class="stat-text"><h3><?= $total_users ?></h3><p>Registered Users</p></div></div>
  <div class="stat-card"><div class="stat-icon">🍔</div><div class="stat-text"><h3><?= $total_menu_items ?></h3><p>Menu Items</p></div></div>
  <div class="stat-card"><div class="stat-icon">✉️</div><div class="stat-text"><h3><?= $total_messages ?></h3><p>Contact Messages</p></div></div>
</div>

<div style="background:#fff; border-radius:14px; padding:24px 28px; box-shadow:0 4px 18px rgba(30,20,60,0.07); margin-bottom:28px;">
  <h2 style="margin-bottom:18px; font-size:1.05rem; color:#241f3a;">Order Status Breakdown</h2>
  <?php
    $bar_colors = ['Placed' => '#1565c0', 'Preparing' => '#e65100', 'Out for Delivery' => '#ff7043', 'Delivered' => '#2e7d32', 'Cancelled' => '#c62828'];
    foreach ($status_breakdown as $status => $count):
        $pct = round(($count / $max_status_count) * 100);
  ?>
    <div style="margin-bottom:14px;">
      <div style="display:flex; justify-content:space-between; font-size:0.85rem; color:#555; margin-bottom:4px;">
        <span><?= htmlspecialchars($status) ?></span><span><b><?= $count ?></b></span>
      </div>
      <div style="background:#f0f1f6; border-radius:6px; height:10px; overflow:hidden;">
        <div style="height:100%; width:0; background:<?= $bar_colors[$status] ?>; border-radius:6px;
                    animation: growBar 1s ease forwards; --grow-to: <?= $pct ?>%;"></div>
      </div>
    </div>
  <?php endforeach; ?>
  <style>@keyframes growBar { to { width: var(--grow-to); } }</style>
</div>

<h2 style="margin-bottom:12px; font-size:1.1rem; color:#241f3a;">Recent Orders</h2>
<table>
  <tr>
    <th>#</th><th>Dish</th><th>Customer</th><th>Total</th><th>Payment</th><th>Order Status</th><th>Date</th>
  </tr>
  <?php while($o = mysqli_fetch_assoc($recent_orders)):
      $status_badge = 'blue';
      if ($o['order_status'] === 'Delivered') $status_badge = 'green';
      elseif ($o['order_status'] === 'Cancelled') $status_badge = 'red';
      elseif ($o['order_status'] === 'Out for Delivery') $status_badge = 'orange';
      elseif ($o['order_status'] === 'Preparing') $status_badge = 'gray';
  ?>
  <tr>
    <td>#<?= $o['id'] ?></td>
    <td><?= htmlspecialchars($o['dish_name']) ?> &times; <?= $o['quantity'] ?></td>
    <td><?= htmlspecialchars($o['customer_name']) ?></td>
    <td><?= htmlspecialchars(SITE_CURRENCY_SYMBOL) ?> <?= number_format($o['total'],2) ?></td>
    <td><?= htmlspecialchars($o['payment_method']) ?> - <?= htmlspecialchars($o['payment_status']) ?></td>
    <td><span class="badge <?= $status_badge ?>"><?= htmlspecialchars($o['order_status']) ?></span></td>
    <td><?= htmlspecialchars($o['created_at']) ?></td>
  </tr>
  <?php endwhile; ?>
</table>

<?php require __DIR__ . '/includes/footer.php'; ?>

