<?php
require_once 'config.php';

// Get event ID
$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get event details
$query = "SELECT * FROM events WHERE id = ? AND status = 'published'";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $event_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header('Location: events.php');
    exit;
}

$event = mysqli_fetch_assoc($result);

// Get registration count
$count_query = "SELECT COUNT(*) as count FROM event_registrations WHERE event_id = ? AND status = 'registered'";
$stmt = mysqli_prepare($conn, $count_query);
mysqli_stmt_bind_param($stmt, "i", $event_id);
mysqli_stmt_execute($stmt);
$count_result = mysqli_stmt_get_result($stmt);
$registration_count = mysqli_fetch_assoc($count_result)['count'];

// Check if user is registered
$is_registered = false;
if (is_logged_in()) {
    $check_query = "SELECT status FROM event_registrations WHERE event_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "ii", $event_id, $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $check_result = mysqli_stmt_get_result($stmt);
    if ($registration = mysqli_fetch_assoc($check_result)) {
        $is_registered = ($registration['status'] == 'registered');
    }
}

$page_title = $event['title'];
include 'includes/header.php';

// Get event type label
$type_labels = [
    'conference' => 'Conférence',
    'workshop' => 'Atelier',
    'career_fair' => 'Forum de carrière',
    'social' => 'Événement social',
    'academic' => 'Événement académique',
    'other' => 'Autre'
];
?>

<div class="page-content">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="events.php">Événements</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($event['title']); ?></li>
            </ol>
        </nav>

        <div class="event-details-container">
            <div class="row">
                <div class="col-md-8">
                    <div class="event-header">
                        <h1><?php echo htmlspecialchars($event['title']); ?></h1>
                        <span class="event-type badge bg-primary">
                            <?php echo $type_labels[$event['type']] ?? ucfirst($event['type']); ?>
                        </span>
                    </div>

                    <div class="event-info-card">
                        <div class="event-meta">
                            <div class="meta-item">
                                <i class="far fa-calendar-alt"></i>
                                <span><?php echo date('d/m/Y', strtotime($event['start_date'])); ?></span>
                            </div>
                            <div class="meta-item">
                                <i class="far fa-clock"></i>
                                <span>
                                    <?php 
                                        echo date('H:i', strtotime($event['start_date']));
                                        if (!empty($event['end_date'])) {
                                            echo ' - ' . date('H:i', strtotime($event['end_date']));
                                        }
                                    ?>
                                </span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($event['location']); ?></span>
                            </div>
                            <?php if (!empty($event['organizer_name'])): ?>
                            <div class="meta-item">
                                <i class="fas fa-user"></i>
                                <span>Organisé par: <?php echo htmlspecialchars($event['organizer_name']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($event['max_participants']): ?>
                        <div class="registration-status">
                            <div class="progress">
                                <?php $percentage = ($registration_count / $event['max_participants']) * 100; ?>
                                <div class="progress-bar" role="progressbar" style="width: <?php echo $percentage; ?>%"
                                     aria-valuenow="<?php echo $registration_count; ?>" 
                                     aria-valuemin="0" 
                                     aria-valuemax="<?php echo $event['max_participants']; ?>">
                                </div>
                            </div>
                            <p class="text-center">
                                <?php echo $registration_count; ?> / <?php echo $event['max_participants']; ?> places réservées
                            </p>
                        </div>
                        <?php endif; ?>

                        <?php if (strtotime($event['start_date']) >= strtotime('today')): ?>
                            <div class="registration-actions">
                                <?php if (is_logged_in()): ?>
                                    <?php if ($is_registered): ?>
                                        <button class="btn btn-success" disabled>Vous êtes inscrit</button>
                                        <button class="btn btn-danger cancel-registration" data-event-id="<?php echo $event_id; ?>">
                                            Annuler mon inscription
                                        </button>
                                    <?php else: ?>
                                        <?php if (!$event['max_participants'] || $registration_count < $event['max_participants']): ?>
                                            <button class="btn btn-primary register-event-btn" data-event-id="<?php echo $event_id; ?>">
                                                S'inscrire à l'événement
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-secondary" disabled>Événement complet</button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <a href="login.php" class="btn btn-primary">Connectez-vous pour vous inscrire</a>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                Cet événement est terminé.
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="event-description">
                        <h2>Description</h2>
                        <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                    </div>

                    <?php if (!empty($event['image_url'])): ?>
                    <div class="event-image">
                        <img src="<?php echo htmlspecialchars($event['image_url']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" class="img-fluid">
                    </div>
                    <?php endif; ?>
                </div>

                <div class="col-md-4">
                    <!-- Similar events -->
                    <?php
                    $similar_query = "SELECT * FROM events 
                                    WHERE status = 'published' 
                                    AND id != ? 
                                    AND type = ?
                                    AND start_date >= CURDATE()
                                    ORDER BY start_date ASC 
                                    LIMIT 3";
                    $stmt = mysqli_prepare($conn, $similar_query);
                    mysqli_stmt_bind_param($stmt, "is", $event_id, $event['type']);
                    mysqli_stmt_execute($stmt);
                    $similar_result = mysqli_stmt_get_result($stmt);
                    
                    if (mysqli_num_rows($similar_result) > 0):
                    ?>
                    <div class="similar-events">
                        <h3>Événements similaires</h3>
                        <?php while ($similar = mysqli_fetch_assoc($similar_result)): ?>
                            <div class="event-card mini">
                                <div class="event-date">
                                    <span class="event-day"><?php echo date('d', strtotime($similar['start_date'])); ?></span>
                                    <span class="event-month"><?php echo date('M', strtotime($similar['start_date'])); ?></span>
                                </div>
                                <div class="event-details">
                                    <h4><?php echo htmlspecialchars($similar['title']); ?></h4>
                                    <p class="event-time">
                                        <i class="far fa-clock"></i> 
                                        <?php echo date('H:i', strtotime($similar['start_date'])); ?>
                                    </p>
                                    <a href="event_details.php?id=<?php echo $similar['id']; ?>" class="btn btn-outline-primary btn-sm">
                                        Plus d'informations
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.event-details-container {
    margin: 2rem 0;
}

.event-header {
    margin-bottom: 2rem;
}

.event-header h1 {
    margin-bottom: 0.5rem;
}

.event-type {
    font-size: 1rem;
    padding: 0.5rem 1rem;
}

.event-info-card {
    background: white;
    border-radius: 10px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow-light);
}

.event-meta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.meta-item i {
    color: var(--primary-color);
    font-size: 1.2rem;
}

.registration-status {
    margin: 1.5rem 0;
}

.registration-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
    justify-content: center;
}

.event-description {
    background: white;
    border-radius: 10px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow-light);
}

.event-description h2 {
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.similar-events {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: var(--shadow-light);
}

.similar-events h3 {
    margin-bottom: 1.5rem;
    font-size: 1.3rem;
}

.event-card.mini {
    margin-bottom: 1rem;
    padding: 1rem;
    border: 1px solid #eee;
    border-radius: 8px;
}

.event-card.mini:last-child {
    margin-bottom: 0;
}

.event-card.mini .event-date {
    padding: 0.5rem;
}

.event-card.mini .event-details {
    padding: 0.5rem;
}

.event-card.mini h4 {
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
}

.event-card.mini .event-time {
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}
</style>

<?php include 'includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle event registration
    const registerButton = document.querySelector('.register-event-btn');
    if (registerButton) {
        registerButton.addEventListener('click', function() {
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
                        location.reload();
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
    }

    // Handle registration cancellation
    const cancelButton = document.querySelector('.cancel-registration');
    if (cancelButton) {
        cancelButton.addEventListener('click', function() {
            const eventId = this.dataset.eventId;
            if (confirm('Voulez-vous annuler votre inscription à cet événement ?')) {
                fetch('cancel_registration.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `event_id=${eventId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Inscription annulée !');
                        location.reload();
                    } else {
                        alert(data.message || 'Une erreur est survenue lors de l\'annulation.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Une erreur est survenue lors de l\'annulation.');
                });
            }
        });
    }
});
</script>