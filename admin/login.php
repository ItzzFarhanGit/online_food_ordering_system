<?php
// Kept for backward compatibility - admin login now lives on the main
// login page (with a User/Admin tab toggle) so both share one UI.
session_start();
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}
header("Location: ../login.php?type=admin");
exit;
