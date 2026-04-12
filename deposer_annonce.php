<?php
// ===== PAGE DE PUBLICATION D'ANNONCE =====
// Permet aux utilisateurs connectés de publier une nouvelle annonce

// Inclure la gestion centralisée des sessions
require_once 'session_config.php';

$message = "";

// Vérifier si l'utilisateur est connecté
if(!$is_logged_in) {
    header('Location: connexion.php');
    exit;
}

// Traiter le formulaire de publication d'annonce
if(empty($message) && !empty($_POST)) {
    // Récupérer les données du formulaire et les sécuriser
    $titre = mysqli_real_escape_string($conn, $_POST['titre'] ?? '');
    $description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
    $prix = floatval($_POST['prix'] ?? 0);
    $etat = mysqli_real_escape_string($conn, $_POST['etat'] ?? '');
    $imageName = basename($_FILES['image']['name'] ?? '');
    $imagePath = "";

    // Vérifier les champs obligatoires
    if(empty($titre) || empty($description) || $prix <= 0 || empty($etat) || empty($imageName)) {
        $message = "Tous les champs sont obligatoires, y compris la photo.";
    } else {
        // Vérifier l'upload de l'image
        if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $destination = "uploads/" . $imageName;
            if(move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $imagePath = $imageName; // stocker uniquement le nom de fichier
            } else {
                $message = "Impossible de déplacer l'image vers le dossier uploads.";
            }
        } else {
            $message = "Erreur lors du téléchargement de l'image.";
        }
    }

    // Insérer la nouvelle annonce dans la base de données
    if(empty($message)) {
        $sql = "INSERT INTO annonces (titre, description, prix, etat, image, user_id) VALUES ('$titre', '$description', '$prix', '$etat', '$imagePath', $user_id)";
        if(mysqli_query($conn, $sql)) {
            $message = "Annonce publiée avec succès !";
        } else {
            $message = "Erreur : " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Métadonnées HTML -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déposer une annonce - LoueTonMatos</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); min-height: 100vh;">

<!-- ===== INCLUSION DE LA NAVBAR ===== -->
<?php include 'header.php'; ?>

<!-- ===== SECTION PRINCIPALE DU FORMULAIRE ===== -->
<div class="d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 80px); padding: 40px 20px;">
    <!-- Carte du formulaire avec animation -->
    <div class="card p-5 shadow-lg" style="width: 100%; max-width: 620px; border: none; animation: slideUp 0.5s ease;">

        <!-- Titre et description -->
        <div class="text-center mb-4">
            <h2 class="fw-bold" style="color: #1a1a1a;">
                <i class="fa-solid fa-paper-plane" style="color: #F5C400;"></i> Déposez votre annonce
            </h2>
            <p class="text-muted">Partagez votre matériel avec la communauté</p>
        </div>

        <!-- Affichage des messages d'erreur/succès -->
        <?php if(!empty($message)): ?>
            <div class="alert <?php echo (strpos($message, 'Erreur') !== false) ? 'alert-danger' : 'alert-success'; ?> alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-<?php echo (strpos($message, 'Erreur') !== false) ? 'circle-exclamation' : 'circle-check'; ?> me-2"></i>
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Formulaire de publication -->
        <form method="POST" action="" enctype="multipart/form-data" class="form-container">

            <!-- Champ : Titre de l'annonce -->
            <div class="mb-3">
                <label class="form-label fw-bold">Titre de l'annonce</label>
                <input type="text" class="form-control form-input" name="titre" 
                       placeholder="Ex: Perceuse Bosch 18V" required>
                <small class="text-muted">Soyez clair et précis</small>
            </div>

            <!-- Champ : Description -->
            <div class="mb-3"> 
                <label class="form-label fw-bold">Description</label>
                <textarea class="form-control form-input" name="description" rows="4"
                          placeholder="Décrivez les caractéristiques, l'état, les accessoires..." required></textarea>
                <small class="text-muted">Plus la description est détaillée, plus de personnes loueront</small>
            </div>

            <!-- Champ : Prix par jour -->
            <div class="mb-3">
                <label class="form-label fw-bold">Prix par jour (€)</label>
                <div class="input-group">
                    <input type="number" class="form-control form-input" name="prix" 
                           placeholder="Ex: 15" step="0.01" min="0" required>
                    <span class="input-group-text">/jour</span>
                </div>
            </div>

            <!-- Champ : État du matériel -->
            <div class="mb-3">
                <label for="etat" class="form-label fw-bold">État du matériel</label>
                <select name="etat" id="etat" class="form-select form-input" required>
                    <option value="" selected disabled>-- Choisir un état --</option>
                    <option value="Neuf">✨ Neuf</option>
                    <option value="Très bon état">⭐ Très bon état</option>
                    <option value="Bon état">👍 Bon état</option>
                    <option value="État correct">🔧 État correct</option>
                    <option value="À réparer">🛠️ À réparer</option>
                </select>
            </div>

            <!-- Champ : Image du matériel -->
            <div class="mb-4">
                <label for="image" class="form-label fw-bold">Ajouter une photo</label>
                <input type="file" name="image" id="image" class="form-control form-input" accept="image/*" required>
                <small class="text-muted">Format: JPG, PNG, GIF. Taille max: 5MB</small>
            </div>

            <!-- Bouton de soumission avec animation -->
            <button type="submit" class="btn btn-warning w-100 btn-submit fw-bold py-2">
                <i class="fa-solid fa-paper-plane me-2"></i> Publier l'annonce
            </button>

        </form>

        <!-- Lien pour fermer -->
        <div class="text-center mt-3">
            <a href="accueil.php" class="text-decoration-none text-muted">
                <small>← Retour à l'accueil</small>
            </a>
        </div>

    </div>
</div>

<!-- ===== STYLES CSS PERSONNALISÉS ===== -->
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

    /* Style de la carte */
    .card {
        border-radius: 15px;
        background: white;
    }

    /* Animation des champs de formulaire */
    .form-input {
        border-radius: 8px;
        border: 2px solid #e0e0e0;
        transition: all 0.3s ease;
        font-size: 15px;
    }

    .form-input:focus {
        border-color: #F5C400 !important;
        box-shadow: 0 0 10px rgba(245, 196, 0, 0.2);
        background-color: #fffbf0;
    }

    /* Animation du bouton submit */
    .btn-submit {
        border-radius: 8px;
        font-size: 16px;
        transition: all 0.3s ease;
        border: none;
    }

    .btn-submit:hover {
        background-color: #d4a000;
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(245, 196, 0, 0.3);
    }

    .btn-submit:active {
        transform: translateY(0);
    }

    /* Style du conteneur de formulaire */
    .form-container {
        animation: fadeIn 0.5s ease 0.2s both;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    /* Responsive */
    @media (max-width: 576px) {
        .card {
            padding: 20px !important;
        }

        h2 {
            font-size: 1.5rem !important;
        }
    }
</style>

<!-- ===== SCRIPTS JAVASCRIPT ===== -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Afficher une notification au chargement si succès
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        if(alerts.length > 0) {
            // L'alerte est déjà visible via la classe fade show
            console.log('Message affiché');
        }

        // Ajouter une animation aux champs
        const inputs = document.querySelectorAll('.form-input');
        inputs.forEach((input, index) => {
            input.style.animation = `slideUp ${0.3 + (index * 0.1)}s ease both`;
        });
    });

    // Valider que l'utilisateur a sélectionné une image
    document.getElementById('image').addEventListener('change', function(e) {
        if(e.target.files.length > 0) {
            const file = e.target.files[0];
            // Vérifier la taille (max 5MB)
            if(file.size > 5 * 1024 * 1024) {
                alert('L\'image est trop volumineuse (max 5MB)');
                e.target.value = '';
                return;
            }
            console.log('Image sélectionnée: ' + file.name);
        }
    });
</script>

</body>
</html>