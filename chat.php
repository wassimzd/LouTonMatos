<?php
require_once 'session_config.php';

// 🔒 Vérifier si l'utilisateur est connecté
if (!$is_logged_in) {
    header('Location: connexion.php');
    exit;
}

// 🔹 Récupérer l'id de l'annonce depuis l'URL
$annonce_id = $_GET['annonce_id'] ?? '';

// 🥀 Récupérer les infos de l'annonce (titre + propriétaire)
$sql = "SELECT annonces.titre, users.nom, annonces.user_id 
        FROM annonces 
        JOIN users ON annonces.user_id = users.id 
        WHERE annonces.id = '$annonce_id'";

$resultAnnonce = mysqli_query($conn, $sql);
$annonceData = mysqli_fetch_assoc($resultAnnonce);

// 🔹 Définir les utilisateurs
$expediteur_id = $user_id; // moi
$destinataire_id = $annonceData['user_id'] ?? ''; // propriétaire

// 🔥 ENVOI MESSAGE
if (!empty($_POST)) {

    // récupérer message + sécuriser
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // insérer en base
    $sql = "INSERT INTO messages (annonce_id, expediteur_id, destinataire_id, message) 
            VALUES ('$annonce_id', '$expediteur_id', '$destinataire_id', '$message')";

    mysqli_query($conn, $sql);

    // recharge la page (évite double envoi)
    header("Location: chat.php?annonce_id=$annonce_id");
    exit;
}

// 🔥 RÉCUPÉRER LES MESSAGES
$sql = "SELECT * FROM messages 
        WHERE annonce_id = '$annonce_id'
        ORDER BY date_envoi ASC";

$resultMessages = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - LoueTonMatos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); min-height: 100vh;">
    <?php include 'header.php'; ?>
<div class="container mt-5 mb-5" style="max-width: 700px; height: 90vh; display: flex; flex-direction: column;">

    <!-- En-tête du chat -->
    <div class="card bg-dark border-0 mb-3 shadow-lg rounded-3" style="border-top: 3px solid #F5C400;">
        <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-1 text-white fw-bold">
                        <?= htmlspecialchars($annonceData['titre']) ?>
                    </h5>
                    <p class="mb-0 text-white-50" style="font-size: 13px;">
                        <i class="fa-solid fa-circle-user me-2" style="color: #F5C400;"></i> 
                        <?= htmlspecialchars($annonceData['nom']) ?>
                    </p>
                </div>
                <a href="consulter_annonce.php?id=<?= htmlspecialchars($annonce_id) ?>" class="btn btn-outline-warning btn-sm rounded-2">
                    Voir
                </a>
            </div>
        </div>
    </div>

    <!-- Zone des messages -->
    <div class="card bg-dark border-0 shadow-lg rounded-3 flex-grow-1" style="overflow-y: auto;">
        <div class="card-body p-4">

            <?php
            // 🔁 boucle sur tous les messages
            while($msg = mysqli_fetch_assoc($resultMessages)):
            ?>

                <?php
                // 🔹 vérifier si le message vient de moi
                $est_envoye = $msg['expediteur_id'] == $user_id;
                ?>

                <!-- 🔹 position du message -->
                <div class="d-flex mb-2 <?= $est_envoye ? 'justify-content-end' : 'justify-content-start' ?>">
                    
                    <!-- 🔹 style du message -->
                    <div class="<?= $est_envoye ? 'bg-warning text-dark' : 'bg-secondary text-white' ?> p-2 rounded" style="max-width:60%;">
                        
                        <!-- 🔹 contenu du message -->
                        <?= htmlspecialchars($msg['message']) ?>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>
    </div>

    <!-- Formulaire -->
    <div class="mt-3">
        <form class="d-flex gap-2" method="POST" action="chat.php?annonce_id=<?= htmlspecialchars($annonce_id) ?>">
            <input type="text" class="form-control bg-dark text-white border-warning" placeholder="Écrivez votre message..." name="message" required>
            <button class="btn btn-warning">Envoyer</button>
        </form>
    </div>

</div>

</body>
</html>
