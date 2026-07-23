<?php
// Shared top nav bar for auth pages (signup, forgot, reset, verify OTP)
// so every page in the flow looks consistent. Include after session_start().
?>
<div class="nav">
  <div class="logo"><h2>🍴 Online Food Ordering</h2></div>
  <div class="navlinks">
    <ul>
      <li><a href="home.php">Home</a></li>
      <li><a href="menu.php">Menu</a></li>
      <li><a href="about.php">About</a></li>
      <li><a href="review.php">Review</a></li>
      <li><a href="contact.php">Contact</a></li>
      <?php if(isset($_SESSION['user_id'])): ?>
        <li><a href="my_orders.php">My Orders</a></li>
        <li><a href="logout.php">Logout</a></li>
      <?php else: ?>
        <li><a href="login.php">Login</a></li>
      <?php endif; ?>
    </ul>
  </div>
</div>

<?php include __DIR__ . '/order_toast.php'; ?>
