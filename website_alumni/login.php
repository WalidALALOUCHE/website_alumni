<?php
require_once 'config.php';

$error_message = '';
$success_message = '';

// Check if user is already logged in, redirect to index page
if (is_logged_in()) {
    redirect('index.php');
}

// Process login form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password']; // No need to sanitize password before verification
    $remember = isset($_POST['remember']) ? 1 : 0;
    $user_type = sanitize_input($_POST['user_type']); // Get the selected user type
    
    // Validate required fields
    if (empty($username) || empty($password)) {
        $error_message = "Tous les champs sont obligatoires";
    } else {
        // Check if username exists and matches the selected role
        $stmt = mysqli_prepare($conn, "SELECT id, username, password, email, full_name, role, status FROM users WHERE (username = ? OR email = ?) AND role = ?");
        mysqli_stmt_bind_param($stmt, "sss", $username, $username, $user_type);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            // Check if user is active
            if ($user['status'] !== 'active') {
                $error_message = "Votre compte n'est pas encore activé ou a été désactivé";
            } 
            // Verify password
            else if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['loggedin'] = true;
                
                // Set cookies if remember me is checked
                if ($remember) {
                    $token = bin2hex(random_bytes(16));
                    
                    // Store token in database
                    $expire = time() + (30 * 24 * 60 * 60); // 30 days
                    $expire_date = date('Y-m-d H:i:s', $expire);
                    $user_id = $user['id'];
                    
                    $stmt = mysqli_prepare($conn, "INSERT INTO user_tokens (user_id, token, expire_date) VALUES (?, ?, ?)");
                    mysqli_stmt_bind_param($stmt, "iss", $user_id, $token, $expire_date);
                    mysqli_stmt_execute($stmt);
                    
                    // Set cookies
                    setcookie("remember_user", $user['id'], $expire, "/");
                    setcookie("remember_token", $token, $expire, "/");
                }
                
                // Redirect based on user role
                switch ($user['role']) {
                    case 'admin':
                        redirect('admin/index.php');
                        break;
                    case 'staff':
                        redirect('staff/index.php');
                        break;
                    case 'user':
                        redirect('user/index.php');
                        break;
                    case 'alumni':
                        redirect('alumni/profile.php');
                        break;
                    default:
                        redirect('index.php');
                }
            } else {
                $error_message = "Nom d'utilisateur ou mot de passe incorrect";
            }
        } else {
            $error_message = "Nom d'utilisateur ou mot de passe incorrect";
        }
    }
}

// Get the user type from URL if present
$user_type = isset($_GET['type']) ? $_GET['type'] : 'user';
$valid_types = ['user', 'alumni', 'admin', 'staff'];
if (!in_array($user_type, $valid_types)) {
    $user_type = 'user';
}

// Set page title based on user type
$titles = [
    'user' => 'Espace Utilisateur',
    'alumni' => 'Espace Lauréats',
    'admin' => 'Espace Administration',
    'staff' => 'Espace Personnel'
];
$page_title = $titles[$user_type];

// Include header
include 'includes/header.php';
?>

<div class="login-page">
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <img src="assets/images/logo-ensaf.png" alt="ENSAF Logo" class="login-logo">
                <h2><?php echo $page_title; ?></h2>
            </div>
            
            <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
            <?php endif; ?>
            
            <div class="user-type">
                <input type="radio" id="user" name="user-type" value="user" <?php echo $user_type == 'user' ? 'checked' : ''; ?> 
                       onclick="window.location.href='login.php?type=user'">
                <label for="user">Utilisateur</label>
                
                <input type="radio" id="staff" name="user-type" value="staff" <?php echo $user_type == 'staff' ? 'checked' : ''; ?>
                       onclick="window.location.href='login.php?type=staff'">
                <label for="staff">Personnel</label>
                
                <input type="radio" id="admin" name="user-type" value="admin" <?php echo $user_type == 'admin' ? 'checked' : ''; ?>
                       onclick="window.location.href='login.php?type=admin'">
                <label for="admin">Administration</label>
                
                <input type="radio" id="alumni" name="user-type" value="alumni" <?php echo $user_type == 'alumni' ? 'checked' : ''; ?>
                       onclick="window.location.href='login.php?type=alumni'">
                <label for="alumni">Lauréat</label>
            </div>
            
            <form class="login-form" action="login.php?type=<?php echo $user_type; ?>" method="post">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur ou Email</label>
                    <input type="text" id="username" name="username" placeholder="Votre identifiant ou email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="Votre mot de passe" required>
                </div>
                
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Se souvenir de moi</label>
                </div>
                
                <input type="hidden" name="user_type" value="<?php echo $user_type; ?>">
                
                <button type="submit" class="btn-primary">Se connecter</button>
            </form>
            
            <div class="form-links">
                <a href="forgot_password.php" class="forgot-link">Mot de passe oublié ?</a>
                <a href="register.php?type=<?php echo $user_type; ?>" class="register-link">Pas encore inscrit ? Créer un compte</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/auth-forms.js"></script>