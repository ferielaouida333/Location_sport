<?php
// ============================================================
// classes/Materiel.php — Entité Matériel
// COMMIT PAR : HOUSSEM (Membre B)
//
// Un objet Materiel représente une ligne de la table "materiel".
// Propriétés privées, accessibles uniquement via getters/setters.
// ============================================================

class Materiel {

    public function __construct(
        private int    $id           = 0,
        private string $nom          = '',
        private string $description  = '',
        private float  $prix_jour    = 0.0,
        private string $photo        = 'default.jpg',
        private int    $disponible   = 1,   // 1=disponible, 0=non disponible
        private int    $categorie_id = 0
    ) {}

    // ---- GETTERS ----

    public function getId(): int          { return $this->id; }
    public function getNom(): string      { return $this->nom; }
    public function getDescription(): string { return $this->description; }
    public function getPrixJour(): float  { return $this->prix_jour; }
    public function getPhoto(): string    { return $this->photo; }
    public function isDisponible(): int   { return $this->disponible; }
    public function getCategorieId(): int { return $this->categorie_id; }

    // ---- SETTERS ----

    public function setNom(string $nom): void              { $this->nom = $nom; }
    public function setDescription(string $d): void        { $this->description = $d; }
    public function setPrixJour(float $p): void            { $this->prix_jour = $p; }
    public function setPhoto(string $photo): void          { $this->photo = $photo; }
    public function setDisponible(int $d): void            { $this->disponible = $d; }
    public function setCategorieId(int $id): void          { $this->categorie_id = $id; }
}
