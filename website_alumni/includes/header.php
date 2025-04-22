<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get current page for active navigation
$current_page = basename($_SERVER['PHP_SELF']);

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';

// Define ROOT_URL only if not already defined
if (!defined('ROOT_URL')) {
    define('ROOT_URL', 'http://localhost/website_alumni');
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <title><?php echo isset($page_title) ? $page_title . ' - ENSAF' : 'ENSAF Alumni - Réseau des diplômés de l\'École Nationale des Sciences Appliquées de Fès'; ?></title>
    <meta name="description" content="Le réseau officiel des anciens étudiants de l'ENSAF. Retrouvez vos camarades de promotion, participez à des événements et accédez à des opportunités professionnelles.">
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo ROOT_URL; ?>/assets/img/favicon.ico" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="<?php echo ROOT_URL; ?>/styles.css">
    
    <!-- Additional CSS -->
    <?php if (isset($extra_css)) echo $extra_css; ?>

    <!-- Main JavaScript -->
    <script src="<?php echo ROOT_URL; ?>/script.js" defer></script>
</head>
<body>
    <!-- Accessibility Skip Link -->
    <a href="#main-content" class="skip-link">Passer au contenu principal</a>
    
    <!-- Page Loader -->
    <div class="page-loader">
        <div class="loader"></div>
    </div>
    
    <header>
        <div class="header-container">
            <div class="logo">
                <a href="<?php echo ROOT_URL; ?>">
                    <img src="<?php echo ROOT_URL; ?>/assets/img/logo-ensaf.png" alt="Logo ENSAF">
                </a>
                <div class="logo-text">
                    <h1>ENSAF</h1>
                    <p>École Nationale des Sciences Appliquées de Fès</p>
                </div>
            </div>
            
            <div class="header-actions">
                <button class="hamburger" aria-label="Menu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>
    
    <nav id="main-nav">
        <ul>
            <li><a href="<?php echo ROOT_URL; ?>" <?php if($current_page == 'index.php') echo 'class="active"'; ?>>Accueil</a></li>
            <li><a href="<?php echo ROOT_URL; ?>/formations.php" <?php if($current_page == 'formations.php') echo 'class="active"'; ?>>Formations</a></li>
            <li><a href="<?php echo ROOT_URL; ?>/opportunities.php" <?php if($current_page == 'opportunities.php') echo 'class="active"'; ?>>Opportunités</a></li>
            <li><a href="<?php echo ROOT_URL; ?>/events.php" <?php if($current_page == 'events.php') echo 'class="active"'; ?>>Événements</a></li>
            <li><a href="<?php echo ROOT_URL; ?>/alumni.php" <?php if($current_page == 'alumni.php') echo 'class="active"'; ?>>lauréats</a></li>
            <li><a href="<?php echo ROOT_URL; ?>/contact.php" <?php if($current_page == 'contact.php') echo 'class="active"'; ?>>Contact</a></li>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <li class="user-menu">
                    <a href="#">
                        <i class="fas fa-user-circle"></i> 
                        <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Mon Compte'; ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo ROOT_URL; ?>/profile.php"><i class="fas fa-user"></i> Mon Profil</a></li>
                        <li><a href="<?php echo ROOT_URL; ?>/my-opportunities.php"><i class="fas fa-briefcase"></i> Mes Opportunités</a></li>
                        <li><a href="<?php echo ROOT_URL; ?>/settings.php"><i class="fas fa-cog"></i> Paramètres</a></li>
                        <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                            <li><a href="<?php echo ROOT_URL; ?>/admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Administration</a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo ROOT_URL; ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li><a href="<?php echo ROOT_URL; ?>/login.php" class="btn-primary">Connexion</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    
    <main id="main-content">
        <!-- Main content goes here -->
    </main>
</body>
</html>