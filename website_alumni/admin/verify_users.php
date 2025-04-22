<?php
require_once '../config.php';

// Check if user is logged in and has admin role
check_permission('admin');

$success_message = '';
$error_message = '';

// Process verification action
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && isset($_POST['user_id'])) {
        $user_id = intval($_POST['user_id']);
        $action = $_POST['action'];
        
        if ($action == 'verify') {
            // Update user verification status to verified
            $stmt = mysqli_prepare($conn, "UPDATE users SET verification_status = 'verified', status = 'active' WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            if (mysqli_stmt_execute($stmt)) {
                $success_message = "L'utilisateur a été vérifié avec succès.";
            } else {
                $error_message = "Erreur lors de la vérification de l'utilisateur: " . mysqli_error($conn);
            }
        } elseif ($action == 'reject') {
            // Update user verification status to rejected
            $stmt = mysqli_prepare($conn, "UPDATE users SET verification_status = 'rejected' WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            if (mysqli_stmt_execute($stmt)) {
                $success_message = "La vérification de l'utilisateur a été rejetée.";
            } else {
                $error_message = "Erreur lors du rejet de la vérification: " . mysqli_error($conn);
            }
        }
    }
}

// Get all users with pending verification
$stmt = mysqli_prepare($conn, "SELECT id, username, email, full_name, role, created_at, verification_document FROM users WHERE verification_status = 'pending' ORDER BY created_at DESC");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$pending_users = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get all users with verification status
$stmt = mysqli_prepare($conn, "SELECT id, username, email, full_name, role, created_at, verification_status, verification_document FROM users WHERE verification_status != 'pending' ORDER BY created_at DESC");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$verified_users = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Page title
$page_title = "Gestion des vérifications";
include '../includes/admin_header.php';
?>

<div class="admin-content">
    <div class="container">
        <h1>Gestion des vérifications</h1>
        
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
            <h2>Vérifications en attente</h2>
            
            <?php if (empty($pending_users)): ?>
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <p>Aucune vérification en attente</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Nom d'utilisateur</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Date d'inscription</th>
                                <th>Document</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pending_users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $user['role']; ?>">
                                            <?php 
                                                switch($user['role']) {
                                                    case 'student': echo 'Étudiant'; break;
                                                    case 'professor': echo 'Enseignant'; break;
                                                    case 'alumni': echo 'Lauréat'; break;
                                                    default: echo ucfirst($user['role']); break;
                                                }
                                            ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <?php if (!empty($user['verification_document'])): ?>
                                            <a href="<?php echo ROOT_URL . '/' . $user['verification_document']; ?>" target="_blank" class="btn btn-sm btn-secondary">
                                                <i class="fas fa-file-alt"></i> Voir document
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Aucun document</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="actions">
                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="action" value="verify">
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Êtes-vous sûr de vouloir vérifier cet utilisateur ?')">
                                                <i class="fas fa-check"></i> Vérifier
                                            </button>
                                        </form>
                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir rejeter cette vérification ?')">
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
            <h2>Historique de vérification</h2>
            
            <?php if (empty($verified_users)): ?>
                <div class="empty-state">
                    <i class="fas fa-history"></i>
                    <p>Aucun historique de vérification</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Nom d'utilisateur</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Statut</th>
                                <th>Date d'inscription</th>
                                <th>Document</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($verified_users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $user['role']; ?>">
                                            <?php 
                                                switch($user['role']) {
                                                    case 'student': echo 'Étudiant'; break;
                                                    case 'professor': echo 'Enseignant'; break;
                                                    case 'alumni': echo 'Lauréat'; break;
                                                    default: echo ucfirst($user['role']); break;
                                                }
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($user['verification_status'] == 'verified'): ?>
                                            <span class="badge badge-success">Vérifié</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Rejeté</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <?php if (!empty($user['verification_document'])): ?>
                                            <a href="<?php echo ROOT_URL . '/' . $user['verification_document']; ?>" target="_blank" class="btn btn-sm btn-secondary">
                                                <i class="fas fa-file-alt"></i> Voir document
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Aucun document</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/admin_footer.php'; ?> 