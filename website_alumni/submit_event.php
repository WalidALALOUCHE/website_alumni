<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = 'submit_event.php';
    header("Location: login.php");
    exit;
}

$page_title = "Proposer un événement";
$error = '';
$success = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $title = sanitize_input($_POST['title']);
    $description = sanitize_input($_POST['description']);
    $location = sanitize_input($_POST['location']);
    $start_date = sanitize_input($_POST['start_date']);
    $end_date = !empty($_POST['end_date']) ? sanitize_input($_POST['end_date']) : null;
    $image = isset($_FILES['image']) ? $_FILES['image'] : null;
    $organizer_id = $_SESSION['user_id'];

    // Validate required fields
    if (empty($title) || empty($start_date) || empty($location) || empty($description)) {
        $error = "Veuillez remplir tous les champs obligatoires.";
    } else {
        $image_url = null;
        
        // Handle image upload if provided
        if ($image && $image['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($image['type'], $allowed_types)) {
                $error = "Format d'image non supporté. Utilisez JPG ou PNG.";
            } elseif ($image['size'] > $max_size) {
                $error = "L'image est trop volumineuse. Taille maximum: 5MB.";
            } else {
                $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $extension;
                $upload_path = 'uploads/images/' . $filename;
                
                if (move_uploaded_file($image['tmp_name'], $upload_path)) {
                    $image_url = $upload_path;
                } else {
                    $error = "Erreur lors de l'upload de l'image.";
                }
            }
        }

        if (empty($error)) {
            // Convert datetime-local to datetime format for MySQL
            $start_date_value = date('Y-m-d H:i:s', strtotime($start_date));
            $end_date_value = $end_date ? date('Y-m-d H:i:s', strtotime($end_date)) : null;

            // Insert event with correct column names
            $query = "INSERT INTO events (
                        title, description, start_date, end_date,
                        location, image, created_by, status
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?, ?, 'draft'
                    )";
                    
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param(
                $stmt, 
                "ssssssi", 
                $title, $description, $start_date_value, $end_date_value,
                $location, $image_url, $organizer_id
            );
            
            if (mysqli_stmt_execute($stmt)) {
                $success = "Votre événement a été soumis avec succès et est en attente d'approbation.";
                
                // Reset form data on success
                $title = $location = $description = '';
                $start_date = $end_date = '';
            } else {
                $error = "Une erreur est survenue lors de la soumission. Veuillez réessayer.";
            }
        }
    }
}

// Include header
include 'includes/header.php';
?>

<div class="container">
    <div class="submit-event-page">
        <div class="page-header">
            <h1>Proposer un événement</h1>
            <p class="page-description">
                Partagez un événement avec la communauté ENSAF. 
                Votre soumission sera examinée par nos administrateurs avant d'être publiée.
            </p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                <p>Vous pouvez consulter le statut de votre soumission dans votre profil.</p>
                <div class="alert-actions">
                    <a href="events.php" class="btn-secondary">Voir tous les événements</a>
                    <a href="profile.php" class="btn-primary">Voir mon profil</a>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (empty($success)): ?>
            <form action="submit_event.php" method="post" class="event-form" enctype="multipart/form-data">
                <div class="form-section">
                    <h2>Informations de base</h2>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="title">Titre de l'événement*</label>
                            <input type="text" id="title" name="title" value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="location">Lieu*</label>
                        <input type="text" id="location" name="location" value="<?php echo isset($location) ? htmlspecialchars($location) : ''; ?>" required>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Date et heure</h2>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="start_date">Date et heure de début*</label>
                            <input type="datetime-local" id="start_date" name="start_date" value="<?php echo isset($start_date) ? date('Y-m-d\TH:i', strtotime($start_date)) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="end_date">Date et heure de fin</label>
                            <input type="datetime-local" id="end_date" name="end_date" value="<?php echo isset($end_date) ? date('Y-m-d\TH:i', strtotime($end_date)) : ''; ?>">
                            <small>Optionnel</small>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Description</h2>
                    <div class="form-group">
                        <label for="description">Description détaillée*</label>
                        <textarea id="description" name="description" rows="6" required><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Image</h2>
                    <div class="form-group">
                        <label for="image">Image de l'événement</label>
                        <input type="file" id="image" name="image" accept="image/*">
                        <small>Format recommandé : JPEG, PNG. Taille maximale : 2 MB</small>
                    </div>
                </div>

                <div class="form-footer">
                    <div class="form-info">
                        <p><i class="fas fa-info-circle"></i> Les champs marqués d'un * sont obligatoires</p>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Soumettre l'événement
                        </button>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<style>
.submit-event-page {
    padding: 2rem 0;
}

.page-header {
    margin-bottom: 2rem;
    text-align: center;
}

.page-header h1 {
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.page-description {
    color: #666;
    max-width: 800px;
    margin: 0 auto;
}

.event-form {
    max-width: 800px;
    margin: 0 auto;
    background-color: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: var(--shadow-light);
}

.form-section {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #eee;
}

.form-section:last-child {
    border-bottom: none;
}

.form-section h2 {
    color: var(--secondary-color);
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group:last-child {
    margin-bottom: 0;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #444;
}

.form-group input[type="text"],
.form-group input[type="datetime-local"],
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
}

.form-group small {
    display: block;
    margin-top: 0.5rem;
    color: #666;
    font-size: 0.875rem;
}

.form-footer {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #eee;
}

.form-info {
    margin-bottom: 1.5rem;
}

.form-info p {
    color: #666;
    margin-bottom: 0.5rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}

.alert {
    padding: 1rem;
    border-radius: 5px;
    margin-bottom: 2rem;
}

.alert-error {
    background-color: #fee2e2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

.alert-success {
    background-color: #dcfce7;
    color: #16a34a;
    border: 1px solid #bbf7d0;
}

.alert i {
    margin-right: 0.5rem;
}

.alert-actions {
    margin-top: 1rem;
    display: flex;
    gap: 1rem;
}
</style>

<?php include 'includes/footer.php'; ?>