<?php
// Simple auth check to protect pages. Include at the top of pages that require authentication.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// If not logged in, redirect to login page. Use relative path heuristics so this include works
// both from root files and from files inside `views/`.
if (empty($_SESSION['user_id'])) {
    $redirect = (strpos($_SERVER['PHP_SELF'], '/views/') !== false) ? '../../views/auth/login.php' : 'views/auth/login.php';
    header('Location: ' . $redirect);
    exit();
}
