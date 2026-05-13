// ============================================================
// js/validation.js — Validations JavaScript côté client
// COMMIT PAR : HOUSSEM (Membre B)
//
// CONCEPT :
// Ce fichier s'exécute dans le NAVIGATEUR (pas sur le serveur).
// Il vérifie les formulaires AVANT qu'ils soient envoyés.
// Si une erreur est trouvée → on bloque l'envoi et on affiche
// un message rouge sous le champ concerné.
//
// IMPORTANT : La validation JS seule ne suffit pas !
// Un utilisateur peut désactiver JS. C'est pourquoi on valide
// aussi côté PHP dans chaque page (double protection).
// ============================================================

// ============================================================
// UTILITAIRES
// ============================================================

// Affiche un message d'erreur rouge sous un champ
// id = l'id du div .js-error  |  msg = le texte à afficher
function afficherErreur(id, msg) {
    const el = document.getElementById(id);
    if (el) el.textContent = msg;
}

// Efface un message d'erreur
function cacherErreur(id) {
    const el = document.getElementById(id);
    if (el) el.textContent = '';
}

// Regex pour valider un email : quelquechose@domaine.extension
// ^ = début de chaîne  |  $ = fin de chaîne
// [^\s@]+ = un ou plusieurs caractères qui ne sont pas espace ou @
const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

// ============================================================
// FORMULAIRE D'INSCRIPTION (#form-inscription)
// ============================================================
const formInscription = document.getElementById('form-inscription');

if (formInscription) {
    formInscription.addEventListener('submit', function(e) {
        // On part du principe que tout est valide
        let valide = true;

        // ---- Vérification du nom ----
        const nom = document.getElementById('nom');
        if (!nom || nom.value.trim() === '') {
            afficherErreur('err-nom', 'Le nom est obligatoire.');
            valide = false;
        } else {
            cacherErreur('err-nom');
        }

        // ---- Vérification de l'email ----
        const email = document.getElementById('email');
        if (!email || !regexEmail.test(email.value.trim())) {
            afficherErreur('err-email', 'Adresse email invalide.');
            valide = false;
        } else {
            cacherErreur('err-email');
        }

        // ---- Vérification du mot de passe ----
        const mdp = document.getElementById('mot_de_passe');
        if (!mdp || mdp.value.length < 6) {
            afficherErreur('err-mdp', 'Minimum 6 caractères.');
            valide = false;
        } else {
            cacherErreur('err-mdp');
        }

        // Si une validation a échoué → bloque l'envoi du formulaire
        // preventDefault() empêche le navigateur d'envoyer le formulaire
        if (!valide) {
            e.preventDefault();
        }
    });
}

// ============================================================
// FORMULAIRE DE CONNEXION (#form-connexion)
// ============================================================
const formConnexion = document.getElementById('form-connexion');

if (formConnexion) {
    formConnexion.addEventListener('submit', function(e) {
        let valide = true;

        const email = document.getElementById('email');
        if (!email || !regexEmail.test(email.value.trim())) {
            afficherErreur('err-email', 'Adresse email invalide.');
            valide = false;
        } else {
            cacherErreur('err-email');
        }

        const mdp = document.getElementById('mot_de_passe');
        if (!mdp || mdp.value.trim() === '') {
            afficherErreur('err-mdp', 'Mot de passe requis.');
            valide = false;
        } else {
            cacherErreur('err-mdp');
        }

        if (!valide) e.preventDefault();
    });
}

// ============================================================
// FORMULAIRE DE RÉSERVATION (#form-reservation)
// ============================================================
const formReservation = document.getElementById('form-reservation');

if (formReservation) {
    formReservation.addEventListener('submit', function(e) {
        let valide = true;

        const debut = document.getElementById('date_debut');
        const fin   = document.getElementById('date_fin');
        const auj   = new Date().toISOString().split('T')[0]; // format YYYY-MM-DD

        if (!debut || debut.value === '') {
            afficherErreur('err-debut', 'La date de début est obligatoire.');
            valide = false;
        } else if (debut.value < auj) {
            afficherErreur('err-debut', 'La date ne peut pas être dans le passé.');
            valide = false;
        } else {
            cacherErreur('err-debut');
        }

        if (!fin || fin.value === '') {
            afficherErreur('err-fin', 'La date de fin est obligatoire.');
            valide = false;
        } else if (fin.value <= debut.value) {
            // La fin doit être STRICTEMENT après le début
            afficherErreur('err-fin', 'La date de fin doit être après le début.');
            valide = false;
        } else {
            cacherErreur('err-fin');
        }

        if (!valide) e.preventDefault();
    });
}

// ============================================================
// FORMULAIRE ADMIN MATÉRIEL
// ============================================================
const formAdminMateriel = document.querySelector('#add-form form');

if (formAdminMateriel) {
    formAdminMateriel.addEventListener('submit', function(e) {
        let valide = true;

        const nom = formAdminMateriel.querySelector('[name="nom"]');
        if (!nom || nom.value.trim() === '') {
            afficherErreur('err-nom-admin', 'Le nom est obligatoire.');
            valide = false;
        } else {
            cacherErreur('err-nom-admin');
        }

        const prix = formAdminMateriel.querySelector('[name="prix_jour"]');
        if (!prix || parseFloat(prix.value) <= 0 || isNaN(parseFloat(prix.value))) {
            afficherErreur('err-prix-admin', 'Prix invalide (doit être > 0).');
            valide = false;
        } else {
            cacherErreur('err-prix-admin');
        }

        if (!valide) e.preventDefault();
    });
}
