<?php
require_once 'config.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: opportunities.php");
    exit;
}

$id = sanitize_input($_GET['id']);

// Get opportunity details
$query = "SELECT * FROM opportunities WHERE id = ? AND status = 'approved'";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("Location: opportunities.php");
    exit;
}

$opportunity = mysqli_fetch_assoc($result);

// Set page title
$page_title = $opportunity['title'];

// Include header
include 'includes/header.php';
?>

<div class="container">
    <div class="opportunity-details-page">
        <div class="page-header">
            <div class="back-link">
                <a href="opportunities.php"><i class="fas fa-arrow-left"></i> Retour aux opportunités</a>
            </div>
            <h1><?php echo htmlspecialchars($opportunity['title']); ?></h1>
            
            <div class="opportunity-meta">
                <span class="opportunity-type <?php echo htmlspecialchars($opportunity['type']); ?>">
                    <?php 
                        $type_labels = [
                            'internship' => 'Stage',
                            'job' => 'Emploi',
                            'project' => 'Projet',
                            'training' => 'Formation'
                        ];
                        echo $type_labels[$opportunity['type']] ?? ucfirst($opportunity['type']);
                    ?>
                </span>
                
                <span class="posted-date">
                    <i class="far fa-calendar-alt"></i> Publié le <?php echo date('d/m/Y', strtotime($opportunity['created_at'])); ?>
                </span>
                
                <?php if (!empty($opportunity['deadline'])): ?>
                    <span class="deadline <?php echo strtotime($opportunity['deadline']) < time() ? 'expired' : ''; ?>">
                        <i class="fas fa-clock"></i> 
                        <?php if (strtotime($opportunity['deadline']) < time()): ?>
                            Date limite dépassée (<?php echo date('d/m/Y', strtotime($opportunity['deadline'])); ?>)
                        <?php else: ?>
                            Date limite: <?php echo date('d/m/Y', strtotime($opportunity['deadline'])); ?>
                        <?php endif; ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="opportunity-main-content">
            <div class="opportunity-primary-info">
                <div class="company-info-card">
                    <h2>Informations sur l'entreprise</h2>
                    <div class="company-details">
                        <p class="company-name"><i class="fas fa-building"></i> <strong>Entreprise:</strong> <?php echo htmlspecialchars($opportunity['company_name']); ?></p>
                        <p class="location"><i class="fas fa-map-marker-alt"></i> <strong>Lieu:</strong> <?php echo htmlspecialchars($opportunity['location']); ?></p>
                        <?php if (!empty($opportunity['company_website'])): ?>
                            <p class="company-website"><i class="fas fa-globe"></i> <strong>Site web:</strong> <a href="<?php echo htmlspecialchars($opportunity['company_website']); ?>" target="_blank"><?php echo htmlspecialchars($opportunity['company_website']); ?></a></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="opportunity-description-card">
                    <h2>Description</h2>
                    <div class="description-content">
                        <?php echo nl2br(htmlspecialchars($opportunity['description'])); ?>
                    </div>
                </div>
                
                <?php if (!empty($opportunity['requirements'])): ?>
                    <div class="requirements-card">
                        <h2>Profil recherché</h2>
                        <div class="requirements-content">
                            <?php echo nl2br(htmlspecialchars($opportunity['requirements'])); ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($opportunity['benefits'])): ?>
                    <div class="benefits-card">
                        <h2>Avantages</h2>
                        <div class="benefits-content">
                            <?php echo nl2br(htmlspecialchars($opportunity['benefits'])); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="opportunity-sidebar">
                <div class="apply-card">
                    <h2>Candidater</h2>
                    <?php if (strtotime($opportunity['deadline']) < time() && !empty($opportunity['deadline'])): ?>
                        <div class="deadline-expired">
                            <i class="fas fa-exclamation-circle"></i>
                            <p>La date limite de candidature est dépassée.</p>
                        </div>
                    <?php else: ?>
                        <?php if (!empty($opportunity['apply_url'])): ?>
                            <p>Candidatez directement sur le site de l'entreprise:</p>
                            <a href="<?php echo htmlspecialchars($opportunity['apply_url']); ?>" target="_blank" class="btn-primary apply-btn"><i class="fas fa-external-link-alt"></i> Postuler en ligne</a>
                        <?php elseif (!empty($opportunity['contact_email'])): ?>
                            <p>Envoyez votre candidature par email:</p>
                            <a href="mailto:<?php echo htmlspecialchars($opportunity['contact_email']); ?>?subject=Candidature: <?php echo htmlspecialchars($opportunity['title']); ?>" class="btn-primary apply-btn"><i class="fas fa-envelope"></i> Envoyer un email</a>
                        <?php else: ?>
                            <p>Pour candidater, veuillez contacter directement l'entreprise en mentionnant cette opportunité.</p>
                        <?php endif; ?>
                        
                        <?php if (!empty($opportunity['application_instructions'])): ?>
                            <div class="application-instructions">
                                <h3>Instructions pour postuler</h3>
                                <p><?php echo nl2br(htmlspecialchars($opportunity['application_instructions'])); ?></p>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                
                <div class="job-details-card">
                    <h2>Détails du poste</h2>
                    <ul class="job-details-list">
                        <?php if (!empty($opportunity['duration'])): ?>
                            <li><i class="fas fa-hourglass-half"></i> <strong>Durée:</strong> <?php echo htmlspecialchars($opportunity['duration']); ?></li>
                        <?php endif; ?>
                        
                        <?php if (!empty($opportunity['start_date'])): ?>
                            <li><i class="fas fa-play"></i> <strong>Date de début:</strong> <?php echo date('d/m/Y', strtotime($opportunity['start_date'])); ?></li>
                        <?php endif; ?>
                        
                        <?php if (!empty($opportunity['contract_type'])): ?>
                            <li><i class="fas fa-file-contract"></i> <strong>Type de contrat:</strong> <?php echo htmlspecialchars($opportunity['contract_type']); ?></li>
                        <?php endif; ?>
                        
                        <?php if (!empty($opportunity['salary_range'])): ?>
                            <li><i class="fas fa-money-bill-wave"></i> <strong>Rémunération:</strong> <?php echo htmlspecialchars($opportunity['salary_range']); ?></li>
                        <?php endif; ?>
                        
                        <?php if (!empty($opportunity['remote_work'])): ?>
                            <li><i class="fas fa-home"></i> <strong>Télétravail:</strong> <?php echo htmlspecialchars($opportunity['remote_work']); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="share-card">
                    <h2>Partager cette opportunité</h2>
                    <div class="share-buttons">
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(ROOT_URL . '/opportunity_details.php?id=' . $opportunity['id']); ?>" target="_blank" class="share-btn linkedin"><i class="fab fa-linkedin"></i> LinkedIn</a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(ROOT_URL . '/opportunity_details.php?id=' . $opportunity['id']); ?>" target="_blank" class="share-btn facebook"><i class="fab fa-facebook"></i> Facebook</a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(ROOT_URL . '/opportunity_details.php?id=' . $opportunity['id']); ?>&text=<?php echo urlencode($opportunity['title'] . ' - Opportunité à l\'ENSAF'); ?>" target="_blank" class="share-btn twitter"><i class="fab fa-twitter"></i> Twitter</a>
                        <a href="mailto:?subject=<?php echo urlencode('Opportunité intéressante: ' . $opportunity['title']); ?>&body=<?php echo urlencode('J\'ai trouvé cette opportunité qui pourrait t\'intéresser: ' . ROOT_URL . '/opportunity_details.php?id=' . $opportunity['id']); ?>" class="share-btn email"><i class="fas fa-envelope"></i> Email</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="related-opportunities">
            <h2>Opportunités similaires</h2>
            
            <?php
            // Get related opportunities
            $related_query = "SELECT * FROM opportunities 
                             WHERE status = 'approved' 
                             AND id != ? 
                             AND type = ? 
                             LIMIT 3";
            $related_stmt = mysqli_prepare($conn, $related_query);
            mysqli_stmt_bind_param($related_stmt, "is", $id, $opportunity['type']);
            mysqli_stmt_execute($related_stmt);
            $related_result = mysqli_stmt_get_result($related_stmt);
            
            if (mysqli_num_rows($related_result) > 0):
            ?>
                <div class="related-opportunities-grid">
                    <?php while ($related = mysqli_fetch_assoc($related_result)): ?>
                        <div class="opportunity-card mini">
                            <div class="opportunity-header">
                                <h3 class="opportunity-title"><?php echo htmlspecialchars($related['title']); ?></h3>
                                <span class="opportunity-type <?php echo htmlspecialchars($related['type']); ?>">
                                    <?php echo $type_labels[$related['type']] ?? ucfirst($related['type']); ?>
                                </span>
                            </div>
                            
                            <div class="opportunity-details">
                                <p class="company-name"><i class="fas fa-building"></i> <?php echo htmlspecialchars($related['company_name']); ?></p>
                                <p class="location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($related['location']); ?></p>
                            </div>
                            
                            <div class="opportunity-actions">
                                <a href="opportunity_details.php?id=<?php echo $related['id']; ?>" class="btn-secondary">Voir les détails</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="no-related">Aucune opportunité similaire disponible pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 