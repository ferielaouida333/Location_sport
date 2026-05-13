<?php
// ============================================================
// public/deconnexion.php — Déconnexion
// COMMIT PAR : ZEINEB (Membre C)
// ============================================================
session_start();

// Vide toutes les variables de session
session_unset();

// Détruit la session côté serveur
session_destroy();

// Redirige vers la page de connexion
header('Location: connexion.php');
exit;
