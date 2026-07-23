<?php
/**
 * Minimal autoloader - loads the bundled PHPMailer library so real OTP
 * emails can be sent without needing Composer installed on your host.
 * (PHPMailer files are in vendor/phpmailer/phpmailer/src/)
 */
require_once __DIR__ . '/phpmailer/phpmailer/src/Exception.php';
require_once __DIR__ . '/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/phpmailer/phpmailer/src/SMTP.php';
