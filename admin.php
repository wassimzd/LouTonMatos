<?php
require_once 'session_config.php';

//  Sécurité : accès admin uniquement
if(!$is_admin){
    exit("Accès refusé");
}

//  ACTIONS ADMIN
if(isset($_POST['action'])){
    $user_id_action = (int) $_POST['user_id'];

    // supprimer
    if($_POST['action'] === 'delete'){
        mysqli_query($conn, "DELETE FROM users WHERE id=$user_id_action AND id!=$user_id");
    }

    // 🚫 désactiver
    if($_POST['action'] === 'disable'){
        mysqli_query($conn, "UPDATE users SET actif=0 WHERE id=$user_id_action");
    }

    // réactiver
    if($_POST['action'] === 'enable'){
        mysqli_query($conn, "UPDATE users SET actif=1 WHERE id=$user_id_action");
    }

    //  promouvoir admin
    if($_POST['action'] === 'promote'){
        mysqli_query($conn, "UPDATE users SET role='admin' WHERE id=$user_id_action");
    }

    //  refresh
    header("Location: admin.php");
    exit;
}

//  récupérer tous les utilisateurs
$sql = "SELECT * FROM users ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - LoueTonMatos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="styles-global.css">
</head>

<body style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); min-height: 100vh;">

<?php include 'header.php'; ?>

<div class="container mt-5">

    <h2 class="fw-bold text-white mb-4">
        <i class="fa-solid fa-shield-halved me-2" style="color:#F5C400;"></i>
        Administration utilisateurs
    </h2>

    <div class="card bg-dark border-0 shadow-lg">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php while($user = mysqli_fetch_assoc($result)): ?>

                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['nom']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>

                            <!-- rôle -->
                            <td>
                                <span class="badge <?= $user['role'] === 'admin' ? 'bg-warning text-dark' : 'bg-secondary' ?>">
                                    <?= $user['role'] ?>
                                </span>
                            </td>

                            <!-- statut -->
                            <td>
                                <?php if(isset($user['actif']) && $user['actif'] == 0): ?>
                                    <span class="badge bg-danger">Désactivé</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Actif</span>
                                <?php endif; ?>
                            </td>

                            <!-- actions -->
                            <td>
                                <form method="POST" class="d-flex gap-2">

                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

                                    <!-- empêcher de s'auto supprimer -->
                                    <?php if($user['id'] != $user_id): ?>

                                        <button name="action" value="delete" class="btn btn-danger btn-sm">
                                            Supprimer
                                        </button>

                                        <?php if(isset($user['actif']) && $user['actif'] == 0): ?>
                                            <button name="action" value="enable" class="btn btn-success btn-sm">
                                                Activer
                                            </button>
                                        <?php else: ?>
                                            <button name="action" value="disable" class="btn btn-secondary btn-sm">
                                                Désactiver
                                            </button>
                                        <?php endif; ?>

                                        <?php if($user['role'] !== 'admin'): ?>
                                            <button name="action" value="promote" class="btn btn-warning btn-sm">
                                                Admin
                                            </button>
                                        <?php endif; ?>

                                    <?php else: ?>
                                        <span class="text-muted">Vous</span>
                                    <?php endif; ?>

                                </form>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>