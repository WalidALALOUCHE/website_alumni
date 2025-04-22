<?php
require_once 'config.php';

// Set page title
$page_title = "Événements";

// Get filter parameters
$type = isset($_GET['type']) ? sanitize_input($_GET['type']) : '';
$date_filter = isset($_GET['date']) ? sanitize_input($_GET['date']) : 'upcoming'; // upcoming, past, all
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';

// Build query based on filters
$query = "SELECT * FROM events WHERE status = 'published'";
$params = [];
$param_types = "";

if (!empty($type)) {
    $query .= " AND type = ?";
    $params[] = $type;
    $param_types .= "s";
}

if ($date_filter == 'upcoming') {
    $query .= " AND start_date >= NOW()";
} elseif ($date_filter == 'past') {
    $query .= " AND start_date < NOW()";
}

if (!empty($search)) {
    $query .= " AND (title LIKE ? OR description LIKE ? OR location LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $param_types .= "sss";
}

$query .= " ORDER BY start_date ASC";

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

<div class="page-content">
    <div class="container">
        <h1 class="page-title"><?php echo $page_title; ?></h1>
        
        <!-- Filters -->
        <div class="filters-section">
            <form action="" method="GET" class="filters-form">
                <div class="row g-3">
                    <div class="col-md-3">
                        <select name="type" class="form-select">
                            <option value="">Tous les types</option>
                            <option value="conference" <?php echo $type == 'conference' ? 'selected' : ''; ?>>Conférences</option>
                            <option value="workshop" <?php echo $type == 'workshop' ? 'selected' : ''; ?>>Ateliers</option>
                            <option value="career_fair" <?php echo $type == 'career_fair' ? 'selected' : ''; ?>>Forums de carrière</option>
                            <option value="social" <?php echo $type == 'social' ? 'selected' : ''; ?>>Événements sociaux</option>
                            <option value="academic" <?php echo $type == 'academic' ? 'selected' : ''; ?>>Événements académiques</option>
                            <option value="other" <?php echo $type == 'other' ? 'selected' : ''; ?>>Autres</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="date" class="form-select">
                            <option value="upcoming" <?php echo $date_filter == 'upcoming' ? 'selected' : ''; ?>>À venir</option>
                            <option value="past" <?php echo $date_filter == 'past' ? 'selected' : ''; ?>>Passés</option>
                            <option value="all" <?php echo $date_filter == 'all' ? 'selected' : ''; ?>>Tous</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Rechercher un événement..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Events listing -->
        <div class="events-container">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($event = mysqli_fetch_assoc($result)): ?>
                    <div class="event-card">
                        <div class="event-date">
                            <span class="event-day"><?php echo date('d', strtotime($event['start_date'])); ?></span>
                            <span class="event-month"><?php echo date('M', strtotime($event['start_date'])); ?></span>
                        </div>
                        <div class="event-details">
                            <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                            <div class="event-meta">
                                <p class="event-time">
                                    <i class="far fa-clock"></i> 
                                    <?php 
                                        echo date('H:i', strtotime($event['start_date']));
                                        if (!empty($event['end_date'])) {
                                            echo ' - ' . date('H:i', strtotime($event['end_date']));
                                        }
                                    ?>
                                </p>
                                <p class="event-location">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    <?php echo htmlspecialchars($event['location']); ?>
                                </p>
                                <?php if ($event['max_participants']): ?>
                                    <p class="event-capacity">
                                        <i class="fas fa-users"></i>
                                        Places limitées: <?php echo $event['max_participants']; ?> participants
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="event-description">
                                <?php 
                                    $description = $event['description'];
                                    echo nl2br(htmlspecialchars(substr($description, 0, 200)));
                                    if (strlen($description) > 200) echo '...';
                                ?>
                            </div>
                            <div class="event-actions">
                                <a href="event_details.php?id=<?php echo $event['id']; ?>" class="btn btn-outline-primary">
                                    Plus d'informations
                                </a>
                                <?php if (strtotime($event['start_date']) >= strtotime('today')): ?>
                                    <?php if (is_logged_in()): ?>
                                        <button class="btn btn-primary register-event-btn" data-event-id="<?php echo $event['id']; ?>">
                                            S'inscrire
                                        </button>
                                    <?php else: ?>
                                        <a href="login.php" class="btn btn-primary">
                                            Connectez-vous pour vous inscrire
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    Aucun événement ne correspond à vos critères de recherche.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (isset($_SESSION['user_id'])): ?>
    <div class="share-section">
        <div class="container">
            <div class="share-content">
                <div class="share-text">
                    <h2><i class="fas fa-calendar-plus"></i> Vous organisez un événement ?</h2>
                    <p>Partagez vos événements avec la communauté ENSAF : conférences, ateliers, formations, rencontres professionnelles...</p>
                </div>
                <div class="share-actions">
                    <a href="submit_event.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus-circle"></i> Proposer un événement
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
                    <h2><i class="fas fa-calendar-plus"></i> Vous organisez un événement ?</h2>
                    <p>Connectez-vous pour partager vos événements avec la communauté ENSAF.</p>
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

<style>
/* Improved events page styling */
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

/* Enhanced existing styles */
.events-container {
    margin: 2rem 0;
}

.filters-section {
    background-color: white;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: var(--shadow-light);
    margin-bottom: 2rem;
}

.event-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.event-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-dark);
}
</style>

<?php include 'includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle event registration
    const registerButtons = document.querySelectorAll('.register-event-btn');
    registerButtons.forEach(button => {
        button.addEventListener('click', function() {
            const eventId = this.dataset.eventId;
            if (confirm('Voulez-vous vous inscrire à cet événement ?')) {
                fetch('register_event.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `event_id=${eventId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Inscription réussie !');
                        this.disabled = true;
                        this.textContent = 'Inscrit';
                    } else {
                        alert(data.message || 'Une erreur est survenue lors de l\'inscription.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Une erreur est survenue lors de l\'inscription.');
                });
            }
        });
    });
});
</script>