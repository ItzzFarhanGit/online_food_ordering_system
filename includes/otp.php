<?php
/**
 * OTP helper functions - used by signup verification and forgot-password flow.
 * Requires config.php ($connect) to already be included.
 */

// Generate a 6-digit numeric OTP
function generate_otp_code() {
    return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

/**
 * Create and store a new OTP for the given email + purpose ('signup' or 'reset').
 * Any previous unused OTPs for the same email+purpose are invalidated first.
 * Returns the plain OTP code (so it can be emailed / shown in dev mode).
 */
function create_otp($connect, $email, $purpose) {
    $email_esc = mysqli_real_escape_string($connect, $email);
    $purpose_esc = mysqli_real_escape_string($connect, $purpose);

    // Invalidate older codes for this email/purpose
    mysqli_query($connect, "UPDATE otps SET used=1 WHERE email='$email_esc' AND purpose='$purpose_esc' AND used=0");

    $code = generate_otp_code();
    $hash = password_hash($code, PASSWORD_DEFAULT);
    $expires_at = date('Y-m-d H:i:s', time() + 10 * 60); // valid for 10 minutes

    $sql = "INSERT INTO otps (email, otp_hash, purpose, expires_at, used, created_at)
            VALUES ('$email_esc', '$hash', '$purpose_esc', '$expires_at', 0, NOW())";
    mysqli_query($connect, $sql);

    return $code;
}

/**
 * Verify a submitted OTP for the given email + purpose.
 * Returns true and marks it used on success, false otherwise.
 */
function verify_otp($connect, $email, $submitted_code, $purpose) {
    $email_esc = mysqli_real_escape_string($connect, $email);
    $purpose_esc = mysqli_real_escape_string($connect, $purpose);

    $sql = "SELECT * FROM otps
            WHERE email='$email_esc' AND purpose='$purpose_esc' AND used=0
            ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($connect, $sql);

    if (!$result || mysqli_num_rows($result) === 0) {
        return false;
    }

    $row = mysqli_fetch_assoc($result);

    if (strtotime($row['expires_at']) < time()) {
        return false; // expired
    }

    if (!password_verify($submitted_code, $row['otp_hash'])) {
        return false; // wrong code
    }

    mysqli_query($connect, "UPDATE otps SET used=1 WHERE id=" . (int) $row['id']);
    return true;
}

/**
 * Send an OTP email. Tries PHPMailer (if installed via Composer and
 * SEND_REAL_EMAILS is true). Falls back to PHP's mail(). If both are
 * unavailable/fail, returns false so the caller can show the code in
 * a "DEV MODE" banner instead - the flow keeps working either way.
 */
function send_otp_email($to_email, $code, $purpose) {
    $subject = ($purpose === 'signup')
        ? SITE_NAME . " - Verify your email"
        : SITE_NAME . " - Password reset code";

    $body = "Your " . SITE_NAME . " verification code is: $code\n\n"
          . "This code expires in 10 minutes. If you did not request this, "
          . "you can safely ignore this email.";

    if (!SEND_REAL_EMAILS) {
        return false;
    }

    // Try PHPMailer if the user has run `composer require phpmailer/phpmailer`
    $autoload = __DIR__ . '/../vendor/autoload.php';
    if (file_exists($autoload)) {
        require_once $autoload;
        if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            try {
                $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = SMTP_HOST;
                $mail->SMTPAuth = true;
                $mail->Username = SMTP_USERNAME;
                $mail->Password = SMTP_PASSWORD;
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = SMTP_PORT;

                $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
                $mail->addAddress($to_email);
                $mail->Subject = $subject;
                $mail->Body = $body;

                $mail->send();
                return true;
            } catch (\Throwable $e) {
                // fall through to mail() fallback below
            }
        }
    }

    // Fallback: PHP's built-in mail() (works if the server has sendmail/SMTP configured)
    $headers = "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
    return @mail($to_email, $subject, $body, $headers);
}
