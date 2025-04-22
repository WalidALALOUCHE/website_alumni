<?php
require_once '../config.php';

// Check if user is logged in and has admin role
check_permission('admin');

$success_message = '';
$error_message = '';

// Process event actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && isset($_POST['event_id'])) {
        $event_id = intval($_POST['event_id']);
        $action = $_POST['action'];
        
        if ($action == 'approve') {
            $stmt = mysqli_prepare($conn, "UPDATE events SET status = 'published' WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $event_id);
            if (mysqli_stmt_execute($stmt)) {
                $success_message = "L'événement a été approuvé avec succès.";
            } else {
                $error_message = "Erreur lors de l'approbation de l'événement: " . mysqli_error($conn);
            }
        } elseif ($action == 'reject') {
            $stmt = mysqli_prepare($conn, "UPDATE events SET status = 'rejected' WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $event_id);
            if (mysqli_stmt_execute($stmt)) {
                $success_message = "L'événement a été rejeté.";
            } else {
                $error_message = "Erreur lors du rejet de l'événement: " . mysqli_error($conn);
            }
        } elseif ($action == 'delete') {
            $stmt = mysqli_prepare($conn, "DELETE FROM events WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $event_id);
            if (mysqli_stmt_execute($stmt)) {
                $success_message = "L'événement a été supprimé avec succès.";
            } else {
                $error_message = "Erreur lors de la suppression de l'événement: " . mysqli_error($conn);
            }
        }
    }
}

// Get pending events
$stmt = mysqli_prepare($conn, "SELECT e.*, u.full_name as organizer_name 
                             FROM events e 
                             LEFT JOIN users u ON e.organizer_id = u.id 
                             WHERE e.status = 'pending' 
                             ORDER BY e.event_date DESC");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$pending_events = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get all other events
$stmt = mysqli_prepare($conn, "SELECT e.*, u.full_name as organizer_name 
                             FROM events e 
                             LEFT JOIN users u ON e.organizer_id = u.id 
                             WHERE e.status != 'pending' 
                             ORDER BY e.event_date DESC");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$other_events = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Page title
$page_title = "Gestion des Événements";
include '../includes/admin_header.php';
?>

<div class="admin-content">
    <div class="container">
        <h1>Gestion des Événements</h1>
        
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
        
        <div class="admin-section">
            <h2>Événements en attente d'approbation</h2>
            
            <?php if (empty($pending_events)): ?>
                <div class="empty-state">
                    <i class="fas fa-calendar-check"></i>
                    <p>Aucun événement en attente d'approbation</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Organisateur</th>
                                <th>Date</th>
                                <th>Lieu</th>
                                <th>Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pending_events as $event): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($event['title']); ?></td>
                                    <td><?php echo htmlspecialchars($event['organizer_name']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($event['event_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($event['location']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $event['type']; ?>">
                                            <?php echo ucfirst($event['type']); ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <button type="button" class="btn btn-sm btn-info view-details" 
                                                data-id="<?php echo $event['id']; ?>"
                                                data-title="<?php echo htmlspecialchars($event['title']); ?>"
                                                data-description="<?php echo htmlspecialchars($event['description']); ?>">
                                            <i class="fas fa-eye"></i> Voir
                                        </button>
                                        
                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i> Approuver
                                            </button>
                                        </form>
                                        
                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-times"></i> Rejeter
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="admin-section">
            <h2>Tous les Événements</h2>
            
            <?php if (empty($other_events)): ?>
                <div class="empty-state">
                    <i class="fas fa-calendar"></i>
                    <p>Aucun événement</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Organisateur</th>
                                <th>Date</th>
                                <th>Lieu</th>
                                <th>Type</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($other_events as $event): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($event['title']); ?></td>
                                    <td><?php echo htmlspecialchars($event['organizer_name']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($event['event_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($event['location']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $event['type']; ?>">
                                            <?php echo ucfirst($event['type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $event['status']; ?>">
                                            <?php echo ucfirst($event['status']); ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <button type="button" class="btn btn-sm btn-info view-details" 
                                                data-id="<?php echo $event['id']; ?>"
                                                data-title="<?php echo htmlspecialchars($event['title']); ?>"
                                                data-description="<?php echo htmlspecialchars($event['description']); ?>">
                                            <i class="fas fa-eye"></i> Voir
                                        </button>
                                        
                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Modal for viewing event details -->
    <div class="modal" id="eventModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Détails de l'Événement</h5>
                    <button type="button" class="modal-close" id="closeModal">&times;</button>
                </div>
                <div class="modal-body">
                    <h3 id="modal-title"></h3>
                    <div id="modal-description"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-sm btn-secondary" id="closeModalBtn">Fermer</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const viewButtons = document.querySelectorAll('.view-details');
    const modal = document.getElementById('eventModal');
    const closeModal = document.getElementById('closeModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const title = this.getAttribute('data-title');
            const description = this.getAttribute('data-description');
            
            document.getElementById('modal-title').textContent = title;
            document.getElementById('modal-description').innerHTML = description;
            
            modal.classList.add('show');
        });
    });
    
    closeModal.addEventListener('click', function() {
        modal.classList.remove('show');
    });
    
    closeModalBtn.addEventListener('click', function() {
        modal.classList.remove('show');
    });
    
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.classList.remove('show');
        }
    });
});
</script>

<?php include '../includes/admin_footer.php'; ?>