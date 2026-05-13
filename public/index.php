<?php
// ============================================================
// public/index.php — Page d'accueil publique
// Affiche le catalogue de matériel avec filtre par catégorie
// COMMIT PAR : FERIEL (Membre A)
// ============================================================

// session_start() doit être la TOUTE PREMIÈRE chose dans le fichier
// Elle active les sessions PHP : $_SESSION garde les infos
// d'un utilisateur connecté d'une page à l'autre
session_start();

// require_once = inclut le fichier UNE SEULE FOIS (évite les doublons)
// On charge d'abord la config (les constantes DB_HOST etc.)
// puis la classe Database, puis les classes entités et DAO
require_once '../config.php';
require_once '../classes/Database.php';
require_once '../classes/Categorie.php';
require_once '../classes/CategorieDAO.php';
require_once '../classes/Materiel.php';
require_once '../classes/MaterielDAO.php';

$categorieDAO = new CategorieDAO();
$materielDAO  = new MaterielDAO();

// Récupère toutes les catégories pour les boutons de filtre
$categories = $categorieDAO->getAll();

// Logique de filtrage :
// Si l'URL contient ?categorie=2 → on filtre
// Si l'URL contient ?q=velo → on cherche par mot
// Sinon → on affiche tout
$materiels = [];
$recherche  = '';
$cat_active = 0;

if (!empty($_GET['q'])) {
    // Nettoyage de l'entrée utilisateur avec htmlspecialchars
    // Transforme < > & en entités HTML → protège contre XSS
    $recherche = htmlspecialchars($_GET['q']);
    $materiels = $materielDAO->rechercher($recherche);

} elseif (!empty($_GET['categorie'])) {
    $cat_active = (int)$_GET['categorie']; // (int) = force le type entier → sécurité
    $materiels  = $materielDAO->getByCategorie($cat_active);

} else {
    $materiels = $materielDAO->getDisponibles();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportLoc — Location de matériel</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<!-- ======= NAVBAR ======= -->
<nav class="navbar">
    <div class="nav-logo">🚲 SportLoc</div>
    <div class="nav-links">
        <a href="index.php" class="active">Catalogue</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Si connecté : affiche le nom + lien compte -->
            <a href="mon_compte.php">👤 <?= htmlspecialchars($_SESSION['user_nom']) ?></a>
            <a href="deconnexion.php" class="btn-outline">Déconnexion</a>
        <?php else: ?>
            <a href="connexion.php">Connexion</a>
            <a href="inscription.php" class="btn-primary">S'inscrire</a>
        <?php endif; ?>
    </div>
</nav>

<!-- ======= HERO ======= -->
<div class="hero">
    <div class="hero-content">
        <h1>Louez votre matériel de sport</h1>
        <p>Vélos, raquettes, ski — Disponibles dès aujourd'hui</p>
        <!-- Formulaire de recherche : envoie vers la même page avec ?q=... -->
        <form method="GET" action="index.php" class="hero-search">
            <input type="text" name="q"
                   value="<?= $recherche ?>"
                   placeholder="Rechercher un matériel...">
            <button type="submit">Rechercher</button>
        </form>
    </div>
</div>

<!-- ======= FILTRES PAR CATÉGORIE ======= -->
<div class="container">
    <div class="cat-pills">
        <!-- Lien "Tout" — efface le filtre -->
        <a href="index.php" class="pill <?= $cat_active === 0 && $recherche === '' ? 'active' : '' ?>">
            Tout
        </a>
        <?php foreach ($categories as $cat): ?>
            <a href="?categorie=<?= $cat->getId() ?>"
               class="pill <?= $cat_active === $cat->getId() ? 'active' : '' ?>">
                <?= htmlspecialchars($cat->getNom()) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Message si recherche active -->
    <?php if ($recherche): ?>
        <p class="search-info">
            Résultats pour "<strong><?= $recherche ?></strong>"
            — <a href="index.php">Effacer la recherche</a>
        </p>
    <?php endif; ?>

    <!-- ======= GRILLE DE MATÉRIELS ======= -->
    <h2 class="section-title">
        <?= count($materiels) ?> matériel(s) disponible(s)
    </h2>

    <?php if (empty($materiels)): ?>
        <div class="empty-state">
            <p>Aucun matériel trouvé.</p>
            <a href="index.php">Voir tout le catalogue</a>
        </div>

    <?php else: ?>
        <div class="grid">
            <?php foreach ($materiels as $m): ?>
                <div class="card">
                    <!-- Photo uploadée ou image par défaut -->
                    <div class="card-img">
                        <img src="../uploads/<?= htmlspecialchars($m->getPhoto()) ?>"
                             alt="<?= htmlspecialchars($m->getNom()) ?>"
                             onerror="this.src='../uploads/default.jpg'">
                    </div>
                    <div class="card-body">
                        <h3 class="card-title"><?= htmlspecialchars($m->getNom()) ?></h3>
                        <p class="card-desc"><?= htmlspecialchars($m->getDescription()) ?></p>
                        <div class="card-price">
                            <?= number_format($m->getPrixJour(), 2) ?>€
                            <span>/ jour</span>
                        </div>
                        <span class="badge-available">✓ Disponible</span>
                        <!-- Si connecté → réserver, sinon → connexion -->
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="reserver.php?id=<?= $m->getId() ?>"
                               class="btn-card">Réserver</a>
                        <?php else: ?>
                            <a href="connexion.php" class="btn-card">
                                Connectez-vous pour réserver
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<footer class="footer">
    <p>© 2025 SportLoc — Projet PHP/PDO — FERIEL · HOUSSEM · ZEINEB</p>
</footer>

</body>
</html>
