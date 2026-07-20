<?php
session_start();
include 'db.php';

$msg = "";
$prefill_email = isset($_GET['email']) ? trim($_GET['email']) : '';

if(isset($_POST['change_password'])){
    $email = mysqli_real_escape_string($connect, $_POST['email'] ?? '');
    $new = mysqli_real_escape_string($connect, $_POST['new_password'] ?? '');
    $confirm = mysqli_real_escape_string($connect, $_POST['confirm_password'] ?? '');

    if($email == "" || $new == "" || $confirm == ""){
        $msg = "All fields are required!";
    } 
    else if($new != $confirm){
        $msg = "Passwords do not match!";
    } 
    else {
        // Check email exists
        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($connect, $sql);

        if(mysqli_num_rows($result) > 0){

            // Update password
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $update = "UPDATE users SET password='$hashed' WHERE email='$email'";
            mysqli_query($connect, $update);

            // REDIRECT TO LOGIN PAGE
            header("Location: login.php?reset=success");
            exit;

        } 
        else {
            $msg = "Email not found!";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password</title>
<link rel="stylesheet" href="reset_password.css">
</head>
<body>

<div class="navbar">
    <ul>
        <li><a href="login.php">LOGIN</a></li>
        <li><a href="signup.php">SIGNUP</a></li>
    </ul>
</div>

<div class="forgot-container">
    <h2>Reset Password</h2>

    <?php if($msg != ""): ?>
        <p class="error"><?php echo htmlspecialchars($msg); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        
        <label for="email">Enter Your Registered Email</label>
        <input type="email" id="email" name="email" placeholder="Email Address" value="<?php echo htmlspecialchars($prefill_email); ?>" required>

        <label for="new_password">New Password</label>
        <input type="password" id="new_password" name="new_password" placeholder="New Password" required>

        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>

        <button type="submit" name="change_password" class="btn">Update Password</button>
    </form>

    <p>Remembered Your Password? <a href="login.php">Login here</a></p>
    <p>Don’t have an Account? <a href="signup.php">Sign Up</a></p>
</div>

</body>
</html>
