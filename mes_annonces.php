<?php
require_once 'session_config.php';

//  Vérifier si l'utilisateur est connecté
if (!$is_logged_in) {
    header('Location: connexion.php');
    exit;
}

//recupérer les annonces de l'utilisateur connecté
$sql = "SELECT * FROM annonces WHERE user_id='$user_id' ORDER BY id DESC";
$result = mysqli_query($conn, $sql);   

//recupérer les annonces favoris de l'utilisateur connecté
$sql_favoris = "SELECT annonces.* FROM annonces 
               INNER JOIN favoris ON annonces.id = favoris.annonce_id 
               WHERE favoris.user_id = '$user_id' ORDER BY favoris.id DESC";
$result_favoris = mysqli_query($conn, $sql_favoris);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes annonces - LoueTonMatos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="styles-global.css">
</head>
<body style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); min-height: 100vh;">
    <?php include 'header.php'; ?>

<div class="container mt-5">

    <!-- Section Mes annonces (EN HAUT) -->
    <div class="mb-5">
        <h3 class="fw-bold" style="color: white; border-bottom: 3px solid #F5C400; padding-bottom: 10px;">
            <i class="fa-solid fa-bullhorn me-2" style="color: #F5C400;"></i>Mes annonces
        </h3>
        
        <div class="row g-4 justify-content-start mt-4">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($annonce = mysqli_fetch_assoc($result)):
                    $imageUrl = $annonce['image'] ?? '';
                    if (!empty($imageUrl) && strpos($imageUrl, 'uploads/') !== 0) {
                        $imageUrl = 'uploads/' . $imageUrl;
                    }
                    $imageUrl = $imageUrl ?: 'https://via.placeholder.com/360x180?text=Pas+d\'image';
            ?>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="card border-0 shadow-sm h-100">
                            <img src="<?= htmlspecialchars($imageUrl) ?>" class="card-img-top" style="height: 180px; object-fit: cover;">
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
            <?php
                endwhile;
            } else {
                echo '<p style="color: #ccc; text-align: center; width: 100%;">Vous n\'avez pas d\'annonces publiées.</p>';
            }
            ?>
        </div>
    </div>

    <!-- Section Favoris (EN BAS) -->
    <div class="mb-5" id="mes-favoris">
        <h3 class="fw-bold" style="color: white; border-bottom: 3px solid #F5C400; padding-bottom: 10px;">
            <i class="fa-solid fa-heart me-2" style="color: #F5C400;"></i>Mes favoris
        </h3>
        
        <div class="row g-4 justify-content-start mt-4">
            <?php
            if (mysqli_num_rows($result_favoris) > 0) {
                while ($favoris = mysqli_fetch_assoc($result_favoris)):
                    $imageUrl = $favoris['image'] ?? '';
                    if (!empty($imageUrl) && strpos($imageUrl, 'uploads/') !== 0) {
                        $imageUrl = 'uploads/' . $imageUrl;
                    }
                    $imageUrl = $imageUrl ?: 'https://via.placeholder.com/360x180?text=Pas+d\'image';
            ?>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="card border-0 shadow-sm h-100">
                            <img src="<?= htmlspecialchars($imageUrl) ?>" class="card-img-top" style="height: 180px; object-fit: cover;">
                            <div class="card-body">
                                <span class="badge mb-2" style="background-color: #1a1a1a; color: #F5C400;">
                                    <?= htmlspecialchars($favoris['etat']) ?>
                                </span>
                                <h5 class="card-title fw-bold">
                                    <?= htmlspecialchars($favoris['titre']) ?>
                                </h5>
                                <p class="card-text text-muted" style="font-size: 13px;">
                                    <?= htmlspecialchars(substr($favoris['description'], 0, 80)) ?>...
                                </p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="fw-bold" style="color: #F5C400;">
                                        <?= htmlspecialchars($favoris['prix']) ?> €/jour
                                    </span>
                                    <a href="consulter_annonce.php?id=<?= htmlspecialchars($favoris['id']) ?>" class="btn btn-warning btn-sm">
                                        Voir
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php
                endwhile;
            } else {
                echo '<p style="color: #ccc; text-align: center; width: 100%;">Vous n\'avez pas d\'annonces en favoris.</p>';
            }
            ?>
        </div>
    </div>

</div>

</body>
</html>
