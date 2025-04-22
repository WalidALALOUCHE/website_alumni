<?php
require_once 'config.php';

// Set page title for header
$page_title = "Annuaire des Alumni";

// Get filter parameters from GET request
$filterPromotion = isset($_GET['promotion']) ? sanitize_input($_GET['promotion']) : 'all';
$filterFiliere = isset($_GET['filiere']) ? sanitize_input($_GET['filiere']) : 'all';
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';

// Prepare the base query
$query = "SELECT ap.*, u.full_name, u.email, d.name as department_name 
          FROM alumni_profiles ap 
          INNER JOIN users u ON ap.user_id = u.id 
          LEFT JOIN departments d ON ap.field_of_study = d.name";

// Add filters
$where_clauses = [];
$params = [];
$param_types = "";

if ($filterPromotion != 'all') {
    $where_clauses[] = "ap.graduation_year = ?";
    $params[] = $filterPromotion;
    $param_types .= "i"; // integer
}

if ($filterFiliere != 'all') {
    $where_clauses[] = "d.id = ?";
    $params[] = $filterFiliere;
    $param_types .= "i"; // integer
}

if (!empty($search)) {
    $where_clauses[] = "(u.full_name LIKE ? OR ap.current_position LIKE ? OR ap.company LIKE ?)";
    $search_param = "%" . $search . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $param_types .= "sss"; // 3 strings
}

// Complete the query with WHERE clauses if any
if (!empty($where_clauses)) {
    $query .= " WHERE " . implode(" AND ", $where_clauses);
}

$query .= " ORDER BY ap.graduation_year DESC, u.full_name ASC";

// Prepare and execute the query with parameters
$stmt = mysqli_prepare($conn, $query);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $param_types, ...$params);
}
mysqli_stmt_execute($stmt);
$alumni_result = mysqli_stmt_get_result($stmt);

// Get all promotions (graduation years) for the filter
$promotion_query = "SELECT DISTINCT graduation_year FROM alumni_profiles ORDER BY graduation_year DESC";
$promotion_result = mysqli_query($conn, $promotion_query);

// Get all departments for the filter
$department_query = "SELECT id, name FROM departments ORDER BY name ASC";
$department_result = mysqli_query($conn, $department_query);

// Include the header
include 'includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1>Annuaire des Alumni</h1>
        <p class="lead">Découvrez et connectez-vous avec les anciens élèves de l'ENSAF</p>
    </div>
</div>

<div class="container">
    <section class="alumni-section">
        <!-- Search and Filters -->
        <div class="alumni-controls">
            <div class="search-container">
                <form action="" method="GET" class="search-form">
                    <div class="search-input-group">
                        <input type="text" name="search" placeholder="Rechercher par nom, poste ou entreprise..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="search-button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="filter-container">
                <form action="" method="GET" class="filter-form">
                    <div class="filter-group">
                        <div class="filter-item">
                            <label for="promotion">Promotion:</label>
                            <select name="promotion" id="promotion">
                                <option value="all" <?php echo $filterPromotion == 'all' ? 'selected' : ''; ?>>
                                    Toutes les promotions
                                </option>
                                <?php while ($row = mysqli_fetch_assoc($promotion_result)): ?>
                                    <option value="<?php echo $row['graduation_year']; ?>" 
                                            <?php echo $filterPromotion == $row['graduation_year'] ? 'selected' : ''; ?>>
                                        Promotion <?php echo $row['graduation_year']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="filter-item">
                            <label for="filiere">Filière:</label>
                            <select name="filiere" id="filiere">
                                <option value="all" <?php echo $filterFiliere == 'all' ? 'selected' : ''; ?>>
                                    Toutes les filières
                                </option>
                                <?php while ($row = mysqli_fetch_assoc($department_result)): ?>
                                    <option value="<?php echo $row['id']; ?>" 
                                            <?php echo $filterFiliere == $row['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($row['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="filter-actions">
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-filter"></i> Filtrer
                            </button>
                            <a href="alumni.php" class="btn-secondary">
                                <i class="fas fa-sync"></i> Réinitialiser
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Alumni Grid -->
        <?php if (mysqli_num_rows($alumni_result) > 0): ?>
            <div class="alumni-grid">
                <?php while ($alumnus = mysqli_fetch_assoc($alumni_result)): ?>
                    <div class="alumni-card" data-aos="fade-up">
                        <div class="alumni-card-header">
                            <?php if (!empty($alumnus['profile_image'])): ?>
                                <img src="<?php echo htmlspecialchars($alumnus['profile_image']); ?>" 
                                     alt="Photo de <?php echo htmlspecialchars($alumnus['full_name']); ?>" 
                                     class="alumni-photo">
                            <?php else: ?>
                                <img src="assets/images/default-avatar.png" 
                                     alt="Photo par défaut" 
                                     class="alumni-photo">
                            <?php endif; ?>
                            
                            <h3 class="alumni-name"><?php echo htmlspecialchars($alumnus['full_name']); ?></h3>
                            <p class="alumni-year">Promotion <?php echo htmlspecialchars($alumnus['graduation_year']); ?></p>
                        </div>
                        
                        <div class="alumni-card-body">
                            <p class="alumni-department"><?php echo htmlspecialchars($alumnus['department_name'] ?? 'Département non spécifié'); ?></p>
                            <p class="alumni-position">
                                <i class="fas fa-briefcase"></i> 
                                <?php echo htmlspecialchars($alumnus['current_position']); ?>
                            </p>
                            <?php if (!empty($alumnus['company'])): ?>
                                <p class="alumni-company">
                                    <i class="fas fa-building"></i> 
                                    <?php echo htmlspecialchars($alumnus['company']); ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php if (!empty($alumnus['bio'])): ?>
                                <p class="alumni-bio"><?php echo htmlspecialchars($alumnus['bio']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="alumni-card-footer">
                            <div class="social-links">
                                <?php if (!empty($alumnus['linkedin_url'])): ?>
                                    <a href="<?php echo htmlspecialchars($alumnus['linkedin_url']); ?>" 
                                       target="_blank" 
                                       class="linkedin" 
                                       title="LinkedIn">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <?php if (!empty($alumnus['twitter_url'])): ?>
                                    <a href="<?php echo htmlspecialchars($alumnus['twitter_url']); ?>" 
                                       target="_blank" 
                                       class="twitter" 
                                       title="Twitter">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <?php if (!empty($alumnus['email'])): ?>
                                    <a href="mailto:<?php echo htmlspecialchars($alumnus['email']); ?>" 
                                       class="email" 
                                       title="Email">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <?php if (!empty($alumnus['website_url'])): ?>
                                    <a href="<?php echo htmlspecialchars($alumnus['website_url']); ?>" 
                                       target="_blank" 
                                       class="website" 
                                       title="Site web personnel">
                                        <i class="fas fa-globe"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <div class="no-results-content">
                    <i class="fas fa-search fa-3x"></i>
                    <h3>Aucun résultat trouvé</h3>
                    <p>Aucun alumnus ne correspond à vos critères de recherche. Veuillez modifier vos filtres et réessayer.</p>
                </div>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php include 'includes/footer.php'; ?>