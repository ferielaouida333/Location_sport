<?php
// ============================================================
// public/connexion.php — Page de connexion
// COMMIT PAR : ZEINEB (Membre C)
// ============================================================
session_start();
require_once '../config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/UserDAO.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']        ?? '');
    $mdp   =      $_POST['mot_de_passe'] ?? '';

    if (empty($email) || empty($mdp)) {
        $erreur = 'Veuillez remplir tous les champs.';
    } else {
        $dao  = new UserDAO();
        $user = $dao->connecter($email, $mdp);

        if ($user === null) {
            $erreur = 'Email ou mot de passe incorrect.';
        } else {
            // CONNEXION RÉUSSIE
            // On stocke les infos dans la session
            // $_SESSION survit d'une page à l'autre tant que le navigateur est ouvert
            $_SESSION['user_id']   = $user->getId();
            $_SESSION['user_nom']  = $user->getNom();
            $_SESSION['user_role'] = $user->getRole();

            // Redirection selon le rôle
            if ($user->isAdmin()) {
                header('Location: ../admin/dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion — SportLoc</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar">
    <div class="nav-logo">🚲 SportLoc</div>
    <div class="nav-links">
        <a href="index.php">Catalogue</a>
        <a href="inscription.php" class="btn-primary">S'inscrire</a>
    </div>
</nav>

<div class="form-page">
    <div class="form-card">
        <h2>Connexion</h2>
        <p class="form-subtitle">Accédez à votre espace personnel</p>

        <?php if ($erreur): ?>
            <div class="alert alert-error"><?= $erreur ?></div>
        <?php endif; ?>

        <form method="POST" action="connexion.php" id="form-connexion" novalidate>
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
                       placeholder="••••••••">
                <div class="js-error" id="err-mdp"></div>
            </div>

            <button type="submit" class="btn-submit">Se connecter</button>
        </form>

        <p class="form-link">
            Pas encore de compte ? <a href="inscription.php">S'inscrire</a>
        </p>

        <!-- Compte de test pour la démo -->
        <div class="demo-box">
            <strong>Comptes de test :</strong><br>
            Admin : admin@sport.com / admin123<br>
            Client : jean@email.com / password
        </div>
    </div>
</div>

<script src="../js/validation.js"></script>
</body>
</html>
