<?php
// ===== PAGE D'INSCRIPTION =====
// Permet aux nouveaux utilisateurs de créer un compte

// Inclure la gestion centralisée des sessions
require_once 'session_config.php';

// Si déjà connecté, rediriger vers l'accueil
if($is_logged_in) {
    header('Location: accueil.php');
    exit;
}

$erreur = "";
$succes = "";

// Traiter le formulaire d'inscription
if(!empty($_POST)) {
    // Récupérer et sécuriser les données du formulaire
    $nom = mysqli_real_escape_string($conn, $_POST['nom'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $mdp = $_POST['mdp'] ?? '';
    $mdp_confirm = $_POST['mdp_confirm'] ?? '';
    
    // Vérifier que les champs ne sont pas vides
    if(empty($nom) || empty($email) || empty($mdp) || empty($mdp_confirm)) {
        $erreur = "Tous les champs sont obligatoires.";
    }
    // Vérifier que les mots de passe correspondent
    else if($mdp != $mdp_confirm) {
        $erreur = "Les mots de passe ne correspondent pas.";
    }
    // Vérifier la longueur du mot de passe
    else if(strlen($mdp) < 6) {
        $erreur = "Le mot de passe doit contenir au moins 6 caractères.";
    }
    // Vérifier le format de l'email
    else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Veuillez entrer une adresse email valide.";
    }
    // Vérifier si l'email n'existe pas déjà
    else {
        $sql_check = "SELECT id FROM users WHERE email='$email'";
        $result_check = mysqli_query($conn, $sql_check);
        if(mysqli_num_rows($result_check) > 0) {
            $erreur = "Cet email est déjà utilisé.";
        } else {
            // Hasher le mot de passe
            $mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);
            // Insérer le nouvel utilisateur dans la base de données
            $sql = "INSERT INTO users (nom, email, mdp) VALUES ('$nom', '$email', '$mdp_hash')";
            if(mysqli_query($conn, $sql)) {
                $succes = "Inscription réussie! Vous pouvez maintenant vous connecter.";
                // Rediriger vers la page de connexion après 2 secondes
                header("refresh:2;url=connexion.php");
            } else {
                $erreur = "Erreur lors de l'inscription : " . mysqli_error($conn);
            }
        }
    }
}

// Récupérer l'erreur de session si elle existe
if(isset($_SESSION['erreur'])) {
    $erreur = $_SESSION['erreur'];
    unset($_SESSION['erreur']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Métadonnées HTML -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - LoueTonMatos</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); min-height: 100vh;">

<!-- ===== INCLUSION DE LA NAVBAR ===== -->
<?php include 'header.php'; ?>

<!-- ===== SECTION PRINCIPALE D'INSCRIPTION ===== -->
<div class="d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 80px); padding: 40px 20px;">
    <!-- Carte d'inscription -->
    <div class="card p-5 shadow-lg" style="width: 100%; max-width: 500px; border: none; border-radius: 15px; animation: slideUp 0.5s ease;">

        <!-- Titre et icône -->
        <div class="text-center mb-5">
            <i class="fa-solid fa-user-plus fa-3x" style="color: #F5C400;"></i>
            <h2 class="fw-bold mt-3">Créer un compte</h2>
            <p class="text-muted">Rejoignez la communauté LoueTonMatos</p>
        </div>

        <!-- Affichage des erreurs -->
        <?php if(!empty($erreur)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <strong>Attention !</strong> <?php echo htmlspecialchars($erreur); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Affichage du message de succès -->
        <?php if(!empty($succes)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>
                <strong>Succès !</strong> <?php echo htmlspecialchars($succes); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Formulaire d'inscription -->
        <form method="POST" action="inscription.php" class="form-inscription">

            <!-- Champ : Nom complet -->
            <div class="mb-3">
                <label class="form-label fw-bold">Nom complet</label>
                <div class="input-group">
                    <span class="input-group-text" style="background: white; border: 2px solid #e0e0e0;">
                        <i class="fa-solid fa-user" style="color: #F5C400;"></i>
                    </span>
                    <input type="text" class="form-control form-input" name="nom" 
                           placeholder="Jean Dupont" required>
                </div>
            </div>

            <!-- Champ : Email -->
            <div class="mb-3">
                <label class="form-label fw-bold">Adresse email</label>
                <div class="input-group">
                    <span class="input-group-text" style="background: white; border: 2px solid #e0e0e0;">
                        <i class="fa-solid fa-envelope" style="color: #F5C400;"></i>
                    </span>
                    <input type="email" class="form-control form-input" name="email" 
                           placeholder="votre@email.com" required>
                </div>
                <small class="text-muted">Nous ne partagerons jamais votre email</small>
            </div>

            <!-- Champ : Mot de passe -->
            <div class="mb-3">
                <label class="form-label fw-bold">Mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text" style="background: white; border: 2px solid #e0e0e0;">
                        <i class="fa-solid fa-lock" style="color: #F5C400;"></i>
                    </span>
                    <input type="password" class="form-control form-input" name="mdp" 
                           placeholder="••••••••" required minlength="6">
                </div>
                <small class="text-muted">Au moins 6 caractères</small>
            </div>

            <!-- Champ : Confirmation du mot de passe -->
            <div class="mb-4">
                <label class="form-label fw-bold">Confirmer le mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text" style="background: white; border: 2px solid #e0e0e0;">
                        <i class="fa-solid fa-lock" style="color: #F5C400;"></i>
                    </span>
                    <input type="password" class="form-control form-input" name="mdp_confirm" 
                           placeholder="••••••••" required>
                </div>
            </div>

            <!-- Checkbox conditions -->
            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="conditions" required>
                    <label class="form-check-label text-muted" for="conditions">
                        J'accepte les conditions d'utilisation
                    </label>
                </div>
            </div>

            <!-- Bouton d'inscription -->
            <button type="submit" class="btn btn-warning w-100 btn-submit fw-bold py-2">
                <i class="fa-solid fa-user-plus me-2"></i> S'inscrire
            </button>

        </form>

        <!-- Séparation -->
        <hr class="my-4">

        <!-- Lien vers connexion -->
        <p class="text-center text-muted mb-0">
            Déjà inscrit ?
            <a href="connexion.php" class="text-decoration-none fw-bold" style="color: #F5C400;">
                Se connecter
            </a>
        </p>

    </div>
</div>

<!-- ===== STYLES CSS PERSONNALISÉS ===== -->
<style>
    /* Animation de slide vers le haut */
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(40px);
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
    .form-inscription {
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

    /* Style de la carte */
    .card {
        background: white;
    }

    /* Responsive */
    @media (max-width: 576px) {
        .card {
            padding: 30px !important;
            margin: 20px;
        }

        h2 {
            font-size: 1.5rem !important;
        }
    }
</style>

<!-- ===== SCRIPTS JAVASCRIPT ===== -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Animer les champs au chargement
    document.addEventListener('DOMContentLoaded', function() {
        const formInputs = document.querySelectorAll('.form-input');
        formInputs.forEach((input, index) => {
            input.style.animation = `slideUp ${0.3 + (index * 0.1)}s ease both`;
        });
    });

    // Validation du formulaire client
    document.querySelector('.form-inscription').addEventListener('submit', function(e) {
        const mdp = document.querySelector('input[name="mdp"]').value;
        const mdp_confirm = document.querySelector('input[name="mdp_confirm"]').value;
        const conditions = document.querySelector('#conditions').checked;

        if(mdp !== mdp_confirm) {
            e.preventDefault();
            alert('Les mots de passe ne correspondent pas!');
        }

        if(!conditions) {
            e.preventDefault();
            alert('Vous devez accepter les conditions d\'utilisation');
        }
    });
</script>

</body>
</html>
