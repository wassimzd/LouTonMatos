<?php
// ===== GESTION CENTRALISÉE DES SESSIONS =====
// Ce fichier démarre la session et doit être inclus en première ligne de chaque page PHP

// Démarrer la session une seule fois (vérifier le statut pour éviter les erreurs)
if(session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Connexion à la base de données (réutilisable partout)
$conn = mysqli_connect("localhost", "root", "root", "LoueTonMatos");

// Vérifier la connexion à la base de données
if(!$conn) {
    die("Erreur de connexion à la base de données : " . mysqli_connect_error());
}

// Définir le charset en UTF-8
mysqli_set_charset($conn, "utf8");

// Variable globale pour vérifier si l'utilisateur est connecté
$is_logged_in = isset($_SESSION['user_id']) ? true : false;
$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['user_role'] ?? 'membre';
$is_admin = $user_role === 'admin';

define('ADMIN_SECRET_CODE', 'ADMIN2026LTM');
?>
