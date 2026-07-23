<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config.php';

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $id = (int) $_POST['order_id'];
        $status = mysqli_real_escape_string($connect, $_POST['order_status']);
        $allowed = ['Placed','Preparing','Out for Delivery','Delivered','Cancelled'];
        if (in_array($status, $allowed, true)) {
            mysqli_query($connect, "UPDATE orders SET order_status='$status', notified=0 WHERE id=$id");
            $msg = "Order #$id status updated to $status. The customer will see this update next time they visit.";
        }
    } elseif (isset($_POST['update_payment'])) {
        $id = (int) $_POST['order_id'];
        $pstatus = mysqli_real_escape_string($connect, $_POST['payment_status']);
        $allowed_pay = ['Pending (Cash)', 'Pending Verification', 'Paid', 'Failed'];
        if (in_array($pstatus, $allowed_pay, true)) {
            mysqli_query($connect, "UPDATE orders SET payment_status='$pstatus', notified=0 WHERE id=$id");
            $msg = "Order #$id payment marked as: $pstatus.";
        }
    } elseif (isset($_POST['delete_order'])) {
        $id = (int) $_POST['order_id'];
        mysqli_query($connect, "DELETE FROM orders WHERE id=$id");
        $msg = "Order #$id deleted.";
    }
}

$orders = mysqli_query($connect, "SELECT * FROM orders ORDER BY id DESC");

$active_page = 'orders';
require __DIR__ . '/includes/header.php';
?>

<div class="topbar">
  <h1>Orders</h1>
  <a class="logout" href="logout.php">Logout</a>
</div>

<?php if($msg): ?><div class="msg success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<table>
  <tr>
    <th>#</th><th>Dish</th><th>Customer</th><th>Phone</th><th>Address</th><th>Total</th>
    <th>Payment</th><th>Order Status</th><th>Date</th><th>Actions</th>
  </tr>
  <?php while($o = mysqli_fetch_assoc($orders)):
      $badge = 'gray';
      if ($o['payment_status'] === 'Paid') $badge = 'green';
      elseif ($o['payment_status'] === 'Pending Verification') $badge = 'orange';
      elseif (strpos($o['payment_status'], 'Pending') === 0) $badge = 'blue';

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
    <td><?= htmlspecialchars($o['phone']) ?></td>
    <td style="max-width:180px;"><?= htmlspecialchars($o['address']) ?></td>
    <td><?= htmlspecialchars(SITE_CURRENCY_SYMBOL) ?> <?= number_format($o['total'],2) ?></td>
    <td>
        <span class="badge <?= $badge ?>" style="margin-bottom:6px; display:inline-block;"><?= htmlspecialchars($o['payment_method']) ?> · <?= htmlspecialchars($o['payment_status']) ?></span>
        <?php if($o['transaction_ref']): ?><br><small>Ref: <?= htmlspecialchars($o['transaction_ref']) ?></small><?php endif; ?>
        <form method="POST" class="inline" style="display:block; margin-top:6px;">
          <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
          <select name="payment_status" onchange="this.form.submit()">
            <?php foreach(['Pending (Cash)','Pending Verification','Paid','Failed'] as $ps): ?>
              <option value="<?= $ps ?>" <?= $o['payment_status']===$ps?'selected':'' ?>><?= $ps ?></option>
            <?php endforeach; ?>
          </select>
          <input type="hidden" name="update_payment" value="1">
        </form>
    </td>
    <td>
      <span class="badge <?= $status_badge ?>" style="margin-bottom:6px; display:inline-block;"><?= htmlspecialchars($o['order_status']) ?></span>
      <form method="POST" class="inline">
        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
        <select name="order_status" onchange="this.form.submit()">
          <?php foreach(['Placed','Preparing','Out for Delivery','Delivered','Cancelled'] as $s): ?>
            <option value="<?= $s ?>" <?= $o['order_status']===$s?'selected':'' ?>><?= $s ?></option>
          <?php endforeach; ?>
        </select>
        <input type="hidden" name="update_status" value="1">
      </form>
    </td>
    <td><?= htmlspecialchars($o['created_at']) ?></td>
    <td>
      <form method="POST" class="inline" onsubmit="return confirm('Delete this order?');">
        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
        <button type="submit" name="delete_order" class="btn small">Delete</button>
      </form>
    </td>
  </tr>
  <?php endwhile; ?>
</table>

<?php require __DIR__ . '/includes/footer.php'; ?>
