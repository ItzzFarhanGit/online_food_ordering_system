<?php
session_start();
include 'db.php';

// Ordering requires an account so we know who placed it (for My Orders,
// cancellation, and status notifications).
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=orders.php");
    exit;
}

$result = mysqli_query($connect, "SELECT * FROM menu_items ORDER BY id ASC");
$dishes = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $dishes[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Online Food Ordering</title>
<link rel="Stylesheet" href="orders.css">
</head>
<body>
<?php include 'includes/order_toast.php'; ?>

<nav>
    <div class="logo">🍴 Food Ordering System</div>
    <div class="links">
        <a href="home.php">Home</a>
        <a href="menu.php">Menu</a>
        <a href="review.php">Review</a>
        <a href="contact.php">Contact</a>
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="my_orders.php">My Orders</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</nav>

<h2>Order Your Favorite Dish</h2>

<section class="menu">
<?php foreach($dishes as $index => $dish): ?>
    <div class="card" style="animation-delay: <?= 0.1*$index ?>s;">
        <img src="<?= htmlspecialchars($dish['image']) ?>" alt="<?= htmlspecialchars($dish['name']) ?>">
        <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($dish['name']) ?></h5>
            <p class="card-text"><?= htmlspecialchars($dish['description']) ?></p>
            <p class="price"><?= htmlspecialchars(SITE_CURRENCY_SYMBOL) ?> <?= number_format($dish['price'], 2) ?></p>

            <!-- Order Form Below Dish -->
            <form method="POST" action="payment.php">
                <input type="hidden" name="dish_name" value="<?= htmlspecialchars($dish['name']) ?>">
                <input type="hidden" name="price" value="<?= htmlspecialchars($dish['price']) ?>">

                <label>Quantity:</label>
                <input type="number" name="quantity" value="1" min="1" required>

                <label>Your Name:</label>
                <input type="text" name="customer_name" placeholder="Enter your name" required>

                <label>Phone:</label>
                <input type="text" name="phone" placeholder="Enter phone number" required>

                <label>Address:</label>
                <textarea name="address" rows="3" placeholder="Enter delivery address" required></textarea>

                <button type="submit">Proceed to Payment</button>
            </form>

        </div>
    </div>
<?php endforeach; ?>
</section>

</body>
</html>
