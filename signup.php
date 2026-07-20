<?php
session_start();
include 'db.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = mysqli_real_escape_string($connect, trim($_POST['fullname'] ?? ''));
    $email = mysqli_real_escape_string($connect, trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if ($fullname == "" || $email == "" || $password == "") {
        $msg = "All fields required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "Please enter a valid email address!";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (fullname, email, password) VALUES ('$fullname','$email','$hashed')";
        if (mysqli_query($connect, $sql)) {
            header("Location: login.php");
            exit;
        } else if (mysqli_errno($connect) == 1062) {
            $msg = "An account with this email already exists!";
        } else {
            $msg = "Something went wrong. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign Up</title>
<link rel="Stylesheet" href="signup.css">
</head>
<body>

<div class="signup-container">
    <h2>Sign Up</h2>

    <?php if($msg != ""): ?>
        <p class="error"><?php echo htmlspecialchars($msg); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Full Name</label>
        <input type="text" name="fullname" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit" class="btn">Sign Up</button>
    </form>

    <p>Already have an account? <a href="login.php">Login</a></p>
</div>

</body>
</html>
