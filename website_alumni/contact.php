<?php
require_once 'config.php';

// Set page title
$page_title = "Contact";

// Process form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and sanitize inputs
    $name = isset($_POST['name']) ? sanitize_input($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
    $subject = isset($_POST['subject']) ? sanitize_input($_POST['subject']) : '';
    $message = isset($_POST['message']) ? sanitize_input($_POST['message']) : '';
    
    // Basic validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = 'Tous les champs sont obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Veuillez entrer une adresse email valide.';
    } else {
        // Save message to database
        $query = "INSERT INTO contact_messages (name, email, subject, message, status, created_at) 
                  VALUES (?, ?, ?, ?, 'unread', NOW())";
        $stmt = mysqli_prepare($conn, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssss', $name, $email, $subject, $message);
            $result = mysqli_stmt_execute($stmt);
            
            if ($result) {
                $success_message = 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.';
                
                // Reset form fields
                $name = $email = $subject = $message = '';
            } else {
                $error_message = 'Une erreur est survenue lors de l\'envoi de votre message. Veuillez réessayer plus tard.';
            }
            
            mysqli_stmt_close($stmt);
        } else {
            $error_message = 'Une erreur est survenue. Veuillez réessayer plus tard.';
        }
    }
}

// Include header
include 'includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Contactez-nous</h1>
        <p class="lead">Pour toute demande d'information ou question, n'hésitez pas à nous contacter</p>
    </div>
</div>

<div class="container">
    <div class="contact-container">
        <div class="contact-info-section">
            <div class="contact-cards">
                <div class="contact-card">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>Adresse</h3>
                    <p>École Nationale des Sciences Appliquées de Fès<br>
                    Route d'Imouzzer, Fès 30000<br>
                    Maroc</p>
                </div>
                
                <div class="contact-card">
                    <i class="fas fa-phone-alt"></i>
                    <h3>Téléphone</h3>
                    <p>Téléphone: +212 535 600 403</p>
                    <p>Fax: +212 535 600 386</p>
                </div>
                
                <div class="contact-card">
                    <i class="fas fa-envelope"></i>
                    <h3>Email</h3>
                    <p>contact@ensaf.ac.ma</p>
                    <p>scolarite@ensaf.ac.ma</p>
                </div>
                
                <div class="contact-card">
                    <i class="fas fa-clock"></i>
                    <h3>Horaires d'ouverture</h3>
                    <p>Lundi - Vendredi: 8:30 - 16:30</p>
                    <p>Fermé les weekends et jours fériés</p>
                </div>
            </div>

            <div class="social-links-large">
                <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" title="YouTube"><i class="fab fa-youtube"></i></a>
            </div>
        </div>

        <div class="contact-form-section">
            <h2>Envoyez-nous un message</h2>
            
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <form action="contact.php" method="POST" class="contact-form">
                <div class="form-group">
                    <label for="name">Nom complet <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="subject">Sujet <span class="required">*</span></label>
                    <input type="text" id="subject" name="subject" class="form-control" value="<?php echo htmlspecialchars($subject ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="message">Message <span class="required">*</span></label>
                    <textarea id="message" name="message" class="form-control" rows="6" required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn-primary">Envoyer le message</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="map-section">
        <h2>Notre localisation</h2>
        <div class="map-container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3307.8391624046147!2d-5.00442298485307!3d33.99802738062091!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd9f8b95a893ae2f%3A0x91bcdf5d1c3a38b!2s%C3%89cole%20Nationale%20des%20Sciences%20Appliqu%C3%A9es%20de%20F%C3%A8s!5e0!3m2!1sfr!2sma!4v1629464148375!5m2!1sfr!2sma" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>

    <div class="faq-section">
        <h2>Questions fréquemment posées</h2>
        
        <div class="accordion" id="faqAccordion">
            <div class="accordion-item">
                <h3 class="accordion-header" id="faq1">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqAnswer1" aria-expanded="true" aria-controls="faqAnswer1">
                        Comment puis-je m'inscrire à l'ENSAF ?
                    </button>
                </h3>
                <div id="faqAnswer1" class="accordion-collapse collapse show" aria-labelledby="faq1" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        L'inscription à l'ENSAF se fait via le concours national d'admission aux écoles d'ingénieurs. Pour plus de détails, consultez la page <a href="formations.php">Formations</a> ou contactez le service de scolarité.
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h3 class="accordion-header" id="faq2">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqAnswer2" aria-expanded="false" aria-controls="faqAnswer2">
                        Quels sont les frais de scolarité ?
                    </button>
                </h3>
                <div id="faqAnswer2" class="accordion-collapse collapse" aria-labelledby="faq2" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        L'ENSAF est un établissement public. Les frais d'inscription annuels sont fixés par le Ministère de l'Éducation Nationale. Pour connaître les tarifs en vigueur, veuillez contacter le service financier de l'école.
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h3 class="accordion-header" id="faq3">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqAnswer3" aria-expanded="false" aria-controls="faqAnswer3">
                        Y a-t-il des logements pour les étudiants ?
                    </button>
                </h3>
                <div id="faqAnswer3" class="accordion-collapse collapse" aria-labelledby="faq3" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        L'ENSAF ne dispose pas de résidences universitaires propres, mais les étudiants peuvent bénéficier des cités universitaires de l'Université Sidi Mohamed Ben Abdellah. Des logements privés sont également disponibles à proximité de l'école.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>