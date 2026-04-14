<?php
require_once 'session_config.php';

// 🔥 récupérer la recherche
$q = $_GET['q'] ?? '';

// 🔥 adapter la requête SQL
if(!empty($q)){
    $q = mysqli_real_escape_string($conn, $q);

    $sql = "SELECT * FROM annonces 
            WHERE titre LIKE '%$q%' 
            ORDER BY id DESC";
} else {
    $sql = "SELECT * FROM annonces ORDER BY id DESC LIMIT 8";
}

$result = mysqli_query($conn, $sql);
$nb_resultats = $result ? mysqli_num_rows($result) : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LoueTonMatos - Accueil</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="styles-global.css">
</head>

<body style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); min-height: 100vh;">

<?php include 'header.php'; ?>

<div class="container text-center" style="margin-top: 60px;">

    <h1 class="display-4 fw-bold text-white">LoueTonMatos</h1>
    <p class="lead mt-3 text-white">Louez et vendez votre matériel en toute simplicité</p>

    <div class="mt-4">
        <a href="deposer_annonce.php" class="btn btn-warning btn-sm px-4">
            Deposer une annonce <i class="fa-solid fa-plus ms-1"></i>
        </a>
    </div>

</div>

<!-- 🔥 titre dynamique -->
<div class="container mt-5">
    <div class="text-center mb-4">
        <h3 class="fw-bold text-white">
            <?= !empty($q) ? "Résultats pour : $q" : "Dernières annonces" ?>
        </h3>
    </div>
</div>

<!-- grille des annonces -->
<div class="container mt-3">
    <div class="row g-4 justify-content-center">

<?php if($nb_resultats === 0 && !empty($q)): ?>
    <div class="col-12">
        <div class="alert alert-warning text-center shadow-sm" role="alert">
            Aucune annonce pour "<?= htmlspecialchars($q) ?>"
        </div>
    </div>
<?php endif; ?>

<?php while($annonce = mysqli_fetch_assoc($result)): ?>

    <?php
        $imageUrl = $annonce['image'] ?? '';

        if(!empty($imageUrl) && strpos($imageUrl, 'uploads/') !== 0) {
            $imageUrl = 'uploads/' . $imageUrl;
        }

        if(empty($imageUrl)) {
            $imageUrl = 'https://via.placeholder.com/360x180?text=Pas+d\'image';
        }
    ?>

    <div class="col-sm-6 col-md-4 col-lg-3">
        <div class="card border-0 shadow-sm">

            <img src="<?= htmlspecialchars($imageUrl) ?>" 
                 class="card-img-top"
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
