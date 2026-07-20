<?php
session_start();
include 'db.php'; 

$msg = "";
$email = "";

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset'])){
    $email = mysqli_real_escape_string($connect, $_POST['email'] ?? '');

    if($email == ""){
        $msg = "Please enter your email!";
    } else {
        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($connect, $sql);

        if($result && mysqli_num_rows($result) > 0){
            // Email verified - send the user on to the reset password page
            header("Location: reset_password.php?email=" . urlencode($email));
            exit;
        } else {
            $msg = "Email not found in our records!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password</title>
<link rel="stylesheet" href="forgot.css">
</head>
<body>

<div class="forgot-container">
    <h2>Forgot Password</h2>

    <?php if($msg != ""): ?>
        <p class="error"><?php echo htmlspecialchars($msg); ?></p>
    <?php endif; ?>

    <form method="POST" action="forgot.php">
        <label for="email">Enter Your Registered Email</label>
        <input type="email" id="email" name="email" placeholder="Email Address" value="<?php echo htmlspecialchars($email); ?>" required>

        <button type="submit" name="reset" class="btn">Send Reset Link</button>
    </form>

    <p>Remembered Your Password? <a href="login.php">Login here</a></p>
    <p>Don’t have an Account? <a href="signup.php">Sign Up</a></p>
</div>

</body>
</html>
