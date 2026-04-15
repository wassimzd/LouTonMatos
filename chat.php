<?php
require_once 'session_config.php';

// 🔒 Vérifier si l'utilisateur est connecté
if (!$is_logged_in) {
    header('Location: connexion.php');
    exit;
}

// 🔹 Récupérer l'id de l'annonce et du contact depuis l'URL
$annonce_id = isset($_GET['annonce_id']) ? (int) $_GET['annonce_id'] : 0;
$contact_id = isset($_GET['contact_id']) ? (int) $_GET['contact_id'] : 0;

// 🥀 Récupérer les infos de l'annonce (titre + propriétaire)
$sql = "SELECT annonces.titre, users.nom, annonces.user_id 
        FROM annonces 
        JOIN users ON annonces.user_id = users.id 
        WHERE annonces.id = '$annonce_id'";

$resultAnnonce = mysqli_query($conn, $sql);
$annonceData = mysqli_fetch_assoc($resultAnnonce);

if (!$annonceData) {
    header('Location: accueil.php');
    exit;
}

$owner_id = (int) ($annonceData['user_id'] ?? 0);

if ($contact_id <= 0) {
    if ((int) $user_id === $owner_id) {
        header('Location: mes_messages.php');
        exit;
    }

    $contact_id = $owner_id;
}

$sqlContact = "SELECT nom FROM users WHERE id = '$contact_id'";
$resultContact = mysqli_query($conn, $sqlContact);
$contactData = mysqli_fetch_assoc($resultContact);

if (!$contactData) {
    header('Location: mes_messages.php');
    exit;
}

// 🔹 Définir les utilisateurs
$expediteur_id = (int) $user_id; // moi
$destinataire_id = $contact_id; // interlocuteur

// 🔥 ENVOI MESSAGE
if (!empty($_POST)) {
    if (isset($_POST['delete_message_id'])) {
        $delete_message_id = (int) $_POST['delete_message_id'];

        $sqlDelete = "DELETE FROM messages
                      WHERE id = '$delete_message_id'
                        AND annonce_id = '$annonce_id'
                        AND expediteur_id = '$expediteur_id'
                        AND destinataire_id = '$destinataire_id'";
        mysqli_query($conn, $sqlDelete);

        header("Location: chat.php?annonce_id=$annonce_id&contact_id=$contact_id");
        exit;
    }

    // récupérer message + sécuriser
    $message = mysqli_real_escape_string($conn, trim($_POST['message'] ?? ''));

    // insérer en base
    if ($message !== '') {
        $sql = "INSERT INTO messages (annonce_id, expediteur_id, destinataire_id, message) 
            VALUES ('$annonce_id', '$expediteur_id', '$destinataire_id', '$message')";

        mysqli_query($conn, $sql);
    }

    // recharge la page (évite double envoi)
    header("Location: chat.php?annonce_id=$annonce_id&contact_id=$contact_id");
    exit;
}

// 🔥 RÉCUPÉRER LES MESSAGES
$sql = "SELECT * FROM messages 
        WHERE annonce_id = '$annonce_id'
          AND (
              (expediteur_id = '$expediteur_id' AND destinataire_id = '$destinataire_id')
              OR
              (expediteur_id = '$destinataire_id' AND destinataire_id = '$expediteur_id')
          )
        ORDER BY date_envoi ASC, id ASC";

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
                        <?= htmlspecialchars($contactData['nom']) ?>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="mes_messages.php" class="btn btn-outline-light btn-sm rounded-2">
                        Retour
                    </a>
                    <a href="consulter_annonce.php?id=<?= htmlspecialchars($annonce_id) ?>" class="btn btn-outline-warning btn-sm rounded-2">
                        Voir
                    </a>
                </div>
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
                    <div class="<?= $est_envoye ? 'bg-warning text-dark' : 'bg-secondary text-white' ?> p-2 rounded message-bubble" style="max-width:60%;">
                        
                        <!-- 🔹 contenu du message -->
                        <div class="d-flex align-items-start gap-2">
                            <div class="flex-grow-1">
                                <?= htmlspecialchars($msg['message']) ?>
                            </div>

                            <?php if($est_envoye): ?>
                                <form method="POST" action="chat.php?annonce_id=<?= htmlspecialchars($annonce_id) ?>&contact_id=<?= htmlspecialchars($contact_id) ?>" onsubmit="return confirm('Voulez-vous supprimer ce message ?');">
                                    <input type="hidden" name="delete_message_id" value="<?= htmlspecialchars($msg['id']) ?>">
                                    <button type="submit" class="btn btn-sm btn-link text-dark p-0 delete-message-btn" title="Supprimer ce message">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>

                    </div>

                </div>

            <?php endwhile; ?>

            <?php if (mysqli_num_rows($resultMessages) === 0): ?>
                <div class="text-center text-white-50 py-5">
                    <i class="fa-solid fa-comments fa-2x mb-3" style="color: #F5C400;"></i>
                    <p class="mb-0">Aucun message dans cette discussion pour le moment.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- Formulaire -->
    <div class="mt-3">
        <form class="d-flex gap-2" method="POST" action="chat.php?annonce_id=<?= htmlspecialchars($annonce_id) ?>&contact_id=<?= htmlspecialchars($contact_id) ?>">
            <input type="text" class="form-control bg-dark text-white border-warning" placeholder="Écrivez votre message..." name="message" required>
            <button class="btn btn-warning">Envoyer</button>
        </form>
    </div>

</div>

<style>
    .message-bubble {
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.18);
    }

    .delete-message-btn {
        opacity: 0.75;
        line-height: 1;
    }

    .delete-message-btn:hover {
        opacity: 1;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
