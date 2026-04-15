<?php
// Ce fichier doit être inclus au début de chaque page PHP
// Il sert à gérer la session utilisateur et la connexion à la base de données

// Vérifie si une session PHP est déjà démarrée
// session_status() retourne l'état actuel de la session
// PHP_SESSION_ACTIVE signifie qu'une session est déjà ouverte
if(session_status() !== PHP_SESSION_ACTIVE) {
    
    // Démarre une nouvelle session
    // La session permet de stocker des informations sur l'utilisateur connecté
   
    session_start();
}

// Crée une connexion à la base de données MySQL

$conn = mysqli_connect("localhost", "root", "root", "LoueTonMatos");

// Vérifie si la connexion a échoué
// Si $conn est faux, cela signifie que MySQL n'a pas réussi à se connecter
if(!$conn) {
    
    // Arrête complètement le script avec un message d'erreur
    die("Erreur de connexion à la base de données : " . mysqli_connect_error());
}

// Définit l'encodage UTF-8 pour éviter les problèmes d'accents
mysqli_set_charset($conn, "utf8");

// Vérifie si l'utilisateur est connecté
// isset($_SESSION['user_id']) retourne true si un identifiant utilisateur existe dans la session
// Sinon retourne false
$is_logged_in = isset($_SESSION['user_id']) ? true : false;

// Récupère l'id de l'utilisateur connecté
// Si aucun utilisateur n'est connecté, la variable vaut null
$user_id = $_SESSION['user_id'] ?? null;

// Récupère le rôle de l'utilisateur connecté
$user_role = $_SESSION['user_role'] ?? 'membre';

// Vérifie si l'utilisateur est administrateur
// Si le rôle vaut "admin", alors $is_admin sera true
// Sinon il sera false
$is_admin = $user_role === 'admin';

// Définit une constante secrète pour créer un administrateur
// Une constante est une valeur qui ne peut pas être modifiée plus tard
define('ADMIN_SECRET_CODE', 'ADMIN2026LTM');
?>