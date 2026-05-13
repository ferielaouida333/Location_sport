<?php
// ============================================================
// admin/materiel.php — Gestion du matériel + upload photo
// COMMIT PAR : HOUSSEM (Membre B)
// ============================================================
require_once 'auth_guard.php';
require_once '../config.php';
require_once '../classes/Database.php';
require_once '../classes/Categorie.php';
require_once '../classes/CategorieDAO.php';
require_once '../classes/Materiel.php';
require_once '../classes/MaterielDAO.php';

$materielDAO  = new MaterielDAO();
$categorieDAO = new CategorieDAO();
$categories   = $categorieDAO->getAll();
$message      = '';
$erreur       = '';

// ============================================================
// TRAITEMENT DES ACTIONS
// $_GET['action'] contient ce qu'on veut faire : add, delete
// ============================================================

// ---- SUPPRESSION ----
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($materielDAO->delete($id)) {
        $message = 'Matériel supprimé.';
    } else {
        $erreur = 'Impossible de supprimer ce matériel.';
    }
}

// ---- AJOUT ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {

    $nom          = trim(htmlspecialchars($_POST['nom']          ?? ''));
    $description  = trim(htmlspecialchars($_POST['description']  ?? ''));
    $prix_jour    = (float)($_POST['prix_jour']    ?? 0);
    $disponible   = isset($_POST['disponible']) ? 1 : 0;
    $categorie_id = (int)($_POST['categorie_id']   ?? 0);
    $photo_nom    = 'default.jpg'; // valeur par défaut si pas d'upload

    // ---- GESTION DE L'UPLOAD ----
    // $_FILES['photo'] contient les infos du fichier uploadé :
    //   ['name']     = nom original du fichier
    //   ['tmp_name'] = chemin temporaire où PHP l'a mis
    //   ['size']     = taille en octets
    //   ['error']    = 0 si ok, autre valeur si problème
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {

        $fichier     = $_FILES['photo'];
        $taille_max  = 2 * 1024 * 1024; // 2 Mo en octets

        // pathinfo() analyse le nom de fichier et en extrait l'extension
        $extension   = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));

        // Extensions autorisées
        $exts_ok     = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($extension, $exts_ok)) {
            $erreur = 'Format invalide. Utilisez JPG, PNG ou WEBP.';

        } elseif ($fichier['size'] > $taille_max) {
            $erreur = 'Fichier trop lourd. Maximum 2 Mo.';

        } else {
            // uniqid() génère un nom unique pour éviter les conflits de nom
            // Ex: "6472abc8f3e12.jpg"
            $photo_nom    = uniqid() . '.' . $extension;
            $destination  = '../uploads/' . $photo_nom;

            // move_uploaded_file() déplace le fichier temporaire vers uploads/
            if (!move_uploaded_file($fichier['tmp_name'], $destination)) {
                $erreur   = 'Erreur lors de l\'upload. Vérifiez les permissions du dossier uploads/.';
                $photo_nom = 'default.jpg';
            }
        }
    }

    // Si pas d'erreur d'upload → on insère en BDD
    if (empty($erreur)) {
        if (empty($nom) || $prix_jour <= 0) {
            $erreur = 'Nom et prix sont obligatoires.';
        } else {
            $materiel = new Materiel(
                id:           0,
                nom:          $nom,
                description:  $description,
                prix_jour:    $prix_jour,
                photo:        $photo_nom,
                disponible:   $disponible,
                categorie_id: $categorie_id
            );

            if ($materielDAO->add($materiel)) {
                $message = 'Matériel ajouté avec succès !';
            } else {
                $erreur = 'Erreur lors de l\'ajout en base de données.';
            }
        }
    }
}

// Récupère la liste APRÈS les modifications pour afficher à jour
$materiels = $materielDAO->getAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Matériel — Admin SportLoc</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin">

<nav class="navbar admin-nav">
    <div class="nav-logo">🛡️ SportLoc Admin</div>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="materiel.php" class="active">Matériel</a>
        <a href="categories.php">Catégories</a>
        <a href="reservations.php">Réservations</a>
        <a href="../public/deconnexion.php" class="btn-danger">Déconnexion</a>
    </div>
</nav>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Gestion du matériel</h1>
        <button class="btn-primary" onclick="toggleForm()">+ Ajouter un matériel</button>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>
    <?php if ($erreur): ?>
        <div class="alert alert-error"><?= $erreur ?></div>
    <?php endif; ?>

    <!-- ======= FORMULAIRE D'AJOUT (caché par défaut) ======= -->
    <div id="add-form" style="display:none" class="card form-section">
        <h3>Ajouter un matériel</h3>

        <!-- enctype="multipart/form-data" EST OBLIGATOIRE pour les uploads -->
        <!-- Sans ça, $_FILES sera vide -->
        <form method="POST" action="materiel.php"
              enctype="multipart/form-data" novalidate>
            <input type="hidden" name="action" value="add">

            <div class="form-grid">
                <div class="form-group">
                    <label>Nom du matériel *</label>
                    <input type="text" name="nom" placeholder="ex: VTT Trek">
                    <div class="js-error" id="err-nom-admin"></div>
                </div>

                <div class="form-group">
                    <label>Catégorie</label>
                    <select name="categorie_id">
                        <option value="0">-- Choisir --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat->getId() ?>">
                                <?= htmlspecialchars($cat->getNom()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Prix / jour (€) *</label>
                    <input type="number" name="prix_jour"
                           min="0.01" step="0.01" placeholder="15.00">
                    <div class="js-error" id="err-prix-admin"></div>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="disponible" value="1" checked>
                        Disponible à la location
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3"
                          placeholder="Description du matériel..."></textarea>
            </div>

            <div class="form-group">
                <label>Photo (JPG/PNG/WEBP, max 2 Mo)</label>
                <!-- input type="file" = le champ upload -->
                <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp">
                <small class="hint">Laissez vide pour utiliser l'image par défaut.</small>
            </div>

            <button type="submit" class="btn-submit">Enregistrer</button>
            <button type="button" class="btn-outline" onclick="toggleForm()">Annuler</button>
        </form>
    </div>

    <!-- ======= TABLEAU DES MATÉRIELS ======= -->
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Nom</th>
                    <th>Catégorie</th>
                    <th>Prix/jour</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($materiels)): ?>
                    <tr><td colspan="6" style="text-align:center">Aucun matériel.</td></tr>
                <?php endif; ?>
                <?php foreach ($materiels as $m): ?>
                    <tr>
                        <td>
                            <img src="../uploads/<?= htmlspecialchars($m->getPhoto()) ?>"
                                 alt="photo"
                                 style="width:50px;height:40px;object-fit:cover;border-radius:4px"
                                 onerror="this.src='../uploads/default.jpg'">
                        </td>
                        <td><?= htmlspecialchars($m->getNom()) ?></td>
                        <td><?= htmlspecialchars($m->getCategorieId() ?: '—') ?></td>
                        <td><?= number_format($m->getPrixJour(), 2) ?>€</td>
                        <td>
                            <span class="badge <?= $m->isDisponible() ? 'badge-success' : 'badge-danger' ?>">
                                <?= $m->isDisponible() ? 'Disponible' : 'Indisponible' ?>
                            </span>
                        </td>
                        <td>
                            <!-- Lien de suppression avec confirmation JavaScript -->
                            <a href="materiel.php?action=delete&id=<?= $m->getId() ?>"
                               class="btn-danger btn-sm"
                               onclick="return confirm('Supprimer ce matériel ?')">
                                Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleForm() {
    const f = document.getElementById('add-form');
    f.style.display = f.style.display === 'none' ? 'block' : 'none';
}
</script>
<script src="../js/validation.js"></script>
</body>
</html>
