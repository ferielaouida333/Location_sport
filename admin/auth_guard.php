<?php
// ============================================================
// admin/auth_guard.php — Protection des pages admin
// Inclus en haut de chaque page admin
// COMMIT PAR : ZEINEB (Membre C)
//
// Ce fichier vérifie que l'utilisateur est connecté ET admin.
// Si non → redirige vers la page de connexion.
// ============================================================

// On vérifie que la session est démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Double vérification : connecté ET rôle admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../public/connexion.php');
    exit;
}
