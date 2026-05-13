<?php
// ============================================================
// admin/dashboard.php — Tableau de bord administrateur
// COMMIT PAR : HOUSSEM (Membre B)
// ============================================================
require_once 'auth_guard.php';
require_once '../config.php';
require_once '../classes/Database.php';
require_once '../classes/Reservation.php';
require_once '../classes/ReservationDAO.php';
require_once '../classes/Materiel.php';
require_once '../classes/MaterielDAO.php';

$reservationDAO = new ReservationDAO();
// getStats() retourne un tableau avec tous les chiffres
$stats = $reservationDAO->getStats();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard — Admin SportLoc</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin">

<nav class="navbar admin-nav">
    <div class="nav-logo">🛡️ SportLoc Admin</div>
    <div class="nav-links">
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="materiel.php">Matériel</a>
        <a href="categories.php">Catégories</a>
        <a href="reservations.php">Réservations</a>
        <a href="../public/index.php" class="btn-outline">→ Site public</a>
        <a href="../public/deconnexion.php" class="btn-danger">Déconnexion</a>
    </div>
</nav>

<div class="container">
    <h1 class="page-title">Tableau de bord</h1>

    <!-- ======= STATISTIQUES ======= -->
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-label">👥 Clients</div>
            <div class="stat-val"><?= $stats['nb_clients'] ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">📅 Réservations confirmées</div>
            <div class="stat-val"><?= $stats['nb_reservations'] ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">💶 Revenu total</div>
            <div class="stat-val"><?= number_format($stats['revenu_total'], 2) ?>€</div>
        </div>
    </div>

    <!-- ======= GRAPHIQUE PAR CATÉGORIE ======= -->
    <div class="card" style="margin-top: 24px; padding: 24px;">
        <h3 class="section-title">Réservations par catégorie</h3>

        <?php
        // Trouve le max pour calculer les pourcentages des barres
        $max = 1;
        foreach ($stats['par_categorie'] as $ligne) {
            if ($ligne['total'] > $max) $max = $ligne['total'];
        }
        ?>

        <?php foreach ($stats['par_categorie'] as $ligne): ?>
            <div class="bar-row">
                <div class="bar-label"><?= htmlspecialchars($ligne['categorie']) ?></div>
                <div class="bar-track">
                    <!-- Largeur proportionnelle au maximum -->
                    <div class="bar-fill"
                         style="width: <?= round(($ligne['total'] / $max) * 100) ?>%">
                    </div>
                </div>
                <div class="bar-val"><?= $ligne['total'] ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
