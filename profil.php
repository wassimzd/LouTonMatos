<?php
// ===== PAGE PROFIL =====
// Permet de consulter et modifier les informations du compte utilisateur

// Inclure la gestion centralisée des sessions
require_once 'session_config.php';

// Vérifier que l'utilisateur est connecté
if(!$is_logged_in) {
    header('Location: connexion.php');
    exit;
}

$erreur = "";
$succes = "";

// Traiter la soumission du formulaire de mise à jour
if(!empty($_POST)) {
    $nom = mysqli_real_escape_string($conn, $_POST['nom'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $mdp = $_POST['mdp'] ?? '';
    $ancien_mdp = $_POST['ancien_mdp'] ?? '';

    // Vérifier les champs obligatoires
    if(empty($nom) || empty($email)) {
        $erreur = "Le nom / pseudo et l'email sont obligatoires.";
    } else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Veuillez entrer une adresse email valide.";
    }

    // Si on veut changer le mot de passe, vérifier l'ancien
    if(!empty($mdp)) {
        if(strlen($mdp) < 6) {
            $erreur = "Le mot de passe doit contenir au moins 6 caractères.";
        } else if(empty($ancien_mdp)) {
            $erreur = "Vous devez saisir votre ancien mot de passe pour en changer.";
        } else {
            // Vérifier l'ancien mot de passe
            $sqlCheck = "SELECT mdp FROM users WHERE id=$user_id";
            $resultCheck = mysqli_query($conn, $sqlCheck);
            $userCheck = mysqli_fetch_assoc($resultCheck);
            if(!$userCheck || !password_verify($ancien_mdp, $userCheck['mdp'])) {
                $erreur = "L'ancien mot de passe est incorrect.";
            }
        }
    }

    // Mettre à jour les informations uniquement si tout est valide
    if(empty($erreur)) {
        if(!empty($mdp)) {
            $mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET nom='$nom', email='$email', mdp='$mdp_hash' WHERE id=$user_id";
        } else {
            $sql = "UPDATE users SET nom='$nom', email='$email' WHERE id=$user_id";
        }

        if(mysqli_query($conn, $sql)) {
            $succes = "Vos informations ont bien été mises à jour.";
        } else {
            $erreur = "Erreur lors de la mise à jour : " . mysqli_error($conn);
        }
    }
}

// Récupérer les informations actuelles de l'utilisateur
$sqlUser = "SELECT nom, email FROM users WHERE id=$user_id";
$resultUser = mysqli_query($conn, $sqlUser);
$user = mysqli_fetch_assoc($resultUser);
if(!$user) {
    header('Location: connexion.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil - LoueTonMatos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); min-height: 100vh;">

<?php include 'header.php'; ?>

<div class="d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 80px); padding: 40px 20px;">
    <div class="card p-5 shadow-lg" style="width: 100%; max-width: 600px; border: none; border-radius: 15px; background: #111111;">

        <div class="text-center mb-4">
            <i class="fa-solid fa-user-circle fa-3x" style="color: #F5C400;"></i>
            <h2 class="fw-bold mt-3" style="color: white;">Mon profil</h2>
            <p class="text-white">Consultez et mettez à jour vos informations personnelles.</p>
        </div>

        <?php if(!empty($erreur)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <?php echo htmlspecialchars($erreur); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(!empty($succes)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>
                <?php echo htmlspecialchars($succes); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="profil.php" class="profile-form">

            <div class="mb-3">
                <label class="form-label fw-bold text-white">Nom / Pseudo</label>
                <input type="text" name="nom" class="form-control form-input" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-white">Adresse email</label>
                <input type="email" name="email" class="form-control form-input" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold text-white">Ancien mot de passe (requis pour changer)</label>
                <input type="password" name="ancien_mdp" class="form-control form-input" placeholder="Votre ancien mot de passe">
                <small class="text-white">Obligatoire uniquement si vous changez de mot de passe.</small>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-white">Mot de passe (laisser vide pour conserver l'actuel)</label>
                <input type="password" name="mdp" class="form-control form-input" placeholder="Nouveau mot de passe">
                <small class="text-white">Au moins 6 caractères si vous changez le mot de passe.</small>
            </div>

        

            <button type="submit" class="btn btn-warning w-100 btn-submit fw-bold py-2">
                <i class="fa-solid fa-save me-2"></i> Enregistrer les modifications
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="accueil.php" class="text-decoration-none text-white"><small>← Retour à l'accueil</small></a>
        </div>
    </div>
</div>

<style>
    /* Animation de slide vers le haut */
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Style des champs de formulaire */
    .form-input {
        border: 2px solid #e0e0e0 !important;
        border-radius: 8px;
        padding: 12px 15px;
        transition: all 0.3s ease;
        font-size: 15px;
        background: white;
    }

    .form-input:focus {
        border-color: #F5C400 !important;
        box-shadow: 0 0 12px rgba(245, 196, 0, 0.2);
        background-color: #fffbf0;
    }

    /* Animation du bouton submit */
    .btn-submit {
        background: linear-gradient(135deg, #F5C400, #d4a000);
        border: none;
        border-radius: 8px;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(245, 196, 0, 0.3);
        background: linear-gradient(135deg, #d4a000, #F5C400);
        color: white;
    }

    .btn-submit:active {
        transform: translateY(-1px);
    }

    /* Animation du formulaire */
    .profile-form {
        animation: fadeIn 0.5s ease 0.3s both;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
