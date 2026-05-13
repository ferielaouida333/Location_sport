<?php
// ============================================================
// public/inscription.php — Formulaire d'inscription
// COMMIT PAR : ZEINEB (Membre C)
// ============================================================
session_start();
require_once '../config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/UserDAO.php';

// Si déjà connecté → rediriger vers le catalogue
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit; // IMPORTANT : toujours mettre exit après header()
}

$erreur  = '';
$succes  = '';

// Ce bloc ne s'exécute que si le formulaire a été soumis (méthode POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Nettoyage des données reçues
    // trim() supprime les espaces avant/après
    // htmlspecialchars() protège contre les injections HTML
    $nom   = trim(htmlspecialchars($_POST['nom']   ?? ''));
    $email = trim(htmlspecialchars($_POST['email'] ?? ''));
    $mdp   = $_POST['mot_de_passe'] ?? ''; // ne pas htmlspecialchars le mot de passe

    // ---- VALIDATION CÔTÉ SERVEUR ----
    // La validation JS peut être contournée → on revalide en PHP aussi
    if (empty($nom)) {
        $erreur = 'Le nom est obligatoire.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // filter_var avec FILTER_VALIDATE_EMAIL vérifie le format email
        $erreur = 'Adresse email invalide.';

    } elseif (strlen($mdp) < 6) {
        $erreur = 'Le mot de passe doit faire au moins 6 caractères.';

    } else {
        // Tout est valide → on essaie d'insérer en BDD
        $dao = new UserDAO();
        if ($dao->inscrire($nom, $email, $mdp)) {
            $succes = 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.';
        } else {
            $erreur = 'Cette adresse email est déjà utilisée.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription — SportLoc</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar">
    <div class="nav-logo">🚲 SportLoc</div>
    <div class="nav-links">
        <a href="index.php">Catalogue</a>
        <a href="connexion.php">Connexion</a>
    </div>
</nav>

<div class="form-page">
    <div class="form-card">
        <h2>Créer un compte</h2>
        <p class="form-subtitle">Rejoignez SportLoc et commencez à réserver</p>

        <!-- Messages d'erreur / succès PHP -->
        <?php if ($erreur): ?>
            <div class="alert alert-error"><?= $erreur ?></div>
        <?php endif; ?>
        <?php if ($succes): ?>
            <div class="alert alert-success"><?= $succes ?></div>
        <?php endif; ?>

        <!-- novalidate : on désactive la validation native HTML
             pour que notre JS prenne le relai -->
        <form method="POST" action="inscription.php"
              id="form-inscription" novalidate>

            <div class="form-group">
                <label for="nom">Nom complet</label>
                <input type="text" id="nom" name="nom"
                       placeholder="Jean Dupont"
                       value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                <!-- div vide : le JS y écrit les messages d'erreur -->
                <div class="js-error" id="err-nom"></div>
            </div>

            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email"
                       placeholder="vous@email.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                <div class="js-error" id="err-email"></div>
            </div>

            <div class="form-group">
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe"
                       placeholder="Minimum 6 caractères">
                <div class="js-error" id="err-mdp"></div>
            </div>

            <button type="submit" class="btn-submit">Créer mon compte</button>
        </form>

        <p class="form-link">
            Déjà un compte ? <a href="connexion.php">Se connecter</a>
        </p>
    </div>
</div>

<!-- Le fichier JS est chargé EN FIN DE PAGE
     pour que le HTML soit déjà chargé quand JS s'exécute -->
<script src="../js/validation.js"></script>
</body>
</html>
