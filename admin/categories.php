<?php
// ============================================================
// admin/categories.php — Gestion des catégories
// COMMIT PAR : FERIEL (Membre A)
// ============================================================
require_once 'auth_guard.php';
require_once '../config.php';
require_once '../classes/Database.php';
require_once '../classes/Categorie.php';
require_once '../classes/CategorieDAO.php';

$dao     = new CategorieDAO();
$message = '';
$erreur  = '';

// ---- SUPPRESSION ----
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    if ($dao->delete((int)$_GET['id'])) {
        $message = 'Catégorie supprimée.';
    } else {
        $erreur = 'Impossible de supprimer (des matériels y sont liés ?).';
    }
}

// ---- AJOUT ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $nom  = trim(htmlspecialchars($_POST['nom']         ?? ''));
    $desc = trim(htmlspecialchars($_POST['description'] ?? ''));

    if (empty($nom)) {
        $erreur = 'Le nom de la catégorie est obligatoire.';
    } else {
        $cat = new Categorie(0, $nom, $desc);
        if ($dao->add($cat)) {
            $message = "Catégorie \"$nom\" ajoutée !";
        } else {
            $erreur = 'Cette catégorie existe déjà.';
        }
    }
}

$categories = $dao->getAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catégories — Admin SportLoc</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin">
<nav class="navbar admin-nav">
    <div class="nav-logo">🛡️ SportLoc Admin</div>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="materiel.php">Matériel</a>
        <a href="categories.php" class="active">Catégories</a>
        <a href="reservations.php">Réservations</a>
        <a href="../public/deconnexion.php" class="btn-danger">Déconnexion</a>
    </div>
</nav>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Gestion des catégories</h1>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>
    <?php if ($erreur): ?>
        <div class="alert alert-error"><?= $erreur ?></div>
    <?php endif; ?>

    <!-- Formulaire d'ajout -->
    <div class="card form-section">
        <h3>Ajouter une catégorie</h3>
        <form method="POST" action="categories.php">
            <input type="hidden" name="action" value="add">
            <div class="form-grid">
                <div class="form-group">
                    <label>Nom *</label>
                    <input type="text" name="nom" placeholder="ex: Kayak">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <input type="text" name="description" placeholder="Description courte">
                </div>
            </div>
            <button type="submit" class="btn-submit">Ajouter</button>
        </form>
    </div>

    <!-- Liste des catégories -->
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr><th>#</th><th>Nom</th><th>Description</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= $cat->getId() ?></td>
                        <td><?= htmlspecialchars($cat->getNom()) ?></td>
                        <td><?= htmlspecialchars($cat->getDescription()) ?></td>
                        <td>
                            <a href="categories.php?action=delete&id=<?= $cat->getId() ?>"
                               class="btn-danger btn-sm"
                               onclick="return confirm('Supprimer cette catégorie ?')">
                                Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
