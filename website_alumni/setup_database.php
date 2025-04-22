<?php
require_once 'config.php';

// Create tables if they don't exist
$tables = [
    // Users table
    "CREATE TABLE IF NOT EXISTS `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `email` VARCHAR(100) NOT NULL UNIQUE,
        `first_name` VARCHAR(50) NOT NULL,
        `last_name` VARCHAR(50) NOT NULL,
        `full_name` VARCHAR(100) NOT NULL,
        `role` ENUM('student', 'alumni', 'professor', 'admin') NOT NULL,
        `profile_image` VARCHAR(255) DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `last_login` TIMESTAMP NULL,
        `status` ENUM('active', 'inactive', 'pending') DEFAULT 'pending',
        `verification_status` ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
        `verification_document` VARCHAR(255) DEFAULT NULL,
        `bio` TEXT DEFAULT NULL
    )",

    // Departments table
    "CREATE TABLE IF NOT EXISTS `departments` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL,
        `short_code` VARCHAR(10) NOT NULL UNIQUE,
        `description` TEXT DEFAULT NULL,
        `head_professor_id` INT DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`head_professor_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
    )",

    // News table
    "CREATE TABLE IF NOT EXISTS `news` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `content` TEXT NOT NULL,
        `image_url` VARCHAR(255) DEFAULT NULL,
        `published_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `author_id` INT DEFAULT NULL,
        `is_featured` TINYINT(1) DEFAULT 0,
        `views` INT DEFAULT 0,
        FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
    )",

    // Events table
    "CREATE TABLE IF NOT EXISTS `events` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `description` TEXT NOT NULL,
        `event_date` DATE NOT NULL,
        `event_time` TIME NOT NULL,
        `end_date` DATE DEFAULT NULL,
        `end_time` TIME DEFAULT NULL,
        `location` VARCHAR(255) NOT NULL,
        `image_url` VARCHAR(255) DEFAULT NULL,
        `organizer_id` INT DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `status` ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
        `approved_by` INT DEFAULT NULL,
        `approval_date` TIMESTAMP NULL,
        `type` ENUM('conference', 'workshop', 'career_fair', 'social', 'academic', 'other') DEFAULT 'other',
        `max_participants` INT DEFAULT NULL,
        FOREIGN KEY (`organizer_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
        FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
    )",

    // Event Registrations table
    "CREATE TABLE IF NOT EXISTS `event_registrations` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `event_id` INT NOT NULL,
        `user_id` INT NOT NULL,
        `registration_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `status` ENUM('registered', 'attended', 'cancelled') DEFAULT 'registered',
        FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
        UNIQUE KEY `unique_registration` (`event_id`, `user_id`)
    )",

    // Opportunities table 
    "CREATE TABLE IF NOT EXISTS `opportunities` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `company` VARCHAR(255) NOT NULL,
        `description` TEXT NOT NULL,
        `type` ENUM('internship', 'job', 'project', 'training', 'other') NOT NULL,
        `location` VARCHAR(255) NOT NULL,
        `deadline` DATE DEFAULT NULL,
        `requirements` TEXT DEFAULT NULL,
        `contact_email` VARCHAR(100) DEFAULT NULL,
        `contact_phone` VARCHAR(50) DEFAULT NULL,
        `contact_person` VARCHAR(100) DEFAULT NULL,
        `url` VARCHAR(255) DEFAULT NULL,
        `salary_range` VARCHAR(100) DEFAULT NULL, 
        `created_by` INT DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `status` ENUM('pending', 'approved', 'rejected', 'expired') DEFAULT 'pending',
        `approved_by` INT DEFAULT NULL,
        `approval_date` TIMESTAMP NULL,
        `department_id` INT DEFAULT NULL,
        FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
        FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
        FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE SET NULL
    )",

    // Alumni table
    "CREATE TABLE IF NOT EXISTS `alumni` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL UNIQUE,
        `graduation_year` INT NOT NULL,
        `department_id` INT NOT NULL,
        `current_position` VARCHAR(255) DEFAULT NULL,
        `current_company` VARCHAR(255) DEFAULT NULL,
        `linkedin` VARCHAR(255) DEFAULT NULL,
        `github` VARCHAR(255) DEFAULT NULL,
        `twitter` VARCHAR(255) DEFAULT NULL,
        `website` VARCHAR(255) DEFAULT NULL,
        `is_highlighted` TINYINT(1) DEFAULT 0,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE CASCADE
    )",

    // Testimonials table
    "CREATE TABLE IF NOT EXISTS `testimonials` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT DEFAULT NULL,
        `content` TEXT NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        `approved_by` INT DEFAULT NULL,
        `approval_date` TIMESTAMP NULL,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
        FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
    )",

    // Gallery table
    "CREATE TABLE IF NOT EXISTS `gallery` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `description` TEXT DEFAULT NULL,
        `image_url` VARCHAR(255) NOT NULL,
        `upload_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `event_id` INT DEFAULT NULL,
        `uploaded_by` INT DEFAULT NULL,
        `is_featured` TINYINT(1) DEFAULT 0,
        FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE SET NULL,
        FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
    )",

    // Notifications table
    "CREATE TABLE IF NOT EXISTS `notifications` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `title` VARCHAR(255) NOT NULL,
        `message` TEXT NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `read_at` TIMESTAMP NULL,
        `type` ENUM('system', 'approval', 'event', 'opportunity', 'general') DEFAULT 'general',
        `related_id` INT DEFAULT NULL,
        `status` ENUM('unread', 'read') DEFAULT 'unread',
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    )"
];

// Execute each table creation query
foreach ($tables as $sql) {
    if (mysqli_query($conn, $sql)) {
        echo "Table created successfully or already exists.<br>";
    } else {
        echo "Error creating table: " . mysqli_error($conn) . "<br>";
    }
}

// Check if admin user exists, if not create one
$admin_check = "SELECT * FROM `users` WHERE `role` = 'admin' LIMIT 1";
$result = mysqli_query($conn, $admin_check);

if (mysqli_num_rows($result) == 0) {
    // Create default admin user
    $admin_username = 'admin';
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT); // Use a secure password in production
    $admin_email = 'admin@ensaf.ac.ma';
    
    $admin_sql = "INSERT INTO `users` (`username`, `password`, `email`, `first_name`, `last_name`, `role`, `status`) 
                 VALUES ('$admin_username', '$admin_password', '$admin_email', 'Admin', 'User', 'admin', 'active')";
    
    if (mysqli_query($conn, $admin_sql)) {
        echo "Default admin user created successfully.<br>";
    } else {
        echo "Error creating admin user: " . mysqli_error($conn) . "<br>";
    }
}

// Insert some default departments if none exist
$dept_check = "SELECT * FROM `departments` LIMIT 1";
$result = mysqli_query($conn, $dept_check);

if (mysqli_num_rows($result) == 0) {
    $departments = [
        ['name' => 'Génie Informatique', 'short_code' => 'GI'],
        ['name' => 'Génie Mécanique', 'short_code' => 'GM'],
        ['name' => 'Génie Énergétique et Systèmes Intelligents', 'short_code' => 'GESI'],
        ['name' => 'Génie Mécatronique', 'short_code' => 'GMeca'],
        ['name' => 'Génie Industriel', 'short_code' => 'GIn'],
        ['name' => 'Génie du Développement Numérique et Cybersécurité', 'short_code' => 'GDNC'],
        ['name' => 'Ingénierie en Science de Données et Intelligence Artificielle', 'short_code' => 'ISDIA']
    ];
    
    foreach ($departments as $dept) {
        $dept_sql = "INSERT INTO `departments` (`name`, `short_code`) VALUES ('{$dept['name']}', '{$dept['short_code']}')";
        if (mysqli_query($conn, $dept_sql)) {
            echo "Department {$dept['name']} created successfully.<br>";
        } else {
            echo "Error creating department: " . mysqli_error($conn) . "<br>";
        }
    }
}

echo "<h3>Database setup completed!</h3>";
echo "<p><a href='index.php'>Return to homepage</a></p>";
?> 