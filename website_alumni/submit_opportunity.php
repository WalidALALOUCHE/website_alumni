<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = 'submit_opportunity.php';
    header("Location: login.php");
    exit;
}

$page_title = "Soumettre une opportunité";
$error = '';
$success = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $title = sanitize_input($_POST['title']);
    $type = sanitize_input($_POST['type']);
    $company_name = sanitize_input($_POST['company_name']);
    $location = sanitize_input($_POST['location']);
    $description = sanitize_input($_POST['description']);
    $requirements = isset($_POST['requirements']) ? sanitize_input($_POST['requirements']) : '';
    $benefits = isset($_POST['benefits']) ? sanitize_input($_POST['benefits']) : '';
    $deadline = !empty($_POST['deadline']) ? sanitize_input($_POST['deadline']) : null;
    $start_date = !empty($_POST['start_date']) ? sanitize_input($_POST['start_date']) : null;
    $duration = isset($_POST['duration']) ? sanitize_input($_POST['duration']) : '';
    $contract_type = isset($_POST['contract_type']) ? sanitize_input($_POST['contract_type']) : '';
    $salary_range = isset($_POST['salary_range']) ? sanitize_input($_POST['salary_range']) : '';
    $remote_work = isset($_POST['remote_work']) ? sanitize_input($_POST['remote_work']) : '';
    $apply_url = isset($_POST['apply_url']) ? sanitize_input($_POST['apply_url']) : '';
    $contact_email = isset($_POST['contact_email']) ? sanitize_input($_POST['contact_email']) : '';
    $application_instructions = isset($_POST['application_instructions']) ? sanitize_input($_POST['application_instructions']) : '';
    $company_website = isset($_POST['company_website']) ? sanitize_input($_POST['company_website']) : '';
    $user_id = $_SESSION['user_id'];
    
    // Validate required fields
    if (empty($title) || empty($type) || empty($company_name) || empty($location) || empty($description)) {
        $error = "Veuillez remplir tous les champs obligatoires.";
    } else {
        // Insert opportunity
        $query = "INSERT INTO opportunities (
                    title, type, company_name, location, description, requirements, 
                    benefits, deadline, start_date, duration, contract_type, salary_range, 
                    remote_work, apply_url, contact_email, application_instructions, 
                    company_website, user_id, status, created_at
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW()
                )";
                
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param(
            $stmt, 
            "sssssssssssssssssi", 
            $title, $type, $company_name, $location, $description, $requirements, 
            $benefits, $deadline, $start_date, $duration, $contract_type, $salary_range, 
            $remote_work, $apply_url, $contact_email, $application_instructions, 
            $company_website, $user_id
        );
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Votre opportunité a été soumise avec succès et est en attente d'approbation.";
            
            // Notify admins (optional - you can implement email notification here)
            
            // Reset form data on success
            $title = $company_name = $location = $description = $requirements = $benefits = '';
            $deadline = $start_date = $duration = $contract_type = $salary_range = $remote_work = '';
            $apply_url = $contact_email = $application_instructions = $company_website = '';
            $type = '';
        } else {
            $error = "Une erreur est survenue lors de la soumission. Veuillez réessayer.";
        }
    }
}

// Include header
include 'includes/header.php';
?>

<div class="container">
    <div class="submit-opportunity-page">
        <div class="page-header">
            <h1>Soumettre une opportunité</h1>
            <p class="page-description">
                Partagez une opportunité de stage, d'emploi, de projet ou de formation avec la communauté ENSAF.
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
                    <a href="opportunities.php" class="btn-secondary">Voir toutes les opportunités</a>
                    <a href="profile.php" class="btn-primary">Voir mon profil</a>
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($success)): ?>
            <form action="submit_opportunity.php" method="post" class="opportunity-form">
                <div class="form-section">
                    <h2>Informations de base</h2>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="title">Titre de l'opportunité*</label>
                            <input type="text" id="title" name="title" value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="type">Type d'opportunité*</label>
                            <select id="type" name="type" required>
                                <option value="" disabled <?php echo empty($type) ? 'selected' : ''; ?>>Sélectionnez un type</option>
                                <option value="internship" <?php echo (isset($type) && $type == 'internship') ? 'selected' : ''; ?>>Stage</option>
                                <option value="job" <?php echo (isset($type) && $type == 'job') ? 'selected' : ''; ?>>Emploi</option>
                                <option value="project" <?php echo (isset($type) && $type == 'project') ? 'selected' : ''; ?>>Projet</option>
                                <option value="training" <?php echo (isset($type) && $type == 'training') ? 'selected' : ''; ?>>Formation</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="company_name">Nom de l'entreprise/organisation*</label>
                            <input type="text" id="company_name" name="company_name" value="<?php echo isset($company_name) ? htmlspecialchars($company_name) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="location">Lieu*</label>
                            <input type="text" id="location" name="location" value="<?php echo isset($location) ? htmlspecialchars($location) : ''; ?>" placeholder="Ville, Pays" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="company_website">Site web de l'entreprise</label>
                        <input type="url" id="company_website" name="company_website" value="<?php echo isset($company_website) ? htmlspecialchars($company_website) : ''; ?>" placeholder="https://...">
                    </div>
                </div>
                
                <div class="form-section">
                    <h2>Description de l'opportunité</h2>
                    <div class="form-group">
                        <label for="description">Description détaillée*</label>
                        <textarea id="description" name="description" rows="6" required><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
                        <small>Décrivez l'opportunité, les responsabilités et le contexte.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="requirements">Profil recherché</label>
                        <textarea id="requirements" name="requirements" rows="4"><?php echo isset($requirements) ? htmlspecialchars($requirements) : ''; ?></textarea>
                        <small>Compétences requises, niveau d'études, expérience...</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="benefits">Avantages</label>
                        <textarea id="benefits" name="benefits" rows="3"><?php echo isset($benefits) ? htmlspecialchars($benefits) : ''; ?></textarea>
                        <small>Avantages, environnement de travail, opportunités de développement...</small>
                    </div>
                </div>
                
                <div class="form-section">
                    <h2>Détails du poste</h2>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="start_date">Date de début</label>
                            <input type="date" id="start_date" name="start_date" value="<?php echo isset($start_date) ? htmlspecialchars($start_date) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="deadline">Date limite de candidature</label>
                            <input type="date" id="deadline" name="deadline" value="<?php echo isset($deadline) ? htmlspecialchars($deadline) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="duration">Durée</label>
                            <input type="text" id="duration" name="duration" value="<?php echo isset($duration) ? htmlspecialchars($duration) : ''; ?>" placeholder="Ex: 6 mois, 1 an, Indéterminée">
                        </div>
                        <div class="form-group">
                            <label for="contract_type">Type de contrat</label>
                            <input type="text" id="contract_type" name="contract_type" value="<?php echo isset($contract_type) ? htmlspecialchars($contract_type) : ''; ?>" placeholder="Ex: CDD, CDI, Stage">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="salary_range">Rémunération</label>
                            <input type="text" id="salary_range" name="salary_range" value="<?php echo isset($salary_range) ? htmlspecialchars($salary_range) : ''; ?>" placeholder="Ex: 1500-2000€/mois, Selon profil">
                        </div>
                        <div class="form-group">
                            <label for="remote_work">Télétravail</label>
                            <input type="text" id="remote_work" name="remote_work" value="<?php echo isset($remote_work) ? htmlspecialchars($remote_work) : ''; ?>" placeholder="Ex: 100%, Hybride, Non">
                        </div>
                    </div>
                </div>
                
                <div class="form-section">
                    <h2>Comment postuler</h2>
                    <div class="form-group">
                        <label for="apply_url">Lien pour postuler</label>
                        <input type="url" id="apply_url" name="apply_url" value="<?php echo isset($apply_url) ? htmlspecialchars($apply_url) : ''; ?>" placeholder="https://...">
                    </div>
                    
                    <div class="form-group">
                        <label for="contact_email">Email de contact</label>
                        <input type="email" id="contact_email" name="contact_email" value="<?php echo isset($contact_email) ? htmlspecialchars($contact_email) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="application_instructions">Instructions pour postuler</label>
                        <textarea id="application_instructions" name="application_instructions" rows="3"><?php echo isset($application_instructions) ? htmlspecialchars($application_instructions) : ''; ?></textarea>
                        <small>Informations supplémentaires sur le processus de candidature.</small>
                    </div>
                </div>
                
                <div class="form-footer">
                    <div class="form-info">
                        <p><strong>Note:</strong> Les champs marqués d'un * sont obligatoires.</p>
                        <p>Votre soumission sera examinée par un administrateur avant d'être publiée.</p>
                    </div>
                    <div class="form-actions">
                        <button type="reset" class="btn-secondary">Réinitialiser</button>
                        <button type="submit" class="btn-primary">Soumettre l'opportunité</button>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 