<?php
require_once 'config.php';

$error_message = '';
$success_message = '';

// Check if user is already logged in, redirect to index page
if (is_logged_in()) {
    redirect('index.php');
}

// Process registration form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $full_name = sanitize_input($_POST['full_name']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $user_type = sanitize_input($_POST['user_type']);
    
    // Validate required fields
    if (empty($username) || empty($email) || empty($full_name) || empty($password) || empty($confirm_password)) {
        $error_message = "Tous les champs sont obligatoires";
    } elseif ($password !== $confirm_password) {
        $error_message = "Les mots de passe ne correspondent pas";
    } elseif (strlen($password) < 8) {
        $error_message = "Le mot de passe doit contenir au moins 8 caractères";
    } else {
        // Check if username or email already exists
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? OR email = ?");
        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $error_message = "Ce nom d'utilisateur ou cet email est déjà utilisé";
        } else {
            // Handle document upload
            $document_path = '';
            if (isset($_FILES['verification_document']) && $_FILES['verification_document']['error'] == 0) {
                $allowed_types = ['application/pdf', 'image/jpeg', 'image/png'];
                $file_type = $_FILES['verification_document']['type'];
                
                if (in_array($file_type, $allowed_types)) {
                    // Create uploads directory if it doesn't exist
                    $upload_dir = 'uploads/verification_documents/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    // Generate unique filename
                    $file_extension = pathinfo($_FILES['verification_document']['name'], PATHINFO_EXTENSION);
                    $filename = uniqid('doc_') . '.' . $file_extension;
                    $document_path = $upload_dir . $filename;
                    
                    // Move uploaded file
                    if (move_uploaded_file($_FILES['verification_document']['tmp_name'], $document_path)) {
                        // Document uploaded successfully
                    } else {
                        $error_message = "Erreur lors du téléchargement du document";
                        $document_path = '';
                    }
                } else {
                    $error_message = "Type de fichier non autorisé. Veuillez télécharger un PDF, JPEG ou PNG";
                }
            } else {
                $error_message = "Veuillez télécharger un document de vérification";
            }
            
            if (empty($error_message)) {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert new user with pending verification status
                $verification_status = 'pending';
                $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password, full_name, role, verification_status, verification_document) VALUES (?, ?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "sssssss", $username, $email, $hashed_password, $full_name, $user_type, $verification_status, $document_path);
                
                if (mysqli_stmt_execute($stmt)) {
                    $success_message = "Inscription réussie! Votre compte est en attente de vérification par l'administration.";
                } else {
                    $error_message = "Erreur lors de l'inscription: " . mysqli_error($conn);
                }
            }
        }
    }
}

// Get the user type from URL if present
$user_type = isset($_GET['type']) ? $_GET['type'] : 'student';
$valid_types = ['student', 'professor', 'alumni'];
if (!in_array($user_type, $valid_types)) {
    $user_type = 'student';
}

// Set page title based on user type
$titles = [
    'student' => 'Inscription Étudiants',
    'professor' => 'Inscription Enseignants',
    'alumni' => 'Inscription Lauréats'
];
$page_title = $titles[$user_type];

// Include header
include 'includes/header.php';
?>

<div class="register-page">
    <div class="container">
        <div class="register-container">
            <div class="register-header text-center mb-4">
                <img src="assets/images/logo-ensaf.png" alt="ENSAF Logo" class="register-logo mb-3">
                <h2 class="title"><?php echo $page_title; ?></h2>
            </div>
            
            <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            
            <div class="user-type-selector mb-4">
                <div class="btn-group w-100" role="group" aria-label="User type selection">
                    <input type="radio" class="btn-check" name="user-type" id="student" value="student" 
                           <?php echo $user_type == 'student' ? 'checked' : ''; ?> 
                           onclick="window.location.href='register.php?type=student'">
                    <label class="btn btn-outline-primary" for="student">Étudiant</label>

                    <input type="radio" class="btn-check" name="user-type" id="professor" value="professor" 
                           <?php echo $user_type == 'professor' ? 'checked' : ''; ?>
                           onclick="window.location.href='register.php?type=professor'">
                    <label class="btn btn-outline-primary" for="professor">Enseignant</label>

                    <input type="radio" class="btn-check" name="user-type" id="alumni" value="alumni" 
                           <?php echo $user_type == 'alumni' ? 'checked' : ''; ?>
                           onclick="window.location.href='register.php?type=alumni'">
                    <label class="btn btn-outline-primary" for="alumni">Lauréat</label>
                </div>
            </div>
            
            <form class="register-form needs-validation" action="register.php?type=<?php echo $user_type; ?>" method="post" enctype="multipart/form-data" novalidate>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Votre nom complet" required>
                            <label for="full_name">Nom complet</label>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="username" name="username" placeholder="Choisir un nom d'utilisateur" required>
                            <label for="username">Nom d'utilisateur</label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-floating">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Votre adresse email" required>
                        <label for="email">Email</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="form-floating">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Choisir un mot de passe" required>
                            <label for="password">Mot de passe</label>
                            <div class="form-text">Le mot de passe doit contenir au moins 8 caractères</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="form-floating">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirmer le mot de passe" required>
                            <label for="confirm_password">Confirmer le mot de passe</label>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="verification_document" class="form-label">Document de vérification</label>
                    <input type="file" class="form-control" id="verification_document" name="verification_document" required>
                    <div class="form-text">Veuillez télécharger un document officiel qui prouve votre statut (carte étudiant, attestation, etc.). Formats acceptés: PDF, JPEG, PNG.</div>
                </div>

                <?php if ($user_type == 'student'): ?>
                <div class="form-group">
                    <label for="student_info">Informations supplémentaires</label>
                    <textarea id="student_info" name="student_info" placeholder="Année d'étude, filière, etc."></textarea>
                </div>
                <?php elseif ($user_type == 'professor'): ?>
                <div class="mb-3">
                    <div class="form-floating">
                        <textarea class="form-control" id="professor_info" name="professor_info" placeholder="Département, spécialité, etc." style="height: 100px"></textarea>
                        <label for="professor_info">Département et spécialité</label>
                    </div>
                </div>
                <?php elseif ($user_type == 'alumni'): ?>
                <div class="mb-3">
                    <div class="form-floating">
                        <textarea class="form-control" id="alumni_info" name="alumni_info" placeholder="Année de diplomation, poste actuel, entreprise, etc." style="height: 100px"></textarea>
                        <label for="alumni_info">Informations professionnelles</label>
                    </div>
                </div>
                <?php endif; ?>
                
                <input type="hidden" name="user_type" value="<?php echo $user_type; ?>">
                
                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-primary btn-lg">S'inscrire</button>
                </div>
            </form>
            
            <div class="text-center mt-3">
                <a href="login.php?type=<?php echo $user_type; ?>" class="text-decoration-none">Déjà inscrit ? Se connecter</a>
            </div>
        </div>
    </div>
</div>

<style>
.register-page {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    padding: 40px 0;
}

.register-container {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    max-width: 800px;
    margin: 0 auto;
}

.register-logo {
    max-height: 80px;
    margin-bottom: 1rem;
}

.title {
    color: #2c3e50;
    margin-bottom: 1.5rem;
}

.form-floating {
    margin-bottom: 1rem;
}

.form-control:focus {
    border-color: #4a90e2;
    box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
}

.btn-primary {
    background-color: #4a90e2;
    border-color: #4a90e2;
    padding: 0.8rem 2rem;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background-color: #357abd;
    border-color: #357abd;
    transform: translateY(-2px);
}

.btn-outline-primary {
    border-color: #4a90e2;
    color: #4a90e2;
}

.btn-outline-primary:hover,
.btn-outline-primary:checked {
    background-color: #4a90e2;
    color: white;
}

.btn-check:checked + .btn-outline-primary {
    background-color: #4a90e2;
    color: white;
}

.form-text {
    color: #6c757d;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.alert {
    border-radius: 10px;
}

@media (max-width: 768px) {
    .register-container {
        margin: 1rem;
        padding: 1.5rem;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/auth-forms.js"></script>