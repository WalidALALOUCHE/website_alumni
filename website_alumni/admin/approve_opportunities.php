<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin"){
    header("location: ../login.php");
    exit;
}

// Include config file
require_once "../config.php";

// Process approval action if submitted
if(isset($_POST["action"]) && !empty($_POST["id"])) {
    $id = $_POST["id"];
    
    if($_POST["action"] == "approve") {
        $sql = "UPDATE opportunities SET status = 'approved' WHERE id = ?";
        $success_message = "L'opportunité a été approuvée avec succès.";
    } else if($_POST["action"] == "reject") {
        $sql = "UPDATE opportunities SET status = 'rejected' WHERE id = ?";
        $success_message = "L'opportunité a été rejetée.";
    }
    
    if(isset($sql)) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        // Set success message
        $_SESSION['success_message'] = $success_message;
    }
    
    // Redirect to avoid form resubmission on refresh
    header("location: approve_opportunities.php");
    exit;
}

// Fetch pending opportunities
$pending_query = "SELECT * FROM opportunities WHERE status = 'pending' ORDER BY created_at DESC";
$pending_result = mysqli_query($conn, $pending_query);

// Get opportunity statistics
$stats_query = "SELECT status, COUNT(*) as count FROM opportunities GROUP BY status";
$stats_result = mysqli_query($conn, $stats_query);
$stats = [
    'approved' => 0,
    'rejected' => 0,
    'pending' => 0
];

if($stats_result) {
    while($row = mysqli_fetch_assoc($stats_result)) {
        $stats[$row['status']] = $row['count'];
    }
}

// Get recent approved opportunities
$recent_approved_query = "SELECT * FROM opportunities WHERE status = 'approved' ORDER BY created_at DESC LIMIT 5";
$recent_approved_result = mysqli_query($conn, $recent_approved_query);

// Get recent rejected opportunities
$recent_rejected_query = "SELECT * FROM opportunities WHERE status = 'rejected' ORDER BY created_at DESC LIMIT 5";
$recent_rejected_result = mysqli_query($conn, $recent_rejected_query);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approuver les Opportunités - ENSAF Admin</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background-color: #1B263B;
            color: white;
            padding: 20px 0;
        }
        
        .sidebar-header {
            padding: 0 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
        }
        
        .sidebar-menu li {
            margin-bottom: 5px;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: #800020;
        }
        
        .sidebar-menu i {
            margin-right: 10px;
            width: 20px;
        }
        
        .main-content {
            flex: 1;
            padding: 20px;
            background-color: #f4f4f4;
        }
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .user-welcome {
            font-size: 14px;
        }
        
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .stat-card .number {
            font-size: 32px;
            font-weight: bold;
            color: #800020;
            margin: 10px 0;
        }
        
        .stat-card .title {
            color: #777;
            font-size: 14px;
        }
        
        .stat-card i {
            font-size: 40px;
            color: #1B263B;
            margin-bottom: 10px;
        }
        
        .content-section {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        
        .section-header h2 {
            margin: 0;
            font-size: 20px;
        }
        
        .action-link {
            color: #800020;
            text-decoration: none;
        }
        
        .action-link:hover {
            text-decoration: underline;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th, .data-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .data-table th {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        
        .data-table tr:hover {
            background-color: #f5f5f5;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
            border-radius: 3px;
            margin-right: 5px;
            display: inline-block;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }
        
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-info {
            background-color: #17a2b8;
            color: white;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            color: white;
        }
        
        .status-approved {
            background-color: #28a745;
        }
        
        .status-rejected {
            background-color: #dc3545;
        }
        
        .status-pending {
            background-color: #ffc107;
            color: #212529;
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        
        .modal.show {
            display: block;
        }
        
        .modal-dialog {
            max-width: 800px;
            margin: 30px auto;
        }
        
        .modal-content {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .modal-header {
            background-color: #1B263B;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-title {
            margin: 0;
            font-size: 20px;
        }
        
        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .modal-footer {
            padding: 15px 20px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
        }
        
        .modal-footer button {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="sidebar-header">
                <h3>ENSAF Admin</h3>
                <p>Panneau d'administration</p>
            </div>
            
            <ul class="sidebar-menu">
                <li>
                    <a href="index.php">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
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
                    <a href="approve_opportunities.php" class="active">
                        <i class="fas fa-check-circle"></i>
                        Approuver Opportunités
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
                <h1>Approuver les Opportunités</h1>
                <div class="user-welcome">
                    Bienvenue, <?php echo $_SESSION['full_name'] ?? $_SESSION['username']; ?> | <?php echo date('d/m/Y H:i'); ?>
                </div>
            </div>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?php 
                        echo $_SESSION['success_message']; 
                        unset($_SESSION['success_message']);
                    ?>
                </div>
            <?php endif; ?>
            
            <div class="stats-cards">
                <div class="stat-card">
                    <i class="fas fa-check-circle"></i>
                    <div class="number"><?php echo $stats['approved']; ?></div>
                    <div class="title">Opportunités Approuvées</div>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-times-circle"></i>
                    <div class="number"><?php echo $stats['rejected']; ?></div>
                    <div class="title">Opportunités Rejetées</div>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-clock"></i>
                    <div class="number"><?php echo $stats['pending']; ?></div>
                    <div class="title">Opportunités en Attente</div>
                </div>
            </div>
            
            <div class="content-section">
                <div class="section-header">
                    <h2><i class="fas fa-clock"></i> Opportunités en Attente</h2>
                </div>
                
                <?php if($pending_result && mysqli_num_rows($pending_result) > 0): ?>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Entreprise</th>
                                    <th>Type</th>
                                    <th>Localisation</th>
                                    <th>Date de publication</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_assoc($pending_result)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row["title"]); ?></td>
                                        <td><?php echo htmlspecialchars($row["company_name"]); ?></td>
                                        <td>
                                            <?php 
                                                $type_labels = [
                                                    'internship' => 'Stage',
                                                    'job' => 'Emploi',
                                                    'project' => 'Projet',
                                                    'training' => 'Formation'
                                                ];
                                                echo $type_labels[$row['type']] ?? ucfirst($row['type']);
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row["location"]); ?></td>
                                        <td><?php echo date("d/m/Y", strtotime($row["created_at"])); ?></td>
                                        <td>
                                            <button type="button" class="btn-sm btn-info view-details" 
                                                    data-id="<?php echo $row['id']; ?>"
                                                    data-title="<?php echo htmlspecialchars($row['title']); ?>"
                                                    data-company="<?php echo htmlspecialchars($row['company_name']); ?>"
                                                    data-type="<?php echo htmlspecialchars($row['type']); ?>"
                                                    data-location="<?php echo htmlspecialchars($row['location']); ?>"
                                                    data-description="<?php echo htmlspecialchars($row['description']); ?>"
                                                    data-requirements="<?php echo htmlspecialchars($row['requirements']); ?>"
                                                    data-apply_url="<?php echo htmlspecialchars($row['apply_url']); ?>"
                                                    data-deadline="<?php echo htmlspecialchars($row['deadline']); ?>">
                                                <i class="fas fa-eye"></i> Voir
                                            </button>
                                            
                                            <form method="post" class="d-inline" style="display: inline;">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="btn-sm btn-success">
                                                    <i class="fas fa-check"></i> Approuver
                                                </button>
                                            </form>
                                            
                                            <form method="post" class="d-inline" style="display: inline;">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="btn-sm btn-danger">
                                                    <i class="fas fa-times"></i> Rejeter
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">Aucune opportunité en attente d'approbation pour le moment.</div>
                <?php endif; ?>
            </div>
            
            <div class="content-section">
                <div class="section-header">
                    <h2><i class="fas fa-check-circle"></i> Opportunités Récemment Approuvées</h2>
                </div>
                
                <?php if($recent_approved_result && mysqli_num_rows($recent_approved_result) > 0): ?>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Entreprise</th>
                                    <th>Type</th>
                                    <th>Localisation</th>
                                    <th>Date de publication</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_assoc($recent_approved_result)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row["title"]); ?></td>
                                        <td><?php echo htmlspecialchars($row["company_name"]); ?></td>
                                        <td>
                                            <?php 
                                                $type_labels = [
                                                    'internship' => 'Stage',
                                                    'job' => 'Emploi',
                                                    'project' => 'Projet',
                                                    'training' => 'Formation'
                                                ];
                                                echo $type_labels[$row['type']] ?? ucfirst($row['type']);
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row["location"]); ?></td>
                                        <td><?php echo date("d/m/Y", strtotime($row["created_at"])); ?></td>
                                        <td><span class="status-badge status-approved">Approuvée</span></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">Aucune opportunité approuvée pour le moment.</div>
                <?php endif; ?>
            </div>
            
            <div class="content-section">
                <div class="section-header">
                    <h2><i class="fas fa-times-circle"></i> Opportunités Récemment Rejetées</h2>
                </div>
                
                <?php if($recent_rejected_result && mysqli_num_rows($recent_rejected_result) > 0): ?>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Entreprise</th>
                                    <th>Type</th>
                                    <th>Localisation</th>
                                    <th>Date de publication</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_assoc($recent_rejected_result)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row["title"]); ?></td>
                                        <td><?php echo htmlspecialchars($row["company_name"]); ?></td>
                                        <td>
                                            <?php 
                                                $type_labels = [
                                                    'internship' => 'Stage',
                                                    'job' => 'Emploi',
                                                    'project' => 'Projet',
                                                    'training' => 'Formation'
                                                ];
                                                echo $type_labels[$row['type']] ?? ucfirst($row['type']);
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row["location"]); ?></td>
                                        <td><?php echo date("d/m/Y", strtotime($row["created_at"])); ?></td>
                                        <td><span class="status-badge status-rejected">Rejetée</span></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">Aucune opportunité rejetée pour le moment.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Modal for viewing opportunity details -->
    <div class="modal" id="opportunityModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Détails de l'Opportunité</h5>
                    <button type="button" class="modal-close" id="closeModal">&times;</button>
                </div>
                <div class="modal-body">
                    <h3 id="modal-title"></h3>
                    <p><strong>Entreprise:</strong> <span id="modal-company"></span></p>
                    <p><strong>Type:</strong> <span id="modal-type"></span></p>
                    <p><strong>Localisation:</strong> <span id="modal-location"></span></p>
                    <p><strong>Date limite:</strong> <span id="modal-deadline"></span></p>
                    
                    <h5>Description</h5>
                    <div id="modal-description" class="mb-3" style="margin-bottom: 15px;"></div>
                    
                    <h5>Prérequis</h5>
                    <div id="modal-requirements" class="mb-3" style="margin-bottom: 15px;"></div>
                    
                    <p><strong>URL de candidature:</strong> <a id="modal-apply-url" href="#" target="_blank"></a></p>
                </div>
                <div class="modal-footer">
                    <form method="post" style="margin-right: auto;">
                        <input type="hidden" name="id" id="modal-reject-id">
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" class="btn-sm btn-danger">
                            <i class="fas fa-times"></i> Rejeter
                        </button>
                    </form>
                    <form method="post">
                        <input type="hidden" name="id" id="modal-approve-id">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="btn-sm btn-success">
                            <i class="fas fa-check"></i> Approuver
                        </button>
                    </form>
                    <button type="button" class="btn-sm btn-secondary" id="closeModalBtn">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Wait for the DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Get all view details buttons
            const viewButtons = document.querySelectorAll('.view-details');
            const modal = document.getElementById('opportunityModal');
            const closeModal = document.getElementById('closeModal');
            const closeModalBtn = document.getElementById('closeModalBtn');
            
            // Add click event listener to each view button
            viewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Get data attributes
                    const id = this.getAttribute('data-id');
                    const title = this.getAttribute('data-title');
                    const company = this.getAttribute('data-company');
                    const type = this.getAttribute('data-type');
                    const location = this.getAttribute('data-location');
                    const description = this.getAttribute('data-description');
                    const requirements = this.getAttribute('data-requirements');
                    const applyUrl = this.getAttribute('data-apply_url');
                    const deadline = this.getAttribute('data-deadline');
                    
                    // Set modal content
                    document.getElementById('modal-title').textContent = title;
                    document.getElementById('modal-company').textContent = company;
                    
                    // Translate type to French
                    const typeLabels = {
                        'internship': 'Stage',
                        'job': 'Emploi',
                        'project': 'Projet',
                        'training': 'Formation'
                    };
                    
                    document.getElementById('modal-type').textContent = typeLabels[type] || type;
                    document.getElementById('modal-location').textContent = location;
                    document.getElementById('modal-description').textContent = description;
                    document.getElementById('modal-requirements').textContent = requirements;
                    
                    // Format the deadline date
                    if (deadline) {
                        const date = new Date(deadline);
                        const formattedDate = date.toLocaleDateString('fr-FR');
                        document.getElementById('modal-deadline').textContent = formattedDate;
                    } else {
                        document.getElementById('modal-deadline').textContent = 'Non spécifiée';
                    }
                    
                    // Set the apply URL
                    const applyUrlElement = document.getElementById('modal-apply-url');
                    if (applyUrl) {
                        applyUrlElement.href = applyUrl;
                        applyUrlElement.textContent = applyUrl;
                    } else {
                        applyUrlElement.textContent = 'Non spécifiée';
                        applyUrlElement.removeAttribute('href');
                    }
                    
                    // Set the IDs for the approval/rejection forms
                    document.getElementById('modal-approve-id').value = id;
                    document.getElementById('modal-reject-id').value = id;
                    
                    // Show the modal
                    modal.classList.add('show');
                });
            });
            
            // Close modal when the X button is clicked
            closeModal.addEventListener('click', function() {
                modal.classList.remove('show');
            });
            
            // Close modal when the Close button is clicked
            closeModalBtn.addEventListener('click', function() {
                modal.classList.remove('show');
            });
            
            // Close modal when clicking outside the modal content
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.classList.remove('show');
                }
            });
        });
    </script>
</body>
</html> 