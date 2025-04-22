<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php?type=admin');
    exit();
}

// Define ROOT_URL if not already defined
if (!defined('ROOT_URL')) {
    define('ROOT_URL', 'http://localhost/website_alumni');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ENSAF Admin' : 'ENSAF Admin'; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo ROOT_URL; ?>/assets/img/favicon.ico" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="<?php echo ROOT_URL; ?>/styles.css">
    
    <!-- Additional CSS -->
    <?php if (isset($extra_css)) echo $extra_css; ?>

    <style>
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background-color: #1B263B;
            color: white;
            padding: 20px 0;
        }
        
        .sidebar-header {
            padding: 0 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
        }
        
        .sidebar-menu li {
            margin-bottom: 5px;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: #800020;
        }
        
        .sidebar-menu i {
            margin-right: 10px;
            width: 20px;
        }
        
        .main-content {
            flex: 1;
            padding: 20px;
            background-color: #f4f4f4;
        }
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
    </style>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="sidebar-header">
                <h3>ENSAF Admin</h3>
                <p>Panneau d'administration</p>
            </div>
            
            <ul class="sidebar-menu">
                <li>
                    <a href="index.php" <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active"' : ''; ?>>
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="verify_users.php" <?php echo basename($_SERVER['PHP_SELF']) == 'verify_users.php' ? 'class="active"' : ''; ?>>
                        <i class="fas fa-user-check"></i>
                        Vérifier Utilisateurs
                    </a>
                </li>
                <li>
                    <a href="manage_events.php" <?php echo basename($_SERVER['PHP_SELF']) == 'manage_events.php' ? 'class="active"' : ''; ?>>
                        <i class="fas fa-calendar-alt"></i>
                        Événements
                    </a>
                </li>
                <li>
                    <a href="approve_opportunities.php" <?php echo basename($_SERVER['PHP_SELF']) == 'approve_opportunities.php' ? 'class="active"' : ''; ?>>
                        <i class="fas fa-briefcase"></i>
                        Opportunités
                    </a>
                </li>
                <li>
                    <a href="../index.php">
                        <i class="fas fa-home"></i>
                        Site public
                    </a>
                </li>
                <li>
                    <a href="../logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        Déconnexion
                    </a>
                </li>
            </ul>
        </div>
    </div>
</body>
</html>