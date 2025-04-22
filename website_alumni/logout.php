<?php
require_once 'config.php';

// Delete remember me token if it exists
if (isset($_COOKIE['remember_user']) && isset($_COOKIE['remember_token'])) {
    $user_id = $_COOKIE['remember_user'];
    $token = $_COOKIE['remember_token'];
    
    // Delete token from database
    $stmt = mysqli_prepare($conn, "DELETE FROM user_tokens WHERE user_id = ? AND token = ?");
    mysqli_stmt_bind_param($stmt, "is", $user_id, $token);
    mysqli_stmt_execute($stmt);
    
    // Delete cookies
    setcookie("remember_user", "", time() - 3600, "/");
    setcookie("remember_token", "", time() - 3600, "/");
}

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to home page
header("Location: index.php");
exit();
?> 