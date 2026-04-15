<?php
// ===== PAGE DE CONNEXION =====
// Permet aux utilisateurs de se connecter à leur compte

// Inclure la gestion centralisée des sessions
require_once 'session_config.php';

// Si déjà connecté, rediriger vers l'accueil
if($is_logged_in) {
    header('Location: accueil.php');
    exit;
}

$erreur = "";

// Traiter le formulaire de connexion
if(!empty($_POST)) {
    // Récupérer les données du formulaire
    $email = $_POST['email'] ?? '';
    $mdp = $_POST['mdp'] ?? '';

    // Requête simple pour trouver l'utilisateur
    $sql = "SELECT * FROM users WHERE email='" . mysqli_real_escape_string($conn, $email) . "'";
    $result = mysqli_query($conn, $sql);

    // Vérifier si l'utilisateur existe
    if(mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        // Vérifier le mot de passe
        if(password_verify($mdp, $user['mdp'])) {
            if (isset($user['actif']) && (int)$user['actif'] !== 1) {
                $erreur = "Ce compte est desactive.";
            } else {
            // Authentification réussie - créer la session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'] ?? 'membre';
                header("Location: accueil.php");
                exit;
            }
        } else {
            // Mot de passe incorrect
            $erreur = "Mot de passe incorrect.";
        }
    } else {
        // Aucun compte trouvé
        $erreur = "Aucun compte trouvé avec cet email.";
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Métadonnées HTML -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - LoueTonMatos</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); min-height: 100vh;">

<!-- ===== INCLUSION DE LA NAVBAR ===== -->
<?php include 'header.php'; ?>

<!-- ===== SECTION PRINCIPALE DE CONNEXION ===== -->
<div class="d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 80px);">
    <!-- Carte de connexion -->
    <div class="card p-5 shadow-lg" style="width: 100%; max-width: 450px; border: none; border-radius: 15px; animation: slideUp 0.5s ease;">

        <!-- Titre et icône -->
        <div class="text-center mb-5">
            <i class="fa-solid fa-sign-in-alt fa-3x" style="color: #F5C400;"></i>
            <h2 class="fw-bold mt-3">Connexion</h2>
            <p class="text-muted">Retrouvez votre compte LoueTonMatos</p>
        </div>

        <!-- Affichage des erreurs -->
        <?php if(!empty($erreur)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <strong>Erreur !</strong> <?php echo htmlspecialchars($erreur); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Formulaire de connexion -->
        <form method="POST" action="connexion.php" class="form-connexion">

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
            </div>

            <!-- Champ : Mot de passe -->
            <div class="mb-4">
                <label class="form-label fw-bold">Mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text" style="background: white; border: 2px solid #e0e0e0;">
                        <i class="fa-solid fa-lock" style="color: #667eea;"></i>
                    </span>
                    <input type="password" class="form-control form-input" name="mdp" 
                           placeholder="••••••••" required>
                </div>
            </div>

            <!-- Bouton de connexion -->
            <button type="submit" class="btn btn-warning w-100 btn-submit fw-bold py-2">
                <i class="fa-solid fa-sign-in-alt me-2"></i> Se connecter
            </button>

        </form>

        <!-- Séparation -->
        <hr class="my-4">

        <!-- Lien vers inscription -->
        <p class="text-center text-muted mb-0">
            Pas encore inscrit ?
            <a href="inscription.php" class="text-decoration-none fw-bold" style="color: #F5C400;">
                Créer un compte
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
    .form-connexion {
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

        // Ajouter un focus sur le premier champ
        if(formInputs.length > 0) {
            formInputs[0].focus();
        }
    });

    // Validation du formulaire client
    document.querySelector('.form-connexion').addEventListener('submit', function(e) {
        const email = document.querySelector('input[name="email"]').value;
        const mdp = document.querySelector('input[name="mdp"]').value;

        if(!email || !mdp) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs');
        }
    });
</script>

</body>
</html>
