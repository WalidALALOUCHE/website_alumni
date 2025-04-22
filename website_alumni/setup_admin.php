<?php
require_once 'config.php';

// Check if admin user already exists
$stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE role = 'admin' LIMIT 1");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    // Create admin user
    $username = 'admin';
    $email = 'admin@ensaf.edu';
    $password = 'Admin@123'; // You should change this after first login
    $full_name = 'Administrator';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $first_name = 'Admin';
    $last_name = 'User';

    // First delete any existing admin user to avoid conflicts
    mysqli_query($conn, "DELETE FROM users WHERE username = 'admin' OR email = 'admin@ensaf.edu'");

    $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password, full_name, first_name, last_name, role, status) VALUES (?, ?, ?, ?, ?, ?, 'admin', 'active')");
    mysqli_stmt_bind_param($stmt, "ssssss", $username, $email, $hashed_password, $full_name, $first_name, $last_name);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "Admin user created successfully!<br>";
        echo "Username: admin<br>";
        echo "Password: Admin@123<br>";
        echo "<strong>Please change your password after first login.</strong><br>";
        echo "<a href='login.php?type=admin'>Click here to login</a>";
    } else {
        echo "Error creating admin user: " . mysqli_error($conn);
    }
} else {
    echo "Admin user already exists. <a href='login.php?type=admin'>Click here to login</a>";
}
?>