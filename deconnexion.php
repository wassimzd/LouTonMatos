<?php
// ===== PAGE DE DÉCONNEXION =====
// Déconnecte l'utilisateur et le redirige vers l'accueil

// Inclure la gestion centralisée des sessions
require_once 'session_config.php';

// Détruire la session de l'utilisateur
if(isset($_SESSION['user_id'])) {
    unset($_SESSION['user_id']);
}

// Détruire la session complète
session_destroy();

// Rediriger vers l'accueil
header("Location: accueil.php");
exit;
?>
