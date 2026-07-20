<?php
session_start();
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Online Food Ordering Menu</title>
<link rel="Stylesheet" href="menu.css">
</head>
<body>

<nav>
  <div class="logo">🍴 Online Food Ordering</div>
  <div class="links">
    <a href="home.php">HOME</a>
    <a href="menu.php">MENU</a>
    <a href="about.php">ABOUT</a>
    <a href="review.php">REVIEW</a>
    <a href="contact.php">CONTACT</a>

    <?php if(isset($_SESSION['user_id'])){ ?>
      <a href="logout.php">LOGOUT</a>
    <?php }else{ ?>
      <a href="login.php">LOGIN</a>
    <?php } ?>
  </div>
</nav>

<h2>Our Popular Dishes</h2>

<section class="menu">

<div class="card">
  <img src="IMAGES/Burger.jpg">
  <div class="card-body">
    <h5 class="card-title">Cheese Burger</h5>
    <p class="price">Rs.1200</p>
    <a href="orders.php"><button class="btn">Order Now</button></a>
  </div>
</div>

<div class="card">
  <img src="IMAGES/Pizza.jpg">
  <div class="card-body">
    <h5 class="card-title">Veg Loaded Pizza</h5>
    <p class="price">Rs.1800</p>
    <a href="orders.php"><button class="btn">Order Now</button></a>
  </div>
</div>

<div class="card">
  <img src="IMAGES/Chicken Biryani.jpg">
  <div class="card-body">
    <h5 class="card-title">Chicken Biryani</h5>
    <p class="price">Rs.1500</p>
    <a href="orders.php"><button class="btn">Order Now</button></a>
  </div>
</div>

<div class="card">
  <img src="IMAGES/Chicken Shawarma.jpg">
  <div class="card-body">
    <h5 class="card-title">Chicken Shawarma</h5>
    <p class="price">Rs.900</p>
    <a href="orders.php"><button class="btn">Order Now</button></a>
  </div>
</div>

<div class="card">
  <img src="IMAGES/Veg Noodles.jpg">
  <div class="card-body">
    <h5 class="card-title">Veg Noodles</h5>
    <p class="price">Rs.850</p>
    <a href="orders.php"><button class="btn">Order Now</button></a>
  </div>
</div>

</section>

<footer>
© 2025 Food Delight. All Rights Reserved. | Designed By <b>Mohamed Farhan</b>
</footer>

</body>
</html>
