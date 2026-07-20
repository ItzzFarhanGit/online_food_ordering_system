<?php
session_start();
include 'db.php'; // Database connection

$msg = "";

// Handle review form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($connect, $_POST['name'] ?? '');
    $review = mysqli_real_escape_string($connect, $_POST['review'] ?? '');
    $stars = (int)($_POST['stars'] ?? 5);
    $stars = max(1, min(5, $stars));

    if ($name == "" || $review == "") {
        $msg = "Please fill in all fields!";
    } else {
        $sql = "INSERT INTO reviews (name, review, stars) VALUES ('$name', '$review', '$stars')";
        if (mysqli_query( $connect, $sql)) {
            $msg = "Review submitted successfully!";
        } else {
            $msg = "Error: " . mysqli_error($connect);
        }
    }
}

// Fetch all reviews (including the new one immediately)
$reviews = mysqli_query($connect, "SELECT * FROM reviews ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Reviews | Food Ordering System</title>
<link rel="Stylesheet" href="review.css">
</head>
<body>
<div class="overlay">
    <h2>✨ Customer Reviews ✨</h2>

    <div class="review-form">
      <h3>Leave a Review</h3>
      <?php if($msg != ""): ?>
          <p style="color:green;text-align:center;margin-bottom:15px;"><?php echo htmlspecialchars($msg); ?></p>
      <?php endif; ?>
      <form method="POST" action="">
        <label>Your Name</label>
        <input type="text" name="name" placeholder="Enter Your Name" required>

        <label>Your Review</label>
        <textarea name="review" rows="4" placeholder="Write Your Review..." required></textarea>

        <label>Rating</label>
        <select name="stars" required>
            <option value="5">⭐⭐⭐⭐⭐</option>
            <option value="4">⭐⭐⭐⭐☆</option>
            <option value="3">⭐⭐⭐☆☆</option>
            <option value="2">⭐⭐☆☆☆</option>
            <option value="1">⭐☆☆☆☆</option>
        </select>

        <button type="submit">Submit Review</button>
      </form>
    </div>
    
    <div class="review-section">
      <h3>What Our Customers Say 💬</h3>

      <?php while($row = mysqli_fetch_assoc($reviews)): ?>
      <div class="review-item">
        <h4><?php echo htmlspecialchars($row['name']); ?></h4>
        <p class="stars"><?php echo str_repeat("⭐", $row['stars']) . str_repeat("☆", 5-$row['stars']); ?></p>
        <p><?php echo htmlspecialchars($row['review']); ?></p>
      </div>
      <?php endwhile; ?>

    </div>
</div>
</body>
</html>
