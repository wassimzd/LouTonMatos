<?php
require_once 'session_config.php';

if (!$is_logged_in) {
    header('Location: connexion.php');
    exit;
}

$annonce_id = (int) ($_GET['id'] ?? $_POST['annonce_id'] ?? 0);
$message = '';
$message_type = 'success';

$sql = "SELECT * FROM annonces WHERE id = $annonce_id AND user_id = $user_id";
$result = mysqli_query($conn, $sql);
$annonce = mysqli_fetch_assoc($result);

if (!$annonce) {
    header('Location: mes_annonces.php');
    exit;
}

if (isset($_POST['delete_annonce'])) {
    mysqli_query($conn, "DELETE FROM favoris WHERE annonce_id = $annonce_id");
    $sql_delete = "DELETE FROM annonces WHERE id = $annonce_id AND user_id = $user_id";

    if (mysqli_query($conn, $sql_delete)) {
        header('Location: mes_annonces.php');
        exit;
    }

    $message = "Erreur lors de la suppression de l'annonce.";
    $message_type = 'danger';
}

if (isset($_POST['save_annonce'])) {
    $titre = mysqli_real_escape_string($conn, trim($_POST['titre'] ?? ''));
    $description = mysqli_real_escape_string($conn, trim($_POST['description'] ?? ''));
    $prix = (float) ($_POST['prix'] ?? 0);
    $etat = mysqli_real_escape_string($conn, trim($_POST['etat'] ?? ''));
    $imagePath = $annonce['image'] ?? '';

    if (empty($titre) || empty($description) || $prix <= 0 || empty($etat)) {
        $message = 'Tous les champs sauf la photo sont obligatoires.';
        $message_type = 'danger';
    } else {
        if (isset($_FILES['image']) && ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $imageName = basename($_FILES['image']['name']);
            $destination = 'uploads/' . $imageName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $imagePath = $imageName;
            } else {
                $message = "Impossible d'enregistrer la nouvelle image.";
                $message_type = 'danger';
            }
        }

        if (empty($message)) {
            $sql_update = "UPDATE annonces
                           SET titre = '$titre',
                               description = '$description',
                               prix = '$prix',
                               etat = '$etat',
                               image = '$imagePath'
                           WHERE id = $annonce_id AND user_id = $user_id";

            if (mysqli_query($conn, $sql_update)) {
                $message = 'Annonce mise a jour avec succes.';
                $message_type = 'success';

                $sql = "SELECT * FROM annonces WHERE id = $annonce_id AND user_id = $user_id";
                $result = mysqli_query($conn, $sql);
                $annonce = mysqli_fetch_assoc($result);
            } else {
                $message = "Erreur lors de la modification de l'annonce.";
                $message_type = 'danger';
            }
        }
    }
}

$imageUrl = $annonce['image'] ?? '';
if (!empty($imageUrl) && strpos($imageUrl, 'uploads/') !== 0) {
    $imageUrl = 'uploads/' . $imageUrl;
}
$imageUrl = $imageUrl ?: 'https://via.placeholder.com/600x400?text=Pas+d\'image';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'annonce - LoueTonMatos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="styles-global.css">
</head>
<body style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); min-height: 100vh;">
<?php include 'header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-lg" style="background: #111111; border-top: 4px solid #F5C400;">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex justify-content-between align-items-center flex-column flex-md-row gap-3 mb-4">
                        <div>
                            <h2 class="fw-bold text-white mb-1">
                                <i class="fa-solid fa-pen-to-square me-2" style="color: #F5C400;"></i>Modifier l'annonce
                            </h2>
                            <p class="text-white-50 mb-0">Mettez a jour votre annonce ou supprimez-la.</p>
                        </div>
                        <a href="consulter_annonce.php?id=<?= htmlspecialchars($annonce['id']) ?>" class="btn btn-outline-warning">
                            Retour a l'annonce
                        </a>
                    </div>

                    <?php if(!empty($message)): ?>
                        <div class="alert alert-<?= htmlspecialchars($message_type) ?> alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($message) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row g-4">
                        <div class="col-lg-5">
                            <div class="preview-card h-100">
                                <img src="<?= htmlspecialchars($imageUrl) ?>" alt="Image annonce" class="img-fluid rounded-3 mb-3" style="width: 100%; height: 260px; object-fit: cover;">
                                <div class="text-white">
                                    <h4 class="fw-bold"><?= htmlspecialchars($annonce['titre']) ?></h4>
                                    <p class="mb-2" style="color: #F5C400; font-weight: 700;"><?= htmlspecialchars($annonce['prix']) ?> EUR/jour</p>
                                    <span class="badge" style="background-color: rgba(245, 196, 0, 0.15); color: #F5C400;">
                                        <?= htmlspecialchars($annonce['etat']) ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-7">
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="annonce_id" value="<?= htmlspecialchars($annonce['id']) ?>">

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-white">Titre de l'annonce</label>
                                    <input type="text" name="titre" class="form-control form-input" value="<?= htmlspecialchars($annonce['titre']) ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-white">Description</label>
                                    <textarea name="description" rows="5" class="form-control form-input" required><?= htmlspecialchars($annonce['description']) ?></textarea>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-white">Prix par jour</label>
                                        <input type="number" name="prix" class="form-control form-input" min="0" step="0.01" value="<?= htmlspecialchars($annonce['prix']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-white">Etat</label>
                                        <select name="etat" class="form-select form-input" required>
                                            <?php
                                            $etats = ['Neuf', 'Tres bon etat', 'Bon etat', 'Etat correct', 'A reparer'];
                                            if (!in_array($annonce['etat'], $etats, true)) {
                                                array_unshift($etats, $annonce['etat']);
                                            }
                                            foreach ($etats as $etatOption):
                                            ?>
                                                <option value="<?= htmlspecialchars($etatOption) ?>" <?= $annonce['etat'] === $etatOption ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($etatOption) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-3 mb-4">
                                    <label class="form-label fw-bold text-white">Nouvelle photo</label>
                                    <input type="file" name="image" class="form-control form-input" accept="image/*">
                                    <small class="text-white-50">Laissez vide si vous gardez l'image actuelle.</small>
                                </div>

                                <div class="d-flex gap-3 flex-column flex-sm-row">
                                    <button type="submit" name="save_annonce" class="btn btn-warning flex-grow-1 fw-bold">
                                        <i class="fa-solid fa-floppy-disk me-2"></i>Enregistrer les modifications
                                    </button>
                                    <button type="submit" name="delete_annonce" class="btn btn-outline-danger flex-grow-1 fw-bold" onclick="return confirm('Voulez-vous vraiment supprimer cette annonce ?');">
                                        <i class="fa-solid fa-trash me-2"></i>Supprimer l'annonce
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .preview-card {
        background: linear-gradient(160deg, #1a1a1a 0%, #232323 100%);
        border: 1px solid rgba(245, 196, 0, 0.15);
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 20px 45px rgba(0, 0, 0, 0.25);
    }

    .form-input {
        background: #1c1c1c;
        color: white;
        border: 1px solid rgba(245, 196, 0, 0.2);
        border-radius: 10px;
        padding: 12px 14px;
    }

    .form-input:focus {
        background: #222222;
        color: white;
        border-color: #F5C400;
        box-shadow: 0 0 0 0.2rem rgba(245, 196, 0, 0.15);
    }

    .form-select.form-input option {
        color: black;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
