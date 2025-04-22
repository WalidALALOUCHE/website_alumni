<?php
require_once '../config.php';

// Check if user is admin
check_permission('admin');

// Get statistics
// Users by role
$users_query = "SELECT role, COUNT(*) as count FROM users GROUP BY role";
$users_result = mysqli_query($conn, $users_query);
$users_by_role = [];
while ($row = mysqli_fetch_assoc($users_result)) {
    $users_by_role[$row['role']] = $row['count'];
}

// Events statistics
$events_query = "SELECT status, COUNT(*) as count FROM events GROUP BY status";
$events_result = mysqli_query($conn, $events_query);
$events_stats = [
    'pending' => 0,
    'published' => 0,
    'rejected' => 0
];
while ($row = mysqli_fetch_assoc($events_result)) {
    $events_stats[$row['status']] = $row['count'];
}

// Opportunities statistics
$opps_query = "SELECT status, COUNT(*) as count FROM opportunities GROUP BY status";
$opps_result = mysqli_query($conn, $opps_query);
$opps_stats = [
    'pending' => 0,
    'approved' => 0,
    'rejected' => 0
];
while ($row = mysqli_fetch_assoc($opps_result)) {
    $opps_stats[$row['status']] = $row['count'];
}

// Recent registrations pending verification
$pending_users_query = "SELECT * FROM users WHERE verification_status = 'pending' ORDER BY created_at DESC LIMIT 5";
$pending_users_result = mysqli_query($conn, $pending_users_query);

// Recent messages
$messages_query = "SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5";
$messages_result = mysqli_query($conn, $messages_query);

// Recent events
$events_query = "SELECT * FROM events 
                 WHERE status = 'pending' 
                 ORDER BY created_at DESC LIMIT 5";
$recent_events_result = mysqli_query($conn, $events_query);

// Recent opportunities
$opps_query = "SELECT * FROM opportunities WHERE status = 'pending' ORDER BY created_at DESC LIMIT 5";
$recent_opps_result = mysqli_query($conn, $opps_query);

// Recent alumni
$recent_alumni_query = "SELECT u.full_name, u.email, ap.field_of_study, ap.graduation_year, ap.company 
                         FROM alumni_profiles ap 
                         INNER JOIN users u ON ap.user_id = u.id 
                         ORDER BY u.created_at DESC LIMIT 5";
$recent_alumni_result = mysqli_query($conn, $recent_alumni_query);

// Page title
$page_title = "Tableau de bord - Administration";
include '../includes/admin_header.php';
?>

<div class="dashboard-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>ENSAF Admin</h3>
            <p>Panneau d'administration</p>
        </div>
        
        <ul class="sidebar-menu">
            <li>
                <a href="index.php" class="active">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="verify_users.php">
                    <i class="fas fa-user-check"></i>
                    Vérifier Utilisateurs
                    <?php if(isset($users_by_role['pending']) && $users_by_role['pending'] > 0): ?>
                        <span class="badge badge-warning"><?php echo $users_by_role['pending']; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="manage_events.php">
                    <i class="fas fa-calendar-alt"></i>
                    Gérer Événements
                    <?php if(isset($events_stats['pending']) && $events_stats['pending'] > 0): ?>
                        <span class="badge badge-warning"><?php echo $events_stats['pending']; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="approve_opportunities.php">
                    <i class="fas fa-briefcase"></i>
                    Gérer Opportunités
                    <?php if(isset($opps_stats['pending']) && $opps_stats['pending'] > 0): ?>
                        <span class="badge badge-warning"><?php echo $opps_stats['pending']; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="users.php">
                    <i class="fas fa-users"></i>
                    Utilisateurs
                </a>
            </li>
            <li>
                <a href="alumni.php">
                    <i class="fas fa-user-graduate"></i>
                    Alumni
                </a>
            </li>
            <li>
                <a href="departments.php">
                    <i class="fas fa-building"></i>
                    Départements
                </a>
            </li>
            <li>
                <a href="news.php">
                    <i class="fas fa-newspaper"></i>
                    Actualités
                </a>
            </li>
            <li>
                <a href="events.php">
                    <i class="fas fa-calendar-alt"></i>
                    Événements
                </a>
            </li>
            <li>
                <a href="messages.php">
                    <i class="fas fa-envelope"></i>
                    Messages
                </a>
            </li>
            <li>
                <a href="../index.php">
                    <i class="fas fa-home"></i>
                    Site public
                </a>
            </li>
            <li>
                <a href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    Déconnexion
                </a>
            </li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="dashboard-header">
            <h1>Tableau de bord</h1>
            <div class="user-actions">
                <?php if(isset($users_by_role['pending']) && $users_by_role['pending'] > 0): ?>
                    <a href="verify_users.php" class="btn btn-primary">
                        <i class="fas fa-user-check"></i> 
                        Vérifier les utilisateurs 
                        <span class="badge bg-white text-primary"><?php echo $users_by_role['pending']; ?></span>
                    </a>
                <?php endif; ?>
                <div class="user-welcome">
                    Bienvenue, <?php echo $_SESSION['full_name']; ?> | <?php echo date('d/m/Y H:i'); ?>
                </div>
            </div>
        </div>
        
        <div class="stats-cards">
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <div class="number"><?php echo array_sum($users_by_role) ?? 0; ?></div>
                <div class="title">Utilisateurs totaux</div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-user-check"></i>
                <div class="number"><?php echo isset($users_by_role['pending']) ? $users_by_role['pending'] : 0; ?></div>
                <div class="title">Vérifications en attente</div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-calendar-alt"></i>
                <div class="number"><?php echo isset($events_stats['pending']) ? $events_stats['pending'] : 0; ?></div>
                <div class="title">Événements en attente</div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-briefcase"></i>
                <div class="number"><?php echo isset($opps_stats['pending']) ? $opps_stats['pending'] : 0; ?></div>
                <div class="title">Opportunités en attente</div>
            </div>
        </div>
        
        <!-- Recent Pending Verifications -->
        <div class="content-section">
            <div class="section-header">
                <h2><i class="fas fa-user-check"></i> Vérifications récentes</h2>
                <a href="verify_users.php" class="action-link">Voir tout</a>
            </div>
            
            <?php if(mysqli_num_rows($pending_users_result) > 0): ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Date d'inscription</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($user = mysqli_fetch_assoc($pending_users_result)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <a href="verify_users.php" class="btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Examiner
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">Aucune vérification en attente</div>
            <?php endif; ?>
        </div>
        
        <!-- Recent Pending Events -->
        <div class="content-section">
            <div class="section-header">
                <h2><i class="fas fa-calendar-alt"></i> Événements récents</h2>
                <a href="manage_events.php" class="action-link">Voir tout</a>
            </div>
            
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Date</th>
                            <th>Lieu</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($event = mysqli_fetch_assoc($recent_events_result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($event['title']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($event['event_date'])); ?></td>
                                <td><?php echo htmlspecialchars($event['location']); ?></td>
                                <td>
                                    <a href="manage_events.php" class="btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Examiner
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Recent Pending Opportunities -->
        <div class="content-section">
            <div class="section-header">
                <h2><i class="fas fa-briefcase"></i> Opportunités récentes</h2>
                <a href="approve_opportunities.php" class="action-link">Voir tout</a>
            </div>
            
            <?php if(mysqli_num_rows($recent_opps_result) > 0): ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Entreprise</th>
                                <th>Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($opp = mysqli_fetch_assoc($recent_opps_result)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($opp['title']); ?></td>
                                    <td><?php echo htmlspecialchars($opp['company_name']); ?></td>
                                    <td><?php echo htmlspecialchars($opp['type']); ?></td>
                                    <td>
                                        <a href="approve_opportunities.php" class="btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Examiner
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">Aucune opportunité en attente</div>
            <?php endif; ?>
        </div>
        
        <div class="content-section">
            <div class="section-header">
                <h2>Messages récents</h2>
                <a href="messages.php" class="action-link">Voir tous les messages</a>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Sujet</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (mysqli_num_rows($messages_result) > 0) {
                        while ($message = mysqli_fetch_assoc($messages_result)) { 
                            $status_class = 'status-' . $message['status'];
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($message['name']); ?></td>
                        <td><?php echo htmlspecialchars($message['email']); ?></td>
                        <td><?php echo htmlspecialchars($message['subject']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($message['created_at'])); ?></td>
                        <td>
                            <span class="status-badge <?php echo $status_class; ?>">
                                <?php echo ucfirst($message['status']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="view_message.php?id=<?php echo $message['id']; ?>" class="action-link">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo '<tr><td colspan="6" style="text-align: center;">Aucun message reçu</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <div class="content-section">
            <div class="section-header">
                <h2>Nouveaux Alumni</h2>
                <a href="alumni.php" class="action-link">Gérer les alumni</a>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Filière</th>
                        <th>Promotion</th>
                        <th>Entreprise</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (mysqli_num_rows($recent_alumni_result) > 0) {
                        while ($alumni = mysqli_fetch_assoc($recent_alumni_result)) { 
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($alumni['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($alumni['email']); ?></td>
                        <td><?php echo htmlspecialchars($alumni['field_of_study']); ?></td>
                        <td><?php echo htmlspecialchars($alumni['graduation_year']); ?></td>
                        <td><?php echo htmlspecialchars($alumni['company'] ?? '-'); ?></td>
                        <td>
                            <a href="edit_alumni.php?email=<?php echo urlencode($alumni['email']); ?>" class="action-link">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo '<tr><td colspan="6" style="text-align: center;">Aucun alumni enregistré</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .user-actions {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    
    .btn-primary {
        background-color: #800020;
        color: white;
        padding: 8px 16px;
        border-radius: 5px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background-color 0.3s;
    }
    
    .btn-primary:hover {
        background-color: #600018;
        color: white;
    }
    
    .btn-primary .badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
    }
    
    /* Enhance the verification section */
    .content-section:first-of-type {
        border: 2px solid #800020;
        border-radius: 10px;
    }
    
    .content-section:first-of-type .section-header {
        background-color: #800020;
        margin: -20px -20px 20px -20px;
        padding: 15px 20px;
        border-radius: 8px 8px 0 0;
    }
    
    .content-section:first-of-type .section-header h2 {
        color: white;
    }
    
    .content-section:first-of-type .section-header .action-link {
        color: white;
        text-decoration: underline;
    }
</style>

<?php include '../includes/admin_footer.php'; ?>