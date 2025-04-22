<?php
session_start();
require_once 'config.php';

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header("Location: ../../inscription.php?error=csrf");
    exit;
}

$required_fields = ['nom', 'prenom', 'email', 'password', 'date_naissance', 'adresse', 'telephone'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        header("Location: ../../inscription.php?error=missing_fields");
        exit;
    }
}

$nom = htmlspecialchars(trim($_POST['nom']));
$prenom = htmlspecialchars(trim($_POST['prenom']));
$email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
$password = $_POST['password'];
$date_naissance = $_POST['date_naissance'];
$adresse = htmlspecialchars(trim($_POST['adresse']));
$telephone = preg_match('/^[0-9]{10}$/', $_POST['telephone']) ? $_POST['telephone'] : null;

if (!$email || !$telephone) {
    header("Location: ../../inscription.php?error=invalid_data");
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();
if ($stmt->fetch()) {
    header("Location: ../../inscription.php?error=email_taken");
    exit;
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, email, mot_de_passe, date_de_naissance, adresse_postale, telephone) 
                           VALUES (:nom, :prenom, :email, :password, :date_naissance, :adresse, :telephone)");
    $stmt->execute([
        ':nom' => $nom,
        ':prenom' => $prenom,
        ':email' => $email,
        ':password' => $hashed_password,
        ':date_naissance' => $date_naissance,
        ':adresse' => $adresse,
        ':telephone' => $telephone
    ]);

    header("Location: ../../index.php?success=1");
    exit;
} catch (PDOException $e) {
    header("Location: ../../inscription.php?error=database");
    exit;
}
?>
