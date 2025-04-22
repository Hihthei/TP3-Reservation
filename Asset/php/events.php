<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $titre = htmlspecialchars($_POST['titre']);
    $description = htmlspecialchars($_POST['description']);
    $date_heure_debut = $_POST['date_heure_debut'];
    
    $date_heure_fin = date('Y-m-d H:i:s', strtotime($date_heure_debut . ' +1 hour'));

    $stmt = $pdo->prepare("INSERT INTO events (user_id, titre, description, date_heure_debut, date_heure_fin) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $titre, $description, $date_heure_debut, $date_heure_fin]);

    header("Location: ../../calendar.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $event_id = $_POST['event_id'];
    $titre = htmlspecialchars($_POST['titre']);
    $description = htmlspecialchars($_POST['description']);
    
    $stmt = $pdo->prepare("SELECT id FROM events WHERE id = ? AND user_id = ?");
    $stmt->execute([$event_id, $user_id]);
    
    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->prepare("UPDATE events SET titre = ?, description = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$titre, $description, $event_id, $user_id]);
    }
    
    header("Location: ../../events.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $event_id = $_POST['event_id'];
    
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ? AND user_id = ?");
    $stmt->execute([$event_id, $user_id]);
    
    header("Location: ../../calendar.php");
    exit();
}
?>