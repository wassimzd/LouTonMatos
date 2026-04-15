<?php
// Inclure la configuration de session et la connexion à la base de données
require_once 'session_config.php';
// Ce fichier doit être inclus en haut de chaque page pour gérer les sessions et la connexion à la base de données
// Vérifier si l'utilisateur est connecté
$is_logged = isset($_SESSION['user_id']);
$user_role = $_SESSION['user_role'] ?? 'membre';
$is_admin = $user_role === 'admin'; // Vérification de rôle pour afficher les options d'administration


?>

<!-- ===== BARRE DE NAVIGATION ===== -->
<nav class="navbar navbar-expand-lg ltm-navbar">
    <div class="container-fluid d-flex align-items-center justify-content-between px-4">

        <!-- Bloc logo + déposer -->
        <div class="d-flex align-items-center gap-3 flex-shrink-0">
            <a class="navbar-brand fw-bold ltm-logo" href="accueil.php">
                <i class="fa-solid fa-tools me-2"></i>LoueTonMatos
            </a>
            <a href="deposer_annonce.php" class="btn ltm-btn-primary d-none d-lg-inline-flex align-items-center gap-2">
                <i class="fa-solid fa-plus"></i> Déposer une annonce
            </a>

            <div class="d-none d-lg-flex align-items-center gap-2">
                <a href="<?= $is_logged ? 'mes_annonces.php#mes-favoris' : 'connexion.php' ?>"
                   class="ltm-icon-btn" title="Mes favoris">
                    <i class="fa-solid fa-heart"></i>
                </a>

                <?php if($is_logged): ?>
                    <a href="mes_messages.php" class="ltm-icon-btn" title="Messages">
                        <i class="fa-solid fa-comment"></i>
                    </a>
                <?php else: ?>
                    <a href="connexion.php" class="ltm-icon-btn" title="Messages">
                        <i class="fa-solid fa-comments"></i>
                    </a>
                <?php endif; ?>

                <button type="button" class="ltm-icon-btn" title="Rechercher" onclick="focusHeaderSearch()">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </button>

                <?php if($is_logged): ?>
                    <button type="button"
                            class="ltm-icon-btn"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#profileMenu"
                            title="Mon profil">
                        <i class="fa-solid fa-circle-user"></i>
                    </button>
                <?php else: ?>
                    <a href="connexion.php" class="ltm-icon-btn" title="Mon profil">
                        <i class="fa-solid fa-circle-user"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Barre de recherche centrale -->
        <div class="flex-fill mx-4">
            <form class="d-flex align-items-center ltm-search-form" action="accueil.php" method="GET">
                <input type="search" id="headerSearchInput" name="q"
                       class="form-control ltm-search-input"
                       placeholder="Rechercher du matériel...">
                <button class="btn ltm-search-btn" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>

        <!-- Boutons connexion/inscription -->
        <div class="d-flex align-items-center gap-2 flex-shrink-0">
           <?php if(!$is_logged): ?>
               <a href="connexion.php" class="btn ltm-btn-primary btn-sm">
                   <i class="fa-solid fa-sign-in-alt me-1"></i> Connexion
               </a>
               <a href="inscription.php" class="btn ltm-btn-outline btn-sm">
                   <i class="fa-solid fa-user-plus me-1"></i> Inscription
               </a>
           <?php endif; ?>
        </div>

    </div>
</nav>

<!-- ===== MENU LATÉRAL DU PROFIL ===== -->
<?php if($is_logged): ?>
<div class="offcanvas offcanvas-end ltm-offcanvas" tabindex="-1" id="profileMenu">
  <div class="offcanvas-header" style="border-bottom: 1px solid #2a2a2a;">
    <div class="d-flex align-items-center gap-2">
        <div class="ltm-avatar">
            <i class="fa-solid fa-circle-user fa-lg" style="color: #F5C400;"></i>
        </div>
        <h5 class="offcanvas-title fw-bold mb-0" style="color: #F5C400;">Mon Profil</h5>
    </div>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body pt-4">
    <div class="d-flex flex-column gap-3">
      <a href="profil.php" class="ltm-menu-item">
        <i class="fa-solid fa-user me-2"></i> Mon Compte
      </a>
      <a href="mes_annonces.php" class="ltm-menu-item">
        <i class="fa-solid fa-bullhorn me-2"></i> Mes Annonces
      </a>
      <a href="mes_messages.php" class="ltm-menu-item">
        <i class="fa-solid fa-comments me-2"></i> Mes Messages
      </a>
      <a href="mes_annonces.php#mes-favoris" class="ltm-menu-item">
        <i class="fa-solid fa-heart me-2"></i> Mes Favoris
      </a>
      <?php if($is_admin): ?>
      <a href="admin.php" class="ltm-menu-item ltm-menu-item--admin">
        <i class="fa-solid fa-shield-halved me-2"></i> Administration
      </a>
      <?php endif; ?>
      <hr style="border-color: #2a2a2a;">
      <a href="deposer_annonce.php" class="btn ltm-btn-primary w-100">
        <i class="fa-solid fa-plus me-2"></i> Déposer une annonce
      </a>
      <a href="deconnexion.php" class="btn ltm-btn-danger w-100">
        <i class="fa-solid fa-sign-out-alt me-2"></i> Déconnexion
      </a>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- ===== STYLES NAVBAR ===== -->
<style>
  .ltm-navbar {
    background-color: #111111;
    border-bottom: 1px solid #2a2a2a;
    box-shadow: 0 2px 20px rgba(245, 196, 0, 0.08);
    padding: 12px 0;
    position: sticky;
    top: 0;
    z-index: 1030;
  }

  .ltm-logo {
    color: #F5C400 !important;
    font-size: 1.2rem;
    text-decoration: none;
    transition: opacity 0.2s ease;
    letter-spacing: -0.3px;
  }

  .ltm-logo:hover { opacity: 0.85; }

  /* Bouton principal jaune */
  .ltm-btn-primary {
    background-color: #F5C400;
    color: #111111 !important;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 13px;
    padding: 8px 16px;
    transition: background-color 0.2s ease, transform 0.15s ease;
  }
  .ltm-btn-primary:hover {
    background-color: #d4a800;
    transform: translateY(-1px);
    color: #111111 !important;
  }

  /* Bouton outline jaune */
  .ltm-btn-outline {
    background: transparent;
    color: #F5C400 !important;
    border: 1px solid rgba(245, 196, 0, 0.5);
    border-radius: 8px;
    font-weight: 600;
    font-size: 13px;
    padding: 8px 16px;
    transition: all 0.2s ease;
  }
  .ltm-btn-outline:hover {
    background: rgba(245, 196, 0, 0.1);
    border-color: #F5C400;
  }

  /* Bouton danger */
  .ltm-btn-danger {
    background-color: #dc3545;
    color: white !important;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 13px;
    padding: 8px 16px;
    transition: background-color 0.2s ease;
  }
  .ltm-btn-danger:hover { background-color: #b02a37; color: white !important; }

  /* Icônes rondes */
  .ltm-icon-btn {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(245, 196, 0, 0.3);
    background: transparent;
    color: #F5C400;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 14px;
  }
  .ltm-icon-btn:hover {
    background: rgba(245, 196, 0, 0.12);
    border-color: #F5C400;
    color: #fff;
    transform: translateY(-2px);
  }

  /* Barre de recherche */
  .ltm-search-form {
    background: #1e1e1e;
    border: 1px solid #2a2a2a;
    border-radius: 10px;
    overflow: hidden;
    transition: border-color 0.2s ease;
  }
  .ltm-search-form:focus-within {
    border-color: rgba(245, 196, 0, 0.5);
    box-shadow: 0 0 0 3px rgba(245, 196, 0, 0.08);
  }
  .ltm-search-input {
    background: transparent !important;
    border: none !important;
    color: #fff !important;
    padding: 10px 16px;
    font-size: 14px;
    box-shadow: none !important;
  }
  .ltm-search-input::placeholder { color: #555; }
  .ltm-search-btn {
    background: #F5C400;
    border: none;
    border-radius: 0 8px 8px 0;
    padding: 0 16px;
    color: #111;
    font-size: 14px;
    transition: background-color 0.2s ease;
  }
  .ltm-search-btn:hover { background-color: #d4a800; }

  /* Offcanvas */
  .ltm-offcanvas {
    background-color: #111111;
    width: 300px !important;
  }

  /* Items menu offcanvas */
  .ltm-menu-item {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    color: #ccc;
    text-decoration: none;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s ease;
    border: 1px solid transparent;
  }
  .ltm-menu-item:hover {
    background: rgba(245, 196, 0, 0.08);
    border-color: rgba(245, 196, 0, 0.2);
    color: #F5C400;
    padding-left: 20px;
  }
  .ltm-menu-item--admin {
    color: #F5C400;
    border-color: rgba(245, 196, 0, 0.2);
  }
  .ltm-menu-item--admin:hover {
    background: rgba(245, 196, 0, 0.15);
  }

  /* Scroll navbar shadow */
  .ltm-navbar.scrolled {
    box-shadow: 0 4px 20px rgba(245, 196, 0, 0.15);
  }
</style>

<script>
window.addEventListener('scroll', function() {
  const navbar = document.querySelector('.ltm-navbar');
  if (navbar) {
    navbar.classList.toggle('scrolled', window.scrollY > 50);
  }
});

function focusHeaderSearch() {
  const searchInput = document.getElementById('headerSearchInput');
  if (!searchInput) return;
  searchInput.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  searchInput.focus();
}
</script>