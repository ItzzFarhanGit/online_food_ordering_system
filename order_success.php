<?php
session_start();
include 'db.php'; 

// Check if form is submitted
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dish_name = mysqli_real_escape_string($connect, $_POST['dish_name'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);
    $total = $price * $quantity;
    $customer_name = mysqli_real_escape_string($connect, $_POST['customer_name'] ?? '');
    $phone = mysqli_real_escape_string($connect, $_POST['phone'] ?? '');
    $address = mysqli_real_escape_string($connect, $_POST['address'] ?? '');

    // Save into session to display in success page
    $_SESSION['customer_name'] = $customer_name;
    $_SESSION['dish_name'] = $dish_name;

    // Insert into database
    $sql = "INSERT INTO orders (dish_name, price, quantity, total, customer_name, phone, address) 
            VALUES ('$dish_name','$price','$quantity','$total','$customer_name','$phone','$address')";
    $inserted = mysqli_query($connect, $sql);
    if(!$inserted){
        die("Error placing order: " . mysqli_error($connect));
    }
} else {
    // If page accessed directly without POST
    $customer_name = isset($_SESSION['customer_name']) ? $_SESSION['customer_name'] : "Customer";
    $dish_name = isset($_SESSION['dish_name']) ? $_SESSION['dish_name'] : "your dish";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Success</title>

<style>
body {
    margin: 0;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #ffecd2, #fcb69f);
    overflow: hidden;
    position: relative;
}

.firework {
    position: absolute;
    border-radius: 50%;
    opacity: 0.8;
    animation: explode linear infinite;
}

@keyframes explode {
    0% { transform: translateY(0) rotate(0deg); opacity:1; }
    100% { transform: translateY(120vh) rotate(360deg); opacity:0; }
}

.success-container {
    position: relative;
    z-index: 10;
    background: white;
    padding: 50px 40px;
    border-radius: 20px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    animation: popup 0.8s ease forwards;
}

@keyframes popup {
    0% { transform: scale(0.7); opacity: 0; }
    50% { transform: scale(1.1); opacity: 1; }
    100% { transform: scale(1); }
}

.success-icon {
    font-size: 80px;
    color: #4BB543;
    animation: bounce 1s ease infinite alternate;
}

@keyframes bounce {
    0% { transform: translateY(0); }
    100% { transform: translateY(-15px); }
}

h1 {
    font-size: 2.5rem;
    color: #333;
    margin: 20px 0 10px;
}

p {
    color: #555;
    font-size: 1.1rem;
    margin-bottom: 30px;
}

a {
    text-decoration: none;
    background: #ff7043;
    color: white;
    padding: 12px 30px;
    border-radius: 25px;
    font-weight: bold;
    transition: all 0.3s ease;
}
a:hover {
    background: #bf360c;
    transform: scale(1.05);
}
</style>

</head>
<body>

<?php
$colors = ['#ff4747','#ffd700','#4caf50','#2196f3','#ff69b4','#ff9800'];
for($i=0; $i<30; $i++) {
    $left = rand(0, 95);
    $top = rand(0, 80);
    $size = rand(8,15);
    $color = $colors[array_rand($colors)];
    $duration = rand(2,6);
    $delay = rand(0,5);
    echo '<div class="firework" style="
        left: '.$left.'%;
        top: '.$top.'%;
        width: '.$size.'px;
        height: '.$size.'px;
        background: '.$color.';
        animation-duration: '.$duration.'s;
        animation-delay: '.$delay.'s;
    "></div>';
}
?>


<div class="success-container">
    <div class="success-icon">✅</div>
    <h1>Order Placed Successfully!</h1>
    <p>Thank you <?= htmlspecialchars($customer_name) ?> for ordering <?= htmlspecialchars($dish_name) ?>.<br>
       Your food will be delivered soon.</p>
    <a href="orders.php">Back to Menu</a>
</div>
</body>
</html>
