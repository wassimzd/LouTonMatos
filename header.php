<?php
// Gestion de la session (déjà démarrée par session_config.php ou autre page parent)
if(session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
$is_logged = isset($_SESSION['user_id']) ? true : false;
$user_id = $_SESSION['user_id'] ?? null;
?>

<!-- ===== BARRE DE NAVIGATION ===== -->
<nav class="navbar navbar-expand-lg" style="background-color: #1a1a1a; box-shadow: 0 2px 10px rgba(245, 196, 0, 0.2);">
    <div class="container-fluid d-flex align-items-center justify-content-between">

        

        <!-- Bloc logo + déposer -->
        <div class="d-flex align-items-center gap-3 flex-shrink-0">
            <a class="navbar-brand fw-bold fs-4 logo-animate" style="color: #F5C400;" href="accueil.php">
                <i class="fa-solid fa-tools me-2"></i>LoueTonMatos
            </a>
            <a href="deposer_annonce.php" class="btn btn-warning btn-lg d-none d-lg-inline-flex align-items-center gap-2 btn-deposer">
                <i class="fa-solid fa-plus"></i>
                Déposer une annonce
            </a>

            <div class="d-none d-lg-flex align-items-center gap-2 header-shortcuts">
                <a href="<?= $is_logged ? 'mes_annonces.php#mes-favoris' : 'connexion.php' ?>"
                   class="nav-btn shortcut-btn"
                   title="Mes favoris"
                   aria-label="Mes favoris">
                    <i class="fa-solid fa-heart"></i>
                </a>

                <?php if($is_logged): ?>
                    <a  href="#"
                            class="nav-btn shortcut-btn"
                            title="Messages"
                            aria-label="Messages">
                            
                     <i class="fa-solid fa-comment" style="color: rgb(255, 212, 59);"></i>
                    </a>
                <?php else: ?>
                    <a href="connexion.php"
                       class="nav-btn shortcut-btn"
                       title="Messages"
                       aria-label="Messages">
                        <i class="fa-solid fa-comments"></i>
                    </a>
                <?php endif; ?>

                <button type="button"
                        class="nav-btn shortcut-btn"
                        title="Mes recherches"
                        aria-label="Mes recherches"
                        onclick="focusHeaderSearch()">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </button>

                <?php if($is_logged): ?>
                    <button type="button"
                            class="nav-btn shortcut-btn profile-shortcut"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#profileMenu"
                            title="Mon profil"
                            aria-label="Mon profil">
                        <i class="fa-solid fa-circle-user"></i>
                    </button>
                <?php else: ?>
                    <a href="connexion.php"
                       class="nav-btn shortcut-btn profile-shortcut"
                       title="Mon profil"
                       aria-label="Mon profil">
                        <i class="fa-solid fa-circle-user"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Barre de recherche centrale -->
        <div class="flex-fill mx-lg-4">
            <form class="d-flex align-items-center" action="accueil.php" method="GET">
                <input type="search" id="headerSearchInput" name="q" class="form-control form-control-lg search-input" placeholder="Rechercher du matériel...">
                <button class="btn btn-warning btn-lg ms-2 search-btn" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>

        <!-- Boutons de navigation et profil -->
        <div class="d-flex align-items-center gap-2 flex-shrink-0">
           <?php if(!$is_logged): ?>
               <!-- Boutons de connexion/inscription avec animations -->
               <a href="connexion.php" class="btn btn-warning btn-sm btn-nav-hover">
                   <i class="fa-solid fa-sign-in-alt me-1"></i> Connexion
               </a>
               <a href="inscription.php" class="btn btn-outline-warning btn-sm btn-nav-hover">
                   <i class="fa-solid fa-user-plus me-1"></i> Inscription
               </a>
           <?php endif; ?>
        </div>

    </div>
</nav>

<!-- ===== MENU LATÉRAL DU PROFIL ===== -->
<?php if($is_logged): ?>
<div class="offcanvas offcanvas-end" tabindex="-1" id="profileMenu" style="background-color: #1a1a1a;">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title fw-bold" style="color: #F5C400;">Mon Profil</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div class="d-flex flex-column gap-3">
      <!-- Lien vers le profil -->
      <a href="profil.php" class="btn btn-warning w-100 menu-btn-hover">
        <i class="fa-solid fa-user me-2"></i> Mon Compte
      </a>
      <hr style="border-color: #F5C400;">
      <!-- Bouton déconnexion avec animation -->
      <a href="deconnexion.php" class="btn btn-danger w-100 menu-btn-hover">
        <i class="fa-solid fa-sign-out-alt me-2"></i> Déconnexion
      </a>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- ===== STYLES POUR LES ANIMATIONS ===== -->
<style>
  /* Animation du logo au survol */
  .logo-animate {
    transition: all 0.3s ease;
  }

  .logo-animate:hover {
    transform: scale(1.05);
    text-shadow: 0 0 10px rgba(245, 196, 0, 0.5);
  }

  /* Animation du champ de recherche */
  .search-input {
    transition: all 0.3s ease;
  }

  .search-input:focus {
    box-shadow: 0 0 12px rgba(245, 196, 0, 0.4);
    border-color: #F5C400 !important;
  }

  /* Animation du bouton recherche */
  .search-btn {
    transition: all 0.3s ease;
  }

  .search-btn:hover {
    background-color: #d4a000;
    transform: scale(1.05);
  }

  .nav-btn {
    min-width: 42px;
    min-height: 42px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    border: 1px solid rgba(245, 196, 0, 0.4);
    background: transparent;
    color: #F5C400;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .nav-btn i {
    font-size: 1rem;
    transition: transform 0.3s ease;
  }

  .nav-btn:hover,
  .nav-btn:focus {
    background-color: rgba(245, 196, 0, 0.15);
    color: white;
    transform: translateY(-2px);
  }

  .nav-btn:hover i,
  .nav-btn:focus i {
    transform: scale(1.1);
  }

  .shortcut-btn {
    box-shadow: none !important;
    outline: none;
  }

  .profile-shortcut i {
    font-size: 1.15rem;
  }

  .header-shortcuts {
    margin-left: 0.25rem;
  }

  /* Animation des boutons de navigation */
  .btn-nav-hover {
    transition: all 0.3s ease;
  }

  .btn-nav-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(245, 196, 0, 0.3);
  }

  /* Animation des boutons du menu */
  .menu-btn-hover {
    transition: all 0.3s ease;
  }

  .menu-btn-hover:hover {
    transform: translateX(5px);
    box-shadow: 0 0 10px rgba(245, 196, 0, 0.3);
  }
</style>

<!-- ===== SCRIPTS JAVASCRIPT ===== -->
<script>
// Animation de la navbar au scroll
window.addEventListener('scroll', function() {
  const navbar = document.querySelector('.navbar');
  if (window.scrollY > 50) {
    navbar.style.boxShadow = '0 4px 15px rgba(245, 196, 0, 0.3)';
  } else {
    navbar.style.boxShadow = '0 2px 10px rgba(245, 196, 0, 0.2)';
  }
});

// Animation au survol des cartes (pour les pages avec des cartes)
document.addEventListener('DOMContentLoaded', function() {
  const cards = document.querySelectorAll('.card');
  cards.forEach(card => {
    card.addEventListener('mouseenter', function() {
      this.style.transform = 'translateY(-8px)';
      this.style.boxShadow = '0 8px 20px rgba(0, 0, 0, 0.3)';
      this.style.transition = 'all 0.3s ease';
    });
    card.addEventListener('mouseleave', function() {
      this.style.transform = 'translateY(0)';
      this.style.boxShadow = '0 0 0 rgba(0, 0, 0, 0.1)';
    });
  });
});

// Fonction pour afficher une notification
function showNotification(message, type = 'success') {
  const alertDiv = document.createElement('div');
  alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
  alertDiv.style.top = '80px';
  alertDiv.style.right = '20px';
  alertDiv.style.zIndex = '9999';
  alertDiv.innerHTML = `
    ${message}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  `;
  document.body.appendChild(alertDiv);

  setTimeout(() => {
    alertDiv.remove();
  }, 4000);
}

function focusHeaderSearch() {
  const searchInput = document.getElementById('headerSearchInput');
  if (!searchInput) {
    return;
  }

  searchInput.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' });
  searchInput.focus();
}
</script>
