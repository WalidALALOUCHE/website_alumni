<?php
require_once 'config.php';

// Ensure user is logged in
if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour annuler une inscription.']);
    exit;
}

// Validate event_id
if (!isset($_POST['event_id']) || !is_numeric($_POST['event_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID d\'événement invalide.']);
    exit;
}

$event_id = (int)$_POST['event_id'];
$user_id = $_SESSION['user_id'];

// Check if registration exists
$check_query = "SELECT r.*, e.start_date 
                FROM event_registrations r 
                JOIN events e ON r.event_id = e.id 
                WHERE r.event_id = ? AND r.user_id = ? AND r.status = 'registered'";
$stmt = mysqli_prepare($conn, $check_query);
mysqli_stmt_bind_param($stmt, "ii", $event_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    echo json_encode(['success' => false, 'message' => 'Aucune inscription trouvée.']);
    exit;
}

$registration = mysqli_fetch_assoc($result);

// Check if event hasn't already started
if (strtotime($registration['start_date']) <= time()) {
    echo json_encode(['success' => false, 'message' => 'Vous ne pouvez plus annuler votre inscription car l\'événement a déjà commencé.']);
    exit;
}

// Update registration status to cancelled
$cancel_query = "UPDATE event_registrations SET status = 'cancelled' WHERE event_id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $cancel_query);
mysqli_stmt_bind_param($stmt, "ii", $event_id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => 'Inscription annulée avec succès.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Une erreur est survenue lors de l\'annulation.']);
}