<?php
session_start();
include 'db.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($connect, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email == "" || $password == "") {
        $msg = "All fields are required!";
    } else {
        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($connect, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['fullname'];
                header("Location: home.php");
                exit;
            } else {
                $msg = "Invalid password!";
            }
        } else {
            $msg = "User not found!";
        }
    }
}

$reset_success = (isset($_GET['reset']) && $_GET['reset'] == "success");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Food Ordering System</title>
<link rel="Stylesheet" href="login.css">
<style>
.container {
 background: url("IMAGES/Login.jpg") no-repeat center center / cover !important;
}
</style>
</head>
<body>

<div class="nav">
  <div class="logo"><h2>🍴 Online Food Ordering</h2></div>
  <div class="navlinks">
    <ul>
      <li><a href="home.php">Home</a></li>
      <li><a href="menu.php">Menu</a></li>
      <li><a href="about.php">About</a></li>
      <li><a href="review.php">Review</a></li>
      <li><a href="contact.php">Contact</a></li>
      <li><a href="login.php">Login</a></li>
    </ul>
  </div>
</div>

<div class="container">
  <form action="" method="POST" class="login-form">
    <h2>Login</h2>

    <?php if($reset_success): ?>
      <p style="color: green; text-align:center;">Password Updated Successfully! Please Login.</p>
    <?php endif; ?>

    <?php if($msg != ""): ?>
      <div class="error"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <input type="email" name="email" placeholder="Enter Email" required>
    <input type="password" name="password" placeholder="Enter Password" required>
    <button type="submit" class="login">Login</button>

    <div class="forget">
      <p>Forgot Password? <a href="forgot.php">Click Here</a></p>
      <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
    </div>
  </form>
</div>

</body>
</html>
