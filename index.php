<?php
// Redirect root to login page when not authenticated; otherwise go to dashboard
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
} else {
    header('Location: views/auth/login.php');
}
exit;
