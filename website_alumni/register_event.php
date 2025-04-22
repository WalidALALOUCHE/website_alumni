<?php
require_once 'config.php';

// Ensure user is logged in
if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour vous inscrire à un événement.']);
    exit;
}

// Validate event_id
if (!isset($_POST['event_id']) || !is_numeric($_POST['event_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID d\'événement invalide.']);
    exit;
}

$event_id = (int)$_POST['event_id'];
$user_id = $_SESSION['user_id'];

// Check if event exists and is still open for registration
$event_query = "SELECT * FROM events WHERE id = ? AND status = 'published' AND start_date >= NOW()";
$stmt = mysqli_prepare($conn, $event_query);
mysqli_stmt_bind_param($stmt, "i", $event_id);
mysqli_stmt_execute($stmt);
$event_result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($event_result) == 0) {
    echo json_encode(['success' => false, 'message' => 'Événement non trouvé ou inscription fermée.']);
    exit;
}

$event = mysqli_fetch_assoc($event_result);

// Check if user is already registered
$check_query = "SELECT * FROM event_registrations WHERE event_id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $check_query);
mysqli_stmt_bind_param($stmt, "ii", $event_id, $user_id);
mysqli_stmt_execute($stmt);
$check_result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($check_result) > 0) {
    echo json_encode(['success' => false, 'message' => 'Vous êtes déjà inscrit à cet événement.']);
    exit;
}

// Check if event has reached maximum participants
if ($event['max_participants']) {
    $count_query = "SELECT COUNT(*) as count FROM event_registrations WHERE event_id = ? AND status != 'cancelled'";
    $stmt = mysqli_prepare($conn, $count_query);
    mysqli_stmt_bind_param($stmt, "i", $event_id);
    mysqli_stmt_execute($stmt);
    $count_result = mysqli_stmt_get_result($stmt);
    $count = mysqli_fetch_assoc($count_result)['count'];
    
    if ($count >= $event['max_participants']) {
        echo json_encode(['success' => false, 'message' => 'L\'événement est complet.']);
        exit;
    }
}

// Register the user
$register_query = "INSERT INTO event_registrations (event_id, user_id, status) VALUES (?, ?, 'registered')";
$stmt = mysqli_prepare($conn, $register_query);
mysqli_stmt_bind_param($stmt, "ii", $event_id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => 'Inscription réussie !']);
} else {
    echo json_encode(['success' => false, 'message' => 'Une erreur est survenue lors de l\'inscription.']);
}