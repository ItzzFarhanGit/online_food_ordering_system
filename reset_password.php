<?php
session_start();
include 'db.php';

$msg = "";

// Must have completed OTP verification first (see verify_otp.php)
$verified_email = $_SESSION['reset_ok_email'] ?? '';
$verified_time = $_SESSION['reset_ok_time'] ?? 0;

if ($verified_email === '' || (time() - $verified_time) > 15 * 60) {
    unset($_SESSION['reset_ok_email'], $_SESSION['reset_ok_time']);
    header("Location: forgot.php");
    exit;
}

if(isset($_POST['change_password'])){
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if($new == "" || $confirm == ""){
        $msg = "All fields are required!";
    }
    else if(strlen($new) < 6){
        $msg = "Password must be at least 6 characters!";
    }
    else if($new != $confirm){
        $msg = "Passwords do not match!";
    }
    else {
        $email_esc = mysqli_real_escape_string($connect, $verified_email);
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $update = "UPDATE users SET password='$hashed' WHERE email='$email_esc'";
        mysqli_query($connect, $update);

        unset($_SESSION['reset_ok_email'], $_SESSION['reset_ok_time']);

        header("Location: login.php?reset=success");
        exit;
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

<?php include 'includes/site_nav.php'; ?>

<div class="page-content">
<div class="forgot-container">
    <h2>Reset Password</h2>
    <p style="text-align:center; margin-bottom:10px;">Resetting password for <b><?php echo htmlspecialchars($verified_email); ?></b></p>

    <?php if($msg != ""): ?>
        <p class="error"><?php echo htmlspecialchars($msg); ?></p>
    <?php endif; ?>

    <form method="POST" action="">

        <label for="new_password">New Password</label>
        <input type="password" id="new_password" name="new_password" placeholder="New Password" minlength="6" required>

        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" minlength="6" required>

        <button type="submit" name="change_password" class="btn">Update Password</button>
    </form>

    <p>Remembered Your Password? <a href="login.php">Login here</a></p>
    <p>Don't have an Account? <a href="signup.php">Sign Up</a></p>
</div>
</div>

</body>
</html>
