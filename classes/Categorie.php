<?php
// ============================================================
// classes/Categorie.php — Entité Catégorie
// COMMIT PAR : FERIEL (Membre A)
//
// CONCEPT ENTITÉ :
// Cette classe représente UNE ligne de la table "categorie".
// Un objet Categorie = une catégorie (ex: Vélos).
// Les propriétés sont PRIVÉES → on les lit/modifie via
// getters et setters uniquement.
// ============================================================

class Categorie {

    // PHP 8 : Constructor Promotion
    // Écrire "private int $id" DANS les paramètres du constructeur
    // crée automatiquement la propriété ET lui assigne la valeur.
    // C'est plus court que de déclarer chaque propriété séparément.
    public function __construct(
        private int    $id          = 0,   // 0 = pas encore en BDD
        private string $nom         = '',
        private string $description = ''
    ) {}
    // Le corps {} est vide car tout est géré au-dessus

    // ---- GETTERS : lire les valeurs ----

    public function getId(): int {
        return $this->id;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getDescription(): string {
        return $this->description;
    }

    // ---- SETTERS : modifier les valeurs ----

    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    public function setDescription(string $desc): void {
        $this->description = $desc;
    }
}
