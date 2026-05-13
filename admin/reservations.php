<?php
// ============================================================
// admin/reservations.php — Gestion des réservations
// COMMIT PAR : ZEINEB (Membre C)
// ============================================================
require_once 'auth_guard.php';
require_once '../config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/Materiel.php';
require_once '../classes/MaterielDAO.php';
require_once '../classes/Reservation.php';
require_once '../classes/ReservationDAO.php';

$dao     = new ReservationDAO();
$message = '';
$erreur  = '';

// ---- CHANGEMENT DE STATUT ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_statut') {
    $id     = (int)$_POST['id'];
    $statut = $_POST['statut'] ?? '';

    // Vérifie que le statut est valide (sécurité)
    $statuts_ok = ['en attente', 'confirmée', 'annulée'];
    if (in_array($statut, $statuts_ok)) {
        if ($dao->updateStatut($id, $statut)) {
            $message = 'Statut mis à jour.';
        } else {
            $erreur = 'Erreur lors de la mise à jour.';
        }
    }
}

// ---- SUPPRESSION ----
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    if ($dao->delete((int)$_GET['id'])) {
        $message = 'Réservation supprimée.';
    }
}

// Filtre par statut si demandé dans l'URL
$filtre       = $_GET['statut'] ?? '';
$reservations = $dao->getAll();

if ($filtre) {
    // array_filter garde seulement les éléments qui passent le test
    $reservations = array_filter($reservations, function($r) use ($filtre) {
        return $r->getStatut() === $filtre;
    });
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réservations — Admin SportLoc</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin">
<nav class="navbar admin-nav">
    <div class="nav-logo">🛡️ SportLoc Admin</div>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="materiel.php">Matériel</a>
        <a href="categories.php">Catégories</a>
        <a href="reservations.php" class="active">Réservations</a>
        <a href="../public/deconnexion.php" class="btn-danger">Déconnexion</a>
    </div>
</nav>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Toutes les réservations</h1>
        <!-- Filtre par statut -->
        <div class="filter-bar">
            <a href="reservations.php"
               class="pill <?= !$filtre ? 'active' : '' ?>">Toutes</a>
            <a href="?statut=en+attente"
               class="pill <?= $filtre === 'en attente' ? 'active' : '' ?>">En attente</a>
            <a href="?statut=confirm%C3%A9e"
               class="pill <?= $filtre === 'confirmée' ? 'active' : '' ?>">Confirmées</a>
            <a href="?statut=annul%C3%A9e"
               class="pill <?= $filtre === 'annulée' ? 'active' : '' ?>">Annulées</a>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>
    <?php if ($erreur): ?>
        <div class="alert alert-error"><?= $erreur ?></div>
    <?php endif; ?>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Matériel</th>
                    <th>Du</th>
                    <th>Au</th>
                    <th>Durée</th>
                    <th>Prix total</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reservations)): ?>
                    <tr><td colspan="8" style="text-align:center">Aucune réservation.</td></tr>
                <?php endif; ?>
                <?php foreach ($reservations as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r->getUserNom()) ?></td>
                        <td><?= htmlspecialchars($r->getMaterielNom()) ?></td>
                        <td><?= $r->getDateDebut() ?></td>
                        <td><?= $r->getDateFin() ?></td>
                        <td><?= $r->getNbJours() ?>j</td>
                        <td><?= number_format($r->getPrixTotal(), 2) ?>€</td>
                        <td>
                            <!-- Formulaire inline pour changer le statut -->
                            <form method="POST" action="reservations.php" style="display:inline">
                                <input type="hidden" name="action" value="update_statut">
                                <input type="hidden" name="id" value="<?= $r->getId() ?>">
                                <select name="statut"
                                        onchange="this.form.submit()"
                                        class="select-statut statut-<?= str_replace(' ', '-', $r->getStatut()) ?>">
                                    <option value="en attente"
                                        <?= $r->getStatut() === 'en attente' ? 'selected' : '' ?>>
                                        En attente
                                    </option>
                                    <option value="confirmée"
                                        <?= $r->getStatut() === 'confirmée' ? 'selected' : '' ?>>
                                        Confirmée
                                    </option>
                                    <option value="annulée"
                                        <?= $r->getStatut() === 'annulée' ? 'selected' : '' ?>>
                                        Annulée
                                    </option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <a href="reservations.php?action=delete&id=<?= $r->getId() ?>"
                               class="btn-danger btn-sm"
                               onclick="return confirm('Supprimer cette réservation ?')">
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
