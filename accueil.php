<?php
require_once 'session_config.php';

// Récupérer la recherche
$q = $_GET['q'] ?? '';

// Adapter la requête SQL
if(!empty($q)){
    $q = mysqli_real_escape_string($conn, $q);
    $sql = "SELECT * FROM annonces WHERE titre LIKE '%$q%' ORDER BY id DESC";
} else {
    $sql = "SELECT * FROM annonces ORDER BY id DESC LIMIT 8";
}

$result = mysqli_query($conn, $sql);
$nb_resultats = $result ? mysqli_num_rows($result) : 0;

// Récupérer les stats pour le hero
$total_annonces = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM annonces"))['total'] ?? 0;
$total_membres = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'] ?? 0;
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

<body class="ltm-body">

<?php include 'header.php'; ?>

<!-- ===== HERO SECTION ===== -->
<?php if(empty($q)): ?>
<section class="ltm-hero">
    <div class="ltm-hero-pattern"></div>
    <div class="container text-center position-relative">

        <!-- Badge accrocheur -->
        <div class="ltm-hero-badge">
            <i class="fa-solid fa-star me-1" style="font-size: 10px;"></i>
            La plateforme N°1 de location de matériel
        </div>

        <!-- Titre principal -->
        <h1 class="ltm-hero-title">
            Louez et vendez votre<br>
            <span class="ltm-hero-highlight">matériel en toute simplicité</span>
        </h1>

        <!-- Sous-titre -->
        <p class="ltm-hero-subtitle">
            BTP, transport, agricole, événementiel — trouvez ce dont vous avez besoin près de chez vous.
        </p>

        <!-- Stats -->
        <div class="ltm-hero-stats">
            <div class="ltm-stat">
                <span class="ltm-stat-num"><?= $total_annonces ?>+</span>
                <span class="ltm-stat-label">Annonces actives</span>
            </div>
            <div class="ltm-stat-divider"></div>
            <div class="ltm-stat">
                <span class="ltm-stat-num"><?= $total_membres ?>+</span>
                <span class="ltm-stat-label">Membres vérifiés</span>
            </div>
            <div class="ltm-stat-divider"></div>
            <div class="ltm-stat">
                <span class="ltm-stat-num">100%</span>
                <span class="ltm-stat-label">Gratuit</span>
            </div>
        </div>

        <!-- CTA buttons -->
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="deposer_annonce.php" class="btn ltm-btn-cta-primary">
                <i class="fa-solid fa-plus me-2"></i> Déposer une annonce
            </a>
            <a href="#annonces" class="btn ltm-btn-cta-outline">
                Parcourir les annonces <i class="fa-solid fa-arrow-down ms-2"></i>
            </a>
        </div>

    </div>
</section>

<!-- Séparateur -->
<div class="ltm-divider"></div>
<?php endif; ?>

<!-- ===== SECTION ANNONCES ===== -->
<section class="ltm-section" id="annonces">
    <div class="container">

        <!-- Titre de section -->
        <div class="ltm-section-header">
            <h2 class="ltm-section-title">
                <?php if(!empty($q)): ?>
                    <i class="fa-solid fa-magnifying-glass me-2" style="color: #F5C400;"></i>
                    Résultats pour : <span style="color: #F5C400;"><?= htmlspecialchars($q) ?></span>
                <?php else: ?>
                    Dernières <span style="color: #F5C400;">annonces</span>
                <?php endif; ?>
            </h2>
            <?php if(empty($q)): ?>
            <a href="accueil.php?q=" class="ltm-see-all">
                Voir tout <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
            <?php endif; ?>
        </div>

        <!-- Message aucun résultat -->
        <?php if($nb_resultats === 0 && !empty($q)): ?>
        <div class="ltm-empty-state">
            <i class="fa-solid fa-magnifying-glass fa-2x mb-3" style="color: #F5C400; opacity: 0.5;"></i>
            <h5 class="text-white">Aucune annonce pour "<?= htmlspecialchars($q) ?>"</h5>
            <p class="text-white-50 mb-3">Essayez d'autres mots-clés ou <a href="accueil.php" style="color: #F5C400;">retournez à l'accueil</a>.</p>
        </div>
        <?php endif; ?>

        <!-- Grille des annonces -->
        <div class="row g-4">
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
                <div class="ltm-card">
                    <!-- Image -->
                    <div class="ltm-card-img-wrapper">
                        <img src="<?= htmlspecialchars($imageUrl) ?>"
                             class="ltm-card-img"
                             alt="<?= htmlspecialchars($annonce['titre']) ?>">
                    </div>

                    <!-- Corps -->
                    <div class="ltm-card-body">
                        <!-- Badge état -->
                        <span class="ltm-badge-etat">
                            <?= htmlspecialchars($annonce['etat']) ?>
                        </span>

                        <!-- Titre -->
                        <h5 class="ltm-card-title">
                            <?= htmlspecialchars($annonce['titre']) ?>
                        </h5>

                        <!-- Description -->
                        <p class="ltm-card-desc">
                            <?= htmlspecialchars(substr($annonce['description'], 0, 80)) ?>...
                        </p>

                        <!-- Prix + bouton -->
                        <div class="ltm-card-footer">
                            <span class="ltm-card-price">
                                <?= htmlspecialchars($annonce['prix']) ?> €<small>/jour</small>
                            </span>
                            <a href="consulter_annonce.php?id=<?= htmlspecialchars($annonce['id']) ?>"
                               class="btn ltm-btn-voir">
                                Voir
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
        </div>

    </div>
</section>

<?php if(empty($q)): ?>

<!-- Séparateur -->
<div class="ltm-divider"></div>

<!-- ===== COMMENT ÇA MARCHE ===== -->
<section class="ltm-section">
    <div class="container">
        <div class="ltm-section-header mb-5">
            <h2 class="ltm-section-title">Comment ça <span style="color: #F5C400;">marche ?</span></h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="ltm-step-card">
                    <div class="ltm-step-num">1</div>
                    <h5 class="ltm-step-title">Créez votre annonce</h5>
                    <p class="ltm-step-desc">Décrivez votre matériel, ajoutez des photos et fixez votre prix en quelques minutes.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="ltm-step-card">
                    <div class="ltm-step-num">2</div>
                    <h5 class="ltm-step-title">Recevez des demandes</h5>
                    <p class="ltm-step-desc">Les acheteurs et locataires vous contactent directement via la messagerie sécurisée.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="ltm-step-card">
                    <div class="ltm-step-num">3</div>
                    <h5 class="ltm-step-title">Concluez la transaction</h5>
                    <p class="ltm-step-desc">Organisez la remise du matériel et finalisez l'accord en toute confiance.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Séparateur -->
<div class="ltm-divider"></div>

<!-- ===== BANNIÈRE CTA ===== -->
<section class="ltm-section">
    <div class="container">
        <div class="ltm-cta-banner">
            <div>
                <h3 class="ltm-cta-title">Vous avez du matériel à louer ou à vendre ?</h3>
                <p class="ltm-cta-sub">Rejoignez nos membres et publiez votre première annonce gratuitement.</p>
            </div>
            <a href="deposer_annonce.php" class="btn ltm-btn-cta-dark">
                Déposer une annonce <i class="fa-solid fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- ===== FOOTER ===== -->
<footer class="ltm-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="ltm-footer-brand">
                    <i class="fa-solid fa-tools me-2"></i>LoueTonMatos
                </div>
                <p class="ltm-footer-desc">La plateforme de location et vente de matériel entre professionnels et particuliers.</p>
            </div>
            <div class="col-6 col-lg-2 offset-lg-2">
                <h6 class="ltm-footer-heading">Navigation</h6>
                <a href="accueil.php" class="ltm-footer-link">Accueil</a>
                <a href="deposer_annonce.php" class="ltm-footer-link">Déposer une annonce</a>
                <a href="inscription.php" class="ltm-footer-link">Inscription</a>
                <a href="connexion.php" class="ltm-footer-link">Connexion</a>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="ltm-footer-heading">Mon compte</h6>
                <a href="profil.php" class="ltm-footer-link">Mon profil</a>
                <a href="mes_annonces.php" class="ltm-footer-link">Mes annonces</a>
                <a href="mes_messages.php" class="ltm-footer-link">Mes messages</a>
                <a href="mes_annonces.php#mes-favoris" class="ltm-footer-link">Mes favoris</a>
            </div>
        </div>
        <div class="ltm-footer-bottom">
            <span>© <?= date('Y') ?> LoueTonMatos — Tous droits réservés</span>
            <span>Paris, France</span>
        </div>
    </div>
</footer>

<?php endif; ?>

<!-- ===== STYLES ===== -->
<style>
  /* Base */
  .ltm-body {
    background: #1a1a1a;
    min-height: 100vh;
  }

  /* Divider */
  .ltm-divider {
    height: 1px;
    background: #2a2a2a;
    margin: 0;
  }

  /* ---- HERO ---- */
  .ltm-hero {
    background: #111111;
    padding: 70px 0 60px;
    position: relative;
    overflow: hidden;
    text-align: center;
  }

  .ltm-hero-pattern {
    position: absolute;
    inset: 0;
    background-image: repeating-linear-gradient(
      45deg,
      rgba(255,255,255,0.015) 0px,
      rgba(255,255,255,0.015) 1px,
      transparent 1px,
      transparent 20px
    );
    pointer-events: none;
  }

  .ltm-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(245, 196, 0, 0.1);
    border: 1px solid rgba(245, 196, 0, 0.3);
    border-radius: 20px;
    padding: 6px 16px;
    font-size: 12px;
    color: #F5C400;
    margin-bottom: 24px;
    position: relative;
  }

  .ltm-hero-title {
    font-size: clamp(28px, 5vw, 46px);
    font-weight: 700;
    color: #ffffff;
    line-height: 1.2;
    margin-bottom: 16px;
    position: relative;
  }

  .ltm-hero-highlight {
    color: #F5C400;
  }

  .ltm-hero-subtitle {
    font-size: 15px;
    color: #888;
    max-width: 520px;
    margin: 0 auto 32px;
    position: relative;
  }

  /* Stats hero */
  .ltm-hero-stats {
    display: inline-flex;
    align-items: center;
    gap: 32px;
    margin-bottom: 36px;
    position: relative;
  }

  .ltm-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .ltm-stat-num {
    font-size: 24px;
    font-weight: 700;
    color: #F5C400;
    line-height: 1;
  }

  .ltm-stat-label {
    font-size: 11px;
    color: #666;
    margin-top: 4px;
  }

  .ltm-stat-divider {
    width: 1px;
    height: 36px;
    background: #2a2a2a;
  }

  /* CTA Buttons hero */
  .ltm-btn-cta-primary {
    background: #F5C400;
    color: #111 !important;
    border: none;
    border-radius: 8px;
    padding: 12px 28px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.2s ease;
  }
  .ltm-btn-cta-primary:hover {
    background: #d4a800;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(245, 196, 0, 0.25);
    color: #111 !important;
  }

  .ltm-btn-cta-outline {
    background: transparent;
    color: #F5C400 !important;
    border: 1px solid rgba(245, 196, 0, 0.4);
    border-radius: 8px;
    padding: 12px 28px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.2s ease;
  }
  .ltm-btn-cta-outline:hover {
    background: rgba(245, 196, 0, 0.08);
    border-color: #F5C400;
  }

  /* ---- SECTION ---- */
  .ltm-section {
    padding: 48px 0;
  }

  .ltm-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 28px;
  }

  .ltm-section-title {
    font-size: 22px;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
  }

  .ltm-see-all {
    font-size: 13px;
    color: #F5C400;
    text-decoration: none;
    opacity: 0.8;
    transition: opacity 0.2s;
  }
  .ltm-see-all:hover { opacity: 1; color: #F5C400; }

  /* Empty state */
  .ltm-empty-state {
    text-align: center;
    padding: 60px 20px;
  }

  /* ---- CARDS ANNONCES ---- */
  .ltm-card {
    background: #222222;
    border: 1px solid #2a2a2a;
    border-radius: 12px;
    overflow: hidden;
    transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
    height: 100%;
  }

  .ltm-card:hover {
    transform: translateY(-5px);
    border-color: rgba(245, 196, 0, 0.3);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
  }

  .ltm-card-img-wrapper {
    overflow: hidden;
    height: 180px;
  }

  .ltm-card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
  }

  .ltm-card:hover .ltm-card-img {
    transform: scale(1.04);
  }

  .ltm-card-body {
    padding: 14px 16px 16px;
  }

  .ltm-badge-etat {
    display: inline-block;
    background: #111111;
    color: #F5C400;
    border: 1px solid rgba(245, 196, 0, 0.3);
    border-radius: 5px;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 9px;
    margin-bottom: 10px;
  }

  .ltm-card-title {
    font-size: 15px;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 6px;
    line-height: 1.3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .ltm-card-desc {
    font-size: 12px;
    color: #888;
    margin-bottom: 14px;
    line-height: 1.5;
  }

  .ltm-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .ltm-card-price {
    font-size: 17px;
    font-weight: 700;
    color: #F5C400;
  }

  .ltm-card-price small {
    font-size: 11px;
    font-weight: 400;
    color: #777;
  }

  .ltm-btn-voir {
    background: #F5C400;
    color: #111 !important;
    border: none;
    border-radius: 6px;
    padding: 5px 14px;
    font-size: 12px;
    font-weight: 600;
    transition: background-color 0.2s ease;
  }
  .ltm-btn-voir:hover { background: #d4a800; color: #111 !important; }

  /* ---- STEPS ---- */
  .ltm-step-card {
    background: #1e1e1e;
    border: 1px solid #2a2a2a;
    border-radius: 12px;
    padding: 28px 24px;
    height: 100%;
    transition: border-color 0.2s ease;
  }
  .ltm-step-card:hover {
    border-color: rgba(245, 196, 0, 0.3);
  }

  .ltm-step-num {
    width: 36px;
    height: 36px;
    background: #F5C400;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    font-weight: 700;
    color: #111;
    margin-bottom: 16px;
  }

  .ltm-step-title {
    font-size: 15px;
    font-weight: 700;
    color: #fff;
    margin-bottom: 8px;
  }

  .ltm-step-desc {
    font-size: 13px;
    color: #777;
    line-height: 1.6;
    margin: 0;
  }

  /* ---- CTA BANNER ---- */
  .ltm-cta-banner {
    background: #F5C400;
    border-radius: 14px;
    padding: 32px 36px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    flex-wrap: wrap;
  }

  .ltm-cta-title {
    font-size: 20px;
    font-weight: 700;
    color: #111;
    margin-bottom: 6px;
  }

  .ltm-cta-sub {
    font-size: 13px;
    color: #5a4a00;
    margin: 0;
  }

  .ltm-btn-cta-dark {
    background: #111;
    color: #F5C400 !important;
    border: none;
    border-radius: 8px;
    padding: 12px 24px;
    font-weight: 600;
    font-size: 14px;
    white-space: nowrap;
    transition: background-color 0.2s ease;
  }
  .ltm-btn-cta-dark:hover { background: #000; color: #F5C400 !important; }

  /* ---- FOOTER ---- */
  .ltm-footer {
    background: #111111;
    padding: 48px 0 0;
    border-top: 1px solid #2a2a2a;
    margin-top: 0;
  }

  .ltm-footer-brand {
    font-size: 18px;
    font-weight: 700;
    color: #F5C400;
    margin-bottom: 10px;
  }

  .ltm-footer-desc {
    font-size: 13px;
    color: #555;
    line-height: 1.6;
    max-width: 280px;
  }

  .ltm-footer-heading {
    font-size: 11px;
    font-weight: 700;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 14px;
  }

  .ltm-footer-link {
    display: block;
    font-size: 13px;
    color: #555;
    text-decoration: none;
    margin-bottom: 8px;
    transition: color 0.2s ease;
  }
  .ltm-footer-link:hover { color: #F5C400; }

  .ltm-footer-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 0;
    margin-top: 40px;
    border-top: 1px solid #1e1e1e;
    font-size: 12px;
    color: #444;
    flex-wrap: wrap;
    gap: 8px;
  }

  /* Responsive */
  @media (max-width: 767px) {
    .ltm-hero { padding: 48px 0 40px; }
    .ltm-hero-stats { gap: 20px; }
    .ltm-cta-banner { flex-direction: column; text-align: center; }
  }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>