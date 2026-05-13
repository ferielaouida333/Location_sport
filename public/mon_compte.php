<?php
// ============================================================
// public/mon_compte.php — Espace personnel du client
// COMMIT PAR : ZEINEB (Membre C)
// ============================================================
session_start();
require_once '../config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/UserDAO.php';
require_once '../classes/Reservation.php';
require_once '../classes/ReservationDAO.php';

// Garde de session : seuls les connectés peuvent voir cette page
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

$reservationDAO = new ReservationDAO();
// Récupère uniquement les réservations de CET utilisateur
$reservations   = $reservationDAO->getByUser($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon compte — SportLoc</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar">
    <div class="nav-logo">🚲 SportLoc</div>
    <div class="nav-links">
        <a href="index.php">Catalogue</a>
        <a href="mon_compte.php" class="active">Mon compte</a>
        <a href="deconnexion.php" class="btn-outline">Déconnexion</a>
    </div>
</nav>

<div class="container">
    <!-- En-tête du compte -->
    <div class="account-header">
        <div class="avatar">
            <!-- Initiales du prénom -->
            <?= strtoupper(substr($_SESSION['user_nom'], 0, 2)) ?>
        </div>
        <div>
            <h2><?= htmlspecialchars($_SESSION['user_nom']) ?></h2>
            <p class="muted">Client SportLoc</p>
        </div>
    </div>

    <h3 class="section-title">Mes réservations</h3>

    <?php if (empty($reservations)): ?>
        <div class="empty-state">
            <p>Vous n'avez pas encore de réservation.</p>
            <a href="index.php" class="btn-primary">Voir le catalogue</a>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Matériel</th>
                        <th>Du</th>
                        <th>Au</th>
                        <th>Durée</th>
                        <th>Prix total</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r->getMaterielNom()) ?></td>
                            <td><?= $r->getDateDebut() ?></td>
                            <td><?= $r->getDateFin() ?></td>
                            <td><?= $r->getNbJours() ?> jour(s)</td>
                            <td><?= number_format($r->getPrixTotal(), 2) ?>€</td>
                            <td>
                                <!-- Classe CSS différente selon le statut -->
                                <span class="status-pill status-<?= str_replace(' ', '-', $r->getStatut()) ?>">
                                    <?= ucfirst($r->getStatut()) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<footer class="footer">
    <p>© 2025 SportLoc</p>
</footer>
</body>
</html>
