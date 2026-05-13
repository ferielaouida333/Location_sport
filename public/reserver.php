<?php
// ============================================================
// public/reserver.php — Formulaire de réservation
// COMMIT PAR : ZEINEB (Membre C)
// ============================================================
session_start();
require_once '../config.php';
require_once '../classes/Database.php';
require_once '../classes/Materiel.php';
require_once '../classes/MaterielDAO.php';
require_once '../classes/Reservation.php';
require_once '../classes/ReservationDAO.php';

// PROTECTION : si non connecté → rediriger vers connexion
// C'est ce qu'on appelle une "garde de session"
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

$materielDAO    = new MaterielDAO();
$reservationDAO = new ReservationDAO();
$erreur         = '';
$succes         = '';

// Récupère l'id du matériel depuis l'URL (?id=3)
$materiel_id = (int)($_GET['id'] ?? 0);
$materiel    = $materielDAO->getById($materiel_id);

// Si l'id est invalide ou matériel introuvable → retour catalogue
if (!$materiel) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date_debut = $_POST['date_debut'] ?? '';
    $date_fin   = $_POST['date_fin']   ?? '';

    // Validation des dates
    if (empty($date_debut) || empty($date_fin)) {
        $erreur = 'Veuillez choisir les deux dates.';

    } elseif ($date_debut >= $date_fin) {
        $erreur = 'La date de fin doit être après la date de début.';

    } elseif ($date_debut < date('Y-m-d')) {
        $erreur = 'La date de début ne peut pas être dans le passé.';

    } else {
        // Crée l'objet Reservation et l'insère en BDD
        $reservation = new Reservation(
            id:          0,
            user_id:     $_SESSION['user_id'],
            materiel_id: $materiel->getId(),
            date_debut:  $date_debut,
            date_fin:    $date_fin
        );

        if ($reservationDAO->add($reservation)) {
            $succes = 'Réservation envoyée ! Elle sera confirmée bientôt.';
        } else {
            $erreur = 'Erreur lors de la réservation. Réessayez.';
        }
    }
}

// Calcul du nombre de jours (pour l'affichage)
$nb_jours   = 0;
$prix_total = 0;
if (!empty($_POST['date_debut']) && !empty($_POST['date_fin'])) {
    $d1 = new DateTime($_POST['date_debut']);
    $d2 = new DateTime($_POST['date_fin']);
    $nb_jours   = max(0, (int)$d1->diff($d2)->days);
    $prix_total = $nb_jours * $materiel->getPrixJour();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réserver — <?= htmlspecialchars($materiel->getNom()) ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar">
    <div class="nav-logo">🚲 SportLoc</div>
    <div class="nav-links">
        <a href="index.php">Catalogue</a>
        <a href="mon_compte.php">Mon compte</a>
        <a href="deconnexion.php" class="btn-outline">Déconnexion</a>
    </div>
</nav>

<div class="form-page">
    <div class="form-card">
        <h2>Réserver — <?= htmlspecialchars($materiel->getNom()) ?></h2>
        <p class="form-subtitle">
            <?= number_format($materiel->getPrixJour(), 2) ?>€ / jour
        </p>

        <?php if ($erreur): ?>
            <div class="alert alert-error"><?= $erreur ?></div>
        <?php endif; ?>
        <?php if ($succes): ?>
            <div class="alert alert-success"><?= $succes ?></div>
        <?php endif; ?>

        <form method="POST" action="reserver.php?id=<?= $materiel->getId() ?>"
              id="form-reservation" novalidate>

            <div class="form-group">
                <label for="date_debut">Date de début</label>
                <!-- min=today : empêche de choisir une date passée -->
                <input type="date" id="date_debut" name="date_debut"
                       min="<?= date('Y-m-d') ?>"
                       value="<?= htmlspecialchars($_POST['date_debut'] ?? '') ?>">
                <div class="js-error" id="err-debut"></div>
            </div>

            <div class="form-group">
                <label for="date_fin">Date de fin</label>
                <input type="date" id="date_fin" name="date_fin"
                       min="<?= date('Y-m-d') ?>"
                       value="<?= htmlspecialchars($_POST['date_fin'] ?? '') ?>">
                <div class="js-error" id="err-fin"></div>
            </div>

            <!-- Résumé du prix (mis à jour par JS) -->
            <div class="price-summary" id="price-summary" style="display:none">
                <span id="nb-jours">0</span> jour(s) ×
                <?= number_format($materiel->getPrixJour(), 2) ?>€ =
                <strong id="prix-total">0.00</strong>€
            </div>

            <button type="submit" class="btn-submit">Confirmer la réservation</button>
        </form>

        <p class="form-link"><a href="index.php">← Retour au catalogue</a></p>
    </div>
</div>

<script>
// Calcul dynamique du prix quand les dates changent
const prixJour = <?= $materiel->getPrixJour() ?>;

function calculerPrix() {
    const debut = document.getElementById('date_debut').value;
    const fin   = document.getElementById('date_fin').value;
    if (debut && fin && fin > debut) {
        const d1 = new Date(debut);
        const d2 = new Date(fin);
        const diff = Math.round((d2 - d1) / (1000 * 60 * 60 * 24));
        document.getElementById('nb-jours').textContent  = diff;
        document.getElementById('prix-total').textContent = (diff * prixJour).toFixed(2);
        document.getElementById('price-summary').style.display = 'block';
    }
}

document.getElementById('date_debut').addEventListener('change', calculerPrix);
document.getElementById('date_fin').addEventListener('change', calculerPrix);
</script>

<script src="../js/validation.js"></script>
</body>
</html>
