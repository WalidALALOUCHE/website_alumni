<?php
require_once 'config.php';

// Set page title
$page_title = "Opportunités";

// Get filter parameters
$type = isset($_GET['type']) ? sanitize_input($_GET['type']) : '';
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';

// Build query based on filters
$query = "SELECT * FROM opportunities WHERE status = 'approved'";
$params = [];
$param_types = "";

if (!empty($type)) {
    $query .= " AND type = ?";
    $params[] = $type;
    $param_types .= "s";
}

if (!empty($search)) {
    $query .= " AND (title LIKE ? OR company_name LIKE ? OR description LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $param_types .= "sss";
}

$query .= " ORDER BY created_at DESC";

// Prepare and execute statement
$stmt = mysqli_prepare($conn, $query);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $param_types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Include header
include 'includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Opportunités</h1>
        <p class="lead">Découvrez les offres de stages, emplois et autres opportunités professionnelles pour les étudiants et diplômés de l'ENSAF</p>
    </div>
    
    <div class="opportunities-filters">
        <form action="opportunities.php" method="GET" class="filter-form">
            <div class="form-row">
                <div class="filter-group">
                    <label for="type">Type d'opportunité</label>
                    <select name="type" id="type" class="form-control">
                        <option value="" <?php echo empty($type) ? 'selected' : ''; ?>>Toutes les opportunités</option>
                        <option value="internship" <?php echo $type === 'internship' ? 'selected' : ''; ?>>Stages</option>
                        <option value="job" <?php echo $type === 'job' ? 'selected' : ''; ?>>Emplois</option>
                        <option value="project" <?php echo $type === 'project' ? 'selected' : ''; ?>>Projets</option>
                        <option value="training" <?php echo $type === 'training' ? 'selected' : ''; ?>>Formations</option>
                    </select>
                </div>
                
                <div class="filter-group search-group">
                    <label for="search">Recherche</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Rechercher par titre, entreprise, mot-clé..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn-primary"><i class="fas fa-filter"></i> Filtrer</button>
                    <a href="opportunities.php" class="btn-secondary"><i class="fas fa-sync"></i> Réinitialiser</a>
                </div>
            </div>
        </form>
    </div>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php 
                echo $_SESSION['success_message']; 
                unset($_SESSION['success_message']);
            ?>
        </div>
    <?php endif; ?>
    
    <div class="opportunities-list">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($opportunity = mysqli_fetch_assoc($result)): ?>
                <div class="opportunity-card">
                    <div class="opportunity-header">
                        <h2 class="opportunity-title"><?php echo htmlspecialchars($opportunity['title']); ?></h2>
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
                    </div>
                    
                    <div class="opportunity-details">
                        <div class="company-info">
                            <p class="company-name"><i class="fas fa-building"></i> <?php echo htmlspecialchars($opportunity['company_name']); ?></p>
                            <p class="location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($opportunity['location']); ?></p>
                        </div>
                        
                        <div class="opportunity-meta">
                            <p class="posted-date"><i class="far fa-calendar-alt"></i> Publié le <?php echo date('d/m/Y', strtotime($opportunity['created_at'])); ?></p>
                            <?php if (!empty($opportunity['deadline'])): ?>
                                <p class="deadline <?php echo strtotime($opportunity['deadline']) < time() ? 'expired' : ''; ?>">
                                    <i class="fas fa-clock"></i> 
                                    <?php if (strtotime($opportunity['deadline']) < time()): ?>
                                        Date limite dépassée (<?php echo date('d/m/Y', strtotime($opportunity['deadline'])); ?>)
                                    <?php else: ?>
                                        Date limite: <?php echo date('d/m/Y', strtotime($opportunity['deadline'])); ?>
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="opportunity-content">
                        <div class="opportunity-description">
                            <?php 
                                // Show a truncated description
                                $description = $opportunity['description'];
                                echo nl2br(htmlspecialchars(substr($description, 0, 300)));
                                if (strlen($description) > 300) {
                                    echo '...';
                                }
                            ?>
                        </div>
                    </div>
                    
                    <div class="opportunity-actions">
                        <a href="opportunity_details.php?id=<?php echo $opportunity['id']; ?>" class="btn-secondary"><i class="fas fa-info-circle"></i> Voir les détails</a>
                        <?php if (!empty($opportunity['apply_url'])): ?>
                            <a href="<?php echo htmlspecialchars($opportunity['apply_url']); ?>" target="_blank" class="btn-primary"><i class="fas fa-paper-plane"></i> Postuler</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-results">
                <i class="fas fa-search"></i>
                <h3>Aucune opportunité trouvée</h3>
                <p>Aucune opportunité ne correspond à vos critères de recherche. Essayez de modifier vos filtres ou revenez plus tard.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="share-section">
            <div class="container">
                <div class="share-content">
                    <div class="share-text">
                        <h2><i class="fas fa-briefcase"></i> Vous avez une opportunité à partager ?</h2>
                        <p>Aidez les étudiants et diplômés de l'ENSAF en partageant des offres de stages, emplois, projets ou formations.</p>
                    </div>
                    <div class="share-actions">
                        <a href="submit_opportunity.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus-circle"></i> Proposer une opportunité
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="share-section guest-section">
            <div class="container">
                <div class="share-content">
                    <div class="share-text">
                        <h2><i class="fas fa-briefcase"></i> Vous avez une opportunité à partager ?</h2>
                        <p>Connectez-vous pour proposer des opportunités et aider les étudiants et diplômés de l'ENSAF.</p>
                    </div>
                    <div class="share-actions">
                        <a href="login.php" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Se connecter
                        </a>
                        <a href="register.php" class="btn btn-outline-primary">
                            <i class="fas fa-user-plus"></i> S'inscrire
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="partnership-section">
        <h2>Devenez partenaire de l'ENSAF</h2>
        <p>L'ENSAF est ouverte aux partenariats avec les entreprises et organisations qui souhaitent collaborer avec notre école pour le développement de projets, stages, et embauches de nos diplômés.</p>
        
        <div class="partnership-benefits">
            <div class="benefit-card">
                <i class="fas fa-users"></i>
                <h3>Accès aux talents</h3>
                <p>Recrutez parmi nos meilleurs étudiants et diplômés hautement qualifiés.</p>
            </div>
            
            <div class="benefit-card">
                <i class="fas fa-handshake"></i>
                <h3>Projets collaboratifs</h3>
                <p>Collaborez avec nos équipes de recherche sur des projets innovants.</p>
            </div>
            
            <div class="benefit-card">
                <i class="fas fa-lightbulb"></i>
                <h3>Innovation</h3>
                <p>Profitez de solutions innovantes développées par nos étudiants.</p>
            </div>
            
            <div class="benefit-card">
                <i class="fas fa-building"></i>
                <h3>Visibilité</h3>
                <p>Augmentez votre visibilité auprès des futurs ingénieurs et de notre réseau.</p>
            </div>
        </div>
        
        <div class="partnership-cta">
            <a href="contact.php" class="btn btn-primary btn-lg">
                <i class="fas fa-envelope"></i> Contactez-nous pour un partenariat
            </a>
        </div>
    </div>
</div>

<style>
/* Share section styling */
.share-section {
    background-color: #f8f9fa;
    padding: 3rem 0;
    margin-top: 3rem;
    border-top: 1px solid #e9ecef;
}

.share-section.guest-section {
    background-color: var(--primary-color);
    color: white;
}

.share-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 2rem;
}

.share-text {
    flex: 1;
}

.share-text h2 {
    font-size: 1.75rem;
    margin-bottom: 1rem;
    color: inherit;
}

.share-text p {
    font-size: 1.1rem;
    margin-bottom: 0;
    opacity: 0.9;
}

.share-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.guest-section .btn-outline-primary {
    color: white;
    border-color: white;
}

.guest-section .btn-outline-primary:hover {
    background-color: white;
    color: var(--primary-color);
}

/* Partnership section styling */
.partnership-section {
    padding: 4rem 0;
    text-align: center;
    background-color: white;
}

.partnership-section h2 {
    font-size: 2rem;
    margin-bottom: 1.5rem;
    color: var(--primary-color);
}

.partnership-section > p {
    font-size: 1.1rem;
    max-width: 800px;
    margin: 0 auto 3rem;
    color: #666;
}

.partnership-benefits {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.benefit-card {
    padding: 2rem;
    background-color: #f8f9fa;
    border-radius: 10px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.benefit-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-light);
}

.benefit-card i {
    font-size: 2.5rem;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.benefit-card h3 {
    font-size: 1.25rem;
    margin-bottom: 1rem;
    color: var(--secondary-color);
}

.benefit-card p {
    color: #666;
    margin: 0;
}

.partnership-cta {
    margin-top: 2rem;
}

/* Enhanced opportunity cards */
.opportunity-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.opportunity-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-dark);
}

.filters-section {
    background-color: white;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: var(--shadow-light);
    margin-bottom: 2rem;
}
</style>

<?php include 'includes/footer.php'; ?>