<?php
require_once 'config.php';

// Create contact_messages table if it doesn't exist
$create_table_query = "CREATE TABLE IF NOT EXISTS contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('unread', 'read', 'replied') DEFAULT 'unread'
)";

mysqli_query($conn, $create_table_query);

// Initialize variables
$name = $email = $subject = $message = "";
$error_message = $success_message = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    if (empty($_POST["name"])) {
        $error_message = "Le nom est obligatoire";
    } else {
        $name = sanitize_input($_POST["name"]);
    }
    
    if (empty($_POST["email"])) {
        $error_message = "L'email est obligatoire";
    } else {
        $email = sanitize_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Format d'email invalide";
        }
    }
    
    if (empty($_POST["subject"])) {
        $error_message = "Le sujet est obligatoire";
    } else {
        $subject = sanitize_input($_POST["subject"]);
    }
    
    if (empty($_POST["message"])) {
        $error_message = "Le message est obligatoire";
    } else {
        $message = sanitize_input($_POST["message"]);
    }
    
    // If no errors, save to database
    if (empty($error_message)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $subject, $message);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.";
            
            // Optional: Send email notification to admin
            $to = "admin@ensaf.ac.ma";
            $email_subject = "Nouveau message de contact: " . $subject;
            $email_body = "Vous avez reçu un nouveau message de contact.\n\n" .
                          "Nom: " . $name . "\n" .
                          "Email: " . $email . "\n" .
                          "Sujet: " . $subject . "\n" .
                          "Message: " . $message . "\n";
            $headers = "From: webmaster@ensaf.ac.ma";
            
            // Uncomment this line to enable email notification
            // mail($to, $email_subject, $email_body, $headers);
            
            // Clear form data
            $name = $email = $subject = $message = "";
        } else {
            $error_message = "Une erreur est survenue lors de l'envoi du message. Veuillez réessayer.";
        }
        
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message envoyé - ENSAF</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .message-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background-color: #1B263B;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            color: white;
            text-align: center;
        }
        
        .success-message {
            color: #4CAF50;
            font-size: 18px;
            margin-bottom: 20px;
        }
        
        .error-message {
            color: #F44336;
            font-size: 18px;
            margin-bottom: 20px;
        }
        
        .buttons {
            margin-top: 30px;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #800020;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            margin: 0 10px;
        }
        
        .btn:hover {
            background-color: #600018;
        }
        
        .icon {
            font-size: 60px;
            margin-bottom: 20px;
            color: #FFD700;
        }
    </style>
</head>
<body>
    <header>
        <h1>ENSAF</h1>
    </header>
    
    <div class="message-container">
        <?php if (!empty($success_message)): ?>
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <h2>Message Envoyé!</h2>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php elseif (!empty($error_message)): ?>
            <div class="icon"><i class="fas fa-exclamation-circle"></i></div>
            <h2>Erreur</h2>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php else: ?>
            <div class="icon"><i class="fas fa-question-circle"></i></div>
            <h2>Aucune données soumises</h2>
            <p>Veuillez retourner au formulaire de contact pour envoyer un message.</p>
        <?php endif; ?>
        
        <div class="buttons">
            <a href="index.php" class="btn"><i class="fas fa-home"></i> Accueil</a>
            <a href="index.php#contact" class="btn"><i class="fas fa-envelope"></i> Retour au formulaire</a>
        </div>
    </div>
    
    <footer>
        <p>&copy; <?php echo date('Y'); ?> École Nationale des Sciences Appliquées de Fès. Tous droits réservés.</p>
    </footer>
    
    <script>
        // Redirect to home page after 5 seconds if message was successful
        <?php if (!empty($success_message)): ?>
        setTimeout(function() {
            window.location.href = 'index.php';
        }, 5000);
        <?php endif; ?>
    </script>
</body>
</html> 