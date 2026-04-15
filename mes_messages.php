<?php
require_once 'session_config.php';

if (!$is_logged_in) {
    header('Location: connexion.php');
    exit;
}

$sql = "SELECT messages.id, messages.annonce_id, messages.expediteur_id, messages.destinataire_id,
               messages.message, messages.date_envoi, annonces.titre,
               expediteur.nom AS expediteur_nom, destinataire.nom AS destinataire_nom
        FROM messages
        JOIN annonces ON messages.annonce_id = annonces.id
        JOIN users AS expediteur ON messages.expediteur_id = expediteur.id
        JOIN users AS destinataire ON messages.destinataire_id = destinataire.id
        WHERE messages.expediteur_id = '$user_id' OR messages.destinataire_id = '$user_id'
        ORDER BY messages.date_envoi DESC, messages.id DESC";
$result = mysqli_query($conn, $sql);

$discussions = [];

if ($result) {
    while ($message = mysqli_fetch_assoc($result)) {
        $is_expediteur = (int) $message['expediteur_id'] === (int) $user_id;
        $other_user_id = $is_expediteur ? $message['destinataire_id'] : $message['expediteur_id'];
        $other_user_name = $is_expediteur ? $message['destinataire_nom'] : $message['expediteur_nom'];
        $discussion_key = $message['annonce_id'] . '-' . $other_user_id;

        if (isset($discussions[$discussion_key])) {
            continue;
        }

        $message_preview = trim($message['message']);
        if (strlen($message_preview) > 90) {
            $message_preview = substr($message_preview, 0, 87) . '...';
        }

        $discussions[$discussion_key] = [
            'annonce_id' => $message['annonce_id'],
            'contact_id' => $other_user_id,
            'contact_name' => $other_user_name,
            'titre' => $message['titre'],
            'preview' => $message_preview,
            'date_envoi' => $message['date_envoi'],
        ];
    }
}

function formatDiscussionDate($date_value) {
    if (empty($date_value)) {
        return '';
    }

    $timestamp = strtotime($date_value);
    return $timestamp ? date('d/m/Y H:i', $timestamp) : $date_value;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes messages - LoueTonMatos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); min-height: 100vh;">
    <?php include 'header.php'; ?>

    <div class="container mt-5 mb-5">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
            <h3 class="fw-bold mb-0" style="color: white; border-bottom: 3px solid #F5C400; padding-bottom: 10px;">
                <i class="fa-solid fa-comments me-2" style="color: #F5C400;"></i>Mes discussions
            </h3>
        </div>

        <div class="card bg-dark border-0 shadow-lg rounded-4" style="border-top: 3px solid #F5C400;">
            <div class="card-body p-0">
                <?php if (!empty($discussions)): ?>
                    <?php foreach ($discussions as $discussion): ?>
                        <div class="discussion-row px-4 py-3 border-bottom border-secondary-subtle">
                            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                                <div class="flex-grow-1">
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                        <span class="badge rounded-pill text-dark" style="background-color: #F5C400;">Annonce</span>
                                        <span class="fw-bold text-white"><?= htmlspecialchars($discussion['titre']) ?></span>
                                    </div>

                                    <p class="mb-1 text-white-50">
                                        <i class="fa-solid fa-user me-2" style="color: #F5C400;"></i>
                                        <?= htmlspecialchars($discussion['contact_name']) ?>
                                    </p>

                                    <p class="mb-0 text-white-50">
                                        <i class="fa-solid fa-envelope me-2" style="color: #F5C400;"></i>
                                        <?= htmlspecialchars($discussion['preview']) ?>
                                    </p>
                                </div>

                                <div class="text-lg-end">
                                    <p class="mb-2 text-white-50 small">
                                        <i class="fa-solid fa-clock me-2" style="color: #F5C400;"></i>
                                        <?= htmlspecialchars(formatDiscussionDate($discussion['date_envoi'])) ?>
                                    </p>
                                    <a href="chat.php?annonce_id=<?= urlencode($discussion['annonce_id']) ?>&contact_id=<?= urlencode($discussion['contact_id']) ?>"
                                       class="btn btn-warning btn-sm fw-semibold">
                                        Entrer dans la discussion
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center px-4 py-5">
                        <i class="fa-solid fa-comments fa-3x mb-3" style="color: #F5C400;"></i>
                        <h4 class="text-white fw-bold">Aucune discussion pour le moment</h4>
                        <p class="text-white-50 mb-0">Quand vous contacterez un proprietaire ou recevrez un message, vos conversations apparaitront ici.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <style>
      .discussion-row:last-child {
        border-bottom: 0 !important;
      }

      .discussion-row {
        transition: background-color 0.3s ease;
      }

      .discussion-row:hover {
        background-color: rgba(245, 196, 0, 0.06);
      }
    </style>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</html>
