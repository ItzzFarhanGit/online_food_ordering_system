<?php
session_start();
include 'db.php';
include 'includes/otp.php';

$purpose = ($_GET['purpose'] ?? $_POST['purpose'] ?? 'signup') === 'reset' ? 'reset' : 'signup';
$session_key = ($purpose === 'reset') ? 'pending_reset_email' : 'pending_verify_email';

$email = $_SESSION[$session_key] ?? '';
$msg = "";
$dev_otp = $_SESSION['dev_otp_code'] ?? '';

if ($email === '') {
    // Nothing to verify - send them back to the right starting point
    header("Location: " . ($purpose === 'reset' ? 'forgot.php' : 'signup.php'));
    exit;
}

// Handle resend
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend'])) {
    $code = create_otp($connect, $email, $purpose);
    $sent = send_otp_email($email, $code, $purpose);
    if (!$sent) {
        $_SESSION['dev_otp_code'] = $code;
        $dev_otp = $code;
    } else {
        unset($_SESSION['dev_otp_code']);
        $dev_otp = '';
    }
    $msg = "A new code has been sent to $email.";
}

// Handle verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify'])) {
    $submitted = trim($_POST['otp'] ?? '');

    if ($submitted === '') {
        $msg = "Please enter the code we sent you.";
    } elseif (verify_otp($connect, $email, $submitted, $purpose)) {

        if ($purpose === 'signup') {
            $email_esc = mysqli_real_escape_string($connect, $email);
            mysqli_query($connect, "UPDATE users SET is_verified=1 WHERE email='$email_esc'");
            unset($_SESSION['pending_verify_email'], $_SESSION['dev_otp_code']);
            header("Location: login.php?verified=1");
            exit;
        } else {
            unset($_SESSION['pending_reset_email'], $_SESSION['dev_otp_code']);
            $_SESSION['reset_ok_email'] = $email;
            $_SESSION['reset_ok_time'] = time();
            header("Location: reset_password.php");
            exit;
        }

    } else {
        $msg = "Invalid or expired code. Please try again or resend.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify OTP</title>
<link rel="stylesheet" href="forgot.css">
<style>
.otp-input {
  letter-spacing: 10px;
  font-size: 1.4rem;
  text-align: center;
  width: 100%;
  box-sizing: border-box;
}
.dev-note {
  background: #fff3cd;
  color: #664d03;
  border: 1px solid #ffe69c;
  padding: 10px;
  border-radius: 6px;
  margin-bottom: 12px;
  font-size: 0.95rem;
  text-align: center;
}
.resend-btn {
  background: none;
  border: none;
  color: #0d6efd;
  text-decoration: underline;
  cursor: pointer;
  font-size: 0.95rem;
  padding: 0;
}
</style>
</head>
<body>

<?php include 'includes/site_nav.php'; ?>

<div class="page-content">
<div class="forgot-container">
    <h2>Verify Your Email</h2>
    <p style="text-align:center; margin-bottom: 15px;">
        We sent a 6-digit code to <b><?php echo htmlspecialchars($email); ?></b>
    </p>

    <?php if($dev_otp !== ''): ?>
        <div class="dev-note">
            <b>DEV MODE</b> - real email sending isn't configured yet (see <code>config.php</code>).<br>
            Your code is: <b style="font-size:1.3rem;"><?php echo htmlspecialchars($dev_otp); ?></b>
        </div>
    <?php endif; ?>

    <?php if($msg != ""): ?>
        <p class="error"><?php echo htmlspecialchars($msg); ?></p>
    <?php endif; ?>

    <form method="POST" action="verify_otp.php?purpose=<?php echo htmlspecialchars($purpose); ?>">
        <input type="hidden" name="purpose" value="<?php echo htmlspecialchars($purpose); ?>">
        <label for="otp">Enter 6-digit code</label>
        <input type="text" id="otp" name="otp" class="otp-input" maxlength="6" pattern="[0-9]{6}" placeholder="------" required>
        <button type="submit" name="verify" class="btn">Verify Code</button>
    </form>

    <form method="POST" action="verify_otp.php?purpose=<?php echo htmlspecialchars($purpose); ?>" style="margin-top:10px; text-align:center;">
        <input type="hidden" name="purpose" value="<?php echo htmlspecialchars($purpose); ?>">
        <button type="submit" name="resend" class="resend-btn">Didn't get a code? Resend</button>
    </form>

    <p style="margin-top:15px;">Remembered your password? <a href="login.php">Login here</a></p>
</div>
</div>

</body>
</html>
