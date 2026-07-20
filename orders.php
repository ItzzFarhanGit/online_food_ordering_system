<?php
session_start();
include 'db.php';

$dishes = [
    ['name'=>'Cheese Burger','img'=>'IMAGES/Burger.jpg','desc'=>'Juicy grilled patty with melted cheese, lettuce & tomato.','price'=>1200],
    ['name'=>'Veg Loaded Pizza','img'=>'IMAGES/Pizza.jpg','desc'=>'Loaded with cheese, capsicum, onion and olives.','price'=>1800],
    ['name'=>'Chicken Biryani','img'=>'IMAGES/Chicken Biryani.jpg','desc'=>'Aromatic rice cooked with tender chicken and spices.','price'=>1500],
    ['name'=>'Chicken Shawarma','img'=>'IMAGES/Chicken Shawarma.jpg','desc'=>'Stuffed with spicy chicken and creamy garlic mayo.','price'=>900],
    ['name'=>'Veg Noodles','img'=>'IMAGES/Veg Noodles.jpg','desc'=>'Hot & spicy noodles with crunchy vegetables.','price'=>850],
    ['name'=>'Fresh Orange Juice','img'=>'IMAGES/Fresh Orange juice.jpg','desc'=>'100% pure and freshly squeezed orange juice.','price'=>400],
];
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

<nav>
    <div class="logo">🍴 Food Ordering System</div>
    <div class="links">
        <a href="home.php">Home</a>
        <a href="menu.php">Menu</a>
        <a href="review.php">Review</a>
        <a href="contact.php">Contact</a>
    </div>
</nav>

<h2>Order Your Favorite Dish</h2>

<section class="menu">
<?php foreach($dishes as $index => $dish): ?>
    <div class="card" style="animation-delay: <?= 0.1*$index ?>s;">
        <img src="<?= $dish['img'] ?>" alt="<?= $dish['name'] ?>">
        <div class="card-body">
            <h5 class="card-title"><?= $dish['name'] ?></h5>
            <p class="card-text"><?= $dish['desc'] ?></p>
            <p class="price">Rs. <?= $dish['price'] ?></p>

            <!-- Order Form Below Dish -->
            <form method="POST" action="order_success.php">
                <input type="hidden" name="dish_name" value="<?= $dish['name'] ?>">
                <input type="hidden" name="price" value="<?= $dish['price'] ?>">

                <label>Quantity:</label>
                <input type="number" name="quantity" value="1" min="1" required>

                <label>Your Name:</label>
                <input type="text" name="customer_name" placeholder="Enter your name" required>

                <label>Phone:</label>
                <input type="text" name="phone" placeholder="Enter phone number" required>

                <label>Address:</label>
                <textarea name="address" rows="3" placeholder="Enter delivery address" required></textarea>

                <button type="submit">Place Order</button>
            </form>

            

        </div>
    </div>
<?php endforeach; ?>
</section>

</body>
</html>
