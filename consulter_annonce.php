<?php
require_once 'session_config.php';

// 🔒 Vérifier si l'utilisateur est connecté
if (!$is_logged_in) {
    header('Location: connexion.php');
    exit;
}

// 🔹 Récupérer l'id de l'annonce depuis l'URL
$annonce_id = (int) ($_GET['id'] ?? 0);

// 🔥 Récupérer les infos de l'annonce
$sql = "SELECT annonces.*, users.nom, users.email 
        FROM annonces 
        JOIN users ON annonces.user_id = users.id 
        WHERE annonces.id = '$annonce_id'";

$result = mysqli_query($conn, $sql);
$annonce = mysqli_fetch_assoc($result);

// Vérifier si l'annonce existe
if (!$annonce) {
    header('Location: accueil.php');
    exit;
}

$is_owner = (int)$annonce['user_id'] === (int)$user_id;

// 🔥 Vérifier si l'annonce est en favoris pour cet utilisateur
$is_favoris = false;
if ($is_logged_in && !$is_owner) {
    $sql_fav = "SELECT id FROM favoris WHERE user_id = '$user_id' AND annonce_id = '$annonce_id'";
    $result_fav = mysqli_query($conn, $sql_fav);
    $is_favoris = mysqli_num_rows($result_fav) > 0;
}

// 🔥 TRAITER L'AJOUT/RETRAIT DE FAVORIS
if (isset($_POST['toggle_favoris']) && !$is_owner) {
    if ($is_favoris) {
        // Retirer des favoris
        $sql = "DELETE FROM favoris WHERE user_id = '$user_id' AND annonce_id = '$annonce_id'";
        mysqli_query($conn, $sql);
    } else {
        // Ajouter aux favoris
        $sql = "INSERT INTO favoris (user_id, annonce_id) VALUES ('$user_id', '$annonce_id')";
        mysqli_query($conn, $sql);
    }
    
    // Redirection pour éviter le double envoi
    header("Location: consulter_annonce.php?id=$annonce_id");
    exit;
}

// 🔥 TRAITER L'OUVERTURE DU CHAT
if (isset($_POST['open_chat']) && !$is_owner) {
    header("Location: chat.php?annonce_id=$annonce_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($annonce['titre']) ?> - LoueTonMatos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="styles-global.css">
</head>
<body style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); min-height: 100vh;">
    <?php include 'header.php'; ?>

<div class="container mt-5 mb-5">
    <div class="row g-4">
        
        <!-- Image de l'annonce -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-lg rounded-3">
                <div class="card-body p-0">
                    <?php
                    $imageUrl = $annonce['image'] ?? '';
                    if (!empty($imageUrl) && strpos($imageUrl, 'uploads/') !== 0) {
                        $imageUrl = 'uploads/' . $imageUrl;
                    }
                    $imageUrl = $imageUrl ?: 'https://via.placeholder.com/600x400?text=Pas+d\'image';
                    ?>
                    <img src="<?= htmlspecialchars($imageUrl) ?>" class="img-fluid rounded-3" style="width: 100%; height: 400px; object-fit: cover;">
                </div>
            </div>
        </div>
        
        <!-- Détails de l'annonce -->
        <div class="col-lg-6">
            <div class="card bg-dark border-0 shadow-lg rounded-3" style="border-top: 3px solid #F5C400;">
                <div class="card-body p-4">
                    
                    <!-- Titre et état -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h2 class="text-white fw-bold mb-2"><?= htmlspecialchars($annonce['titre']) ?></h2>
                            <span class="badge" style="background-color: #1a1a1a; color: #F5C400; font-size: 12px;">
                                <?= htmlspecialchars($annonce['etat']) ?>
                            </span>
                        </div>
                        
                        <?php if(!$is_owner): ?>
                            <form method="POST" class="d-inline">
                                <button type="submit" name="toggle_favoris" class="btn btn-link p-0" style="font-size: 24px; color: #F5C400;">
                                    <i class="fa<?= $is_favoris ? 's' : 'r' ?> fa-heart"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Prix -->
                    <div class="mb-4">
                        <span class="h3 fw-bold" style="color: #F5C400;">
                            <?= htmlspecialchars($annonce['prix']) ?> €/jour
                        </span>
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-4">
                        <h5 class="text-white mb-3">Description</h5>
                        <p class="text-white-50" style="line-height: 1.6;">
                            <?= nl2br(htmlspecialchars($annonce['description'])) ?>
                        </p>
                    </div>
                    
                    <!-- Propriétaire -->
                    <div class="mb-4">
                        <h5 class="text-white mb-2">Propriétaire</h5>
                        <p class="text-white-50 mb-1">
                            <i class="fa-solid fa-user me-2" style="color: #F5C400;"></i>
                            <?= htmlspecialchars($annonce['nom']) ?>
                        </p>
                        <p class="text-white-50">
                            <i class="fa-solid fa-envelope me-2" style="color: #F5C400;"></i>
                            <?= htmlspecialchars($annonce['email']) ?>
                        </p>
                    </div>
                    
                    <!-- Boutons d'action -->
                    <div class="d-flex gap-3 flex-column flex-sm-row">
                        <?php if($is_owner): ?>
                            <a href="modifier_annonce.php?id=<?= htmlspecialchars($annonce['id']) ?>" class="btn btn-warning flex-grow-1">
                                <i class="fa-solid fa-pen-to-square me-2"></i>Modifier l'annonce
                            </a>
                        <?php else: ?>
                            <form method="POST" class="flex-grow-1">
                            <button type="submit" name="open_chat" class="btn btn-warning w-100">
                                <i class="fa-solid fa-comments me-2"></i>Contacter le propriétaire
                            </button>
                            </form>
                        <?php endif; ?>
                    </div>
                    
                </div>
            </div>
        </div>
        
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
