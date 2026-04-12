<?php
// ===== PAGE D'ACCUEIL PRINCIPALE =====
// Cette page affiche la page d'accueil avec les 8 dernières annonces

// Inclure la gestion centralisée des sessions
require_once 'session_config.php';

// Récupérer les 8 dernières annonces
$sql = "SELECT * FROM annonces ORDER BY id DESC LIMIT 8";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Métadonnées HTML -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LoueTonMatos - Accueil</title>
    
    <!-- Bootstrap CSS pour le design responsive -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="styles-global.css">
</head>
<body style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); min-height: 100vh;">

<!-- ===== INCLUSION DE LA NAVBAR ===== -->
<?php include 'header.php'; ?>

<div class="container text-center" style="margin-top: 60px;">

    <h1 class="display-4 fw-bold" style="color: white;">Bienvenue sur LoueTonMatos</h1>
    <p class="lead mt-3" style="color: white;">Louez et vendez votre matériel en toute simplicité</p>
    <div class="mt-4">
        <a href="deposer_annonce.php" class="btn btn-warning btn-sm px-4">
            Deposer une annonce <i class="fa-solid fa-plus ms-1"></i>
        </a>
    </div>

</div>

<!-- Section dernières annonces -->
<div class="container mt-5">
    <div class="text-center mb-4">
        <h3 class="fw-bold" style="color: white;">Dernières annonces</h3>
    </div>
</div>

<!-- grille des annonces -->
<div class="container mt-3">
    <div class="row g-4 justify-content-center">





<!-- mysqli_fetch_assoc récupère une ligne de résultat SQL et la transforme en tableau ($annonce) -->
<!-- La boucle while permet de parcourir toutes les annonces une par une -->

<?php while($annonce = mysqli_fetch_assoc($result)): ?>
    <?php
        $imageUrl = $annonce['image'] ?? '';
        if(!empty($imageUrl) && strpos($imageUrl, 'uploads/') !== 0) {
            $imageUrl = 'uploads/' . $imageUrl; // ajouter le dossier uploads si nécessaire
        }
        if(empty($imageUrl)) {
            $imageUrl = 'https://via.placeholder.com/360x180?text=Pas+d\'image';
        }
    ?>

    <div class="col-sm-6 col-md-4 col-lg-3">
        <div class="card border-0 shadow-sm">
            <!-- htmlspecialchars sert à sécuriser les données affichées en HTML -->
            <!-- Il empêche l'exécution de code malveillant (ex: scripts) -->
            <!-- et transforme les caractères spéciaux en texte sans danger -->

            <img src="<?= htmlspecialchars($imageUrl) ?>" class="card-img-top" 
                 style="height: 180px; object-fit: cover;">

            <div class="card-body">
                    <span class="badge mb-2" style="background-color: #1a1a1a; color: #F5C400;">
                        <?= htmlspecialchars($annonce['etat']) ?>
                    </span>

                    <h5 class="card-title fw-bold">
                        <?= htmlspecialchars($annonce['titre']) ?>
                    </h5>

                    <p class="card-text text-muted" style="font-size: 13px;">
                        <?= htmlspecialchars(substr($annonce['description'], 0, 80)) ?>...
                    </p>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <span class="fw-bold" style="color: #F5C400;">
                        <?= htmlspecialchars($annonce['prix']) ?> €/jour
                    </span>

                    <a href="consulter_annonce.php?id=<?= htmlspecialchars($annonce['id']) ?>" class="btn btn-warning btn-sm">
                        Voir
                    </a>
                </div>
            </div>

        </div>
    </div>

<?php endwhile; ?>

</div>
        
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
