<?php
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de Réservation - Inscription</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Asset/css/style.css">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg">
            <h2 class="text-center">Créer un compte</h2>
            <form action="Asset/php/inscription.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="mb-3">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="nom" name="nom" required>
                </div>
                
                <div class="mb-3">
                    <label for="prenom" class="form-label">Prénom</label>
                    <input type="text" class="form-control" id="prenom" name="prenom" required>
                </div>
                
                <div class="mb-3">
                    <label for="date_naissance" class="form-label">Date de naissance</label>
                    <input type="date" class="form-control" id="date_naissance" name="date_naissance" required>
                </div>
                
                <div class="mb-3">
                    <label for="adresse" class="form-label">Adresse postale</label>
                    <input type="text" class="form-control" id="adresse" name="adresse" required>
                </div>
                
                <div class="mb-3">
                    <label for="telephone" class="form-label">Téléphone</label>
                    <input type="tel" class="form-control" id="telephone" name="telephone" pattern="[0-9]{10}" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" required minlength="6">
                </div>
                
                <button type="submit" class="btn btn-primary w-100">Créer un compte</button>
            </form>
            
            <div id="error-message" class="text-danger text-center mt-3">
                <?php
                if (isset($_GET['error'])) {
                    switch ($_GET['error']) {
                        case 'missing_fields':
                            echo "Veuillez remplir tous les champs.";
                            break;
                        case 'invalid_data':
                            echo "Format des données incorrect.";
                            break;
                        case 'email_taken':
                            echo "Cet email est déjà utilisé.";
                            break;
                        default:
                            echo "Une erreur est survenue.";
                    }
                }
                ?>
            </div>
            
            <div class="text-center mt-3">
                <a href="index.php" class="btn btn-outline-secondary">Se connecter</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
