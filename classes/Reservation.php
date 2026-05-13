<?php
// ============================================================
// classes/Reservation.php — Entité Réservation
// COMMIT PAR : ZEINEB (Membre C)
// ============================================================

class Reservation {

    public function __construct(
        private int    $id           = 0,
        private int    $user_id      = 0,
        private int    $materiel_id  = 0,
        private string $date_debut   = '',
        private string $date_fin     = '',
        private string $statut       = 'en attente',
        private string $created_at   = '',
        // Ces propriétés viennent du JOIN dans ReservationDAO
        // Elles ne sont PAS dans la table reservation,
        // mais PDO les injecte grâce au SELECT
        private string $user_nom     = '',
        private string $materiel_nom = '',
        private float  $prix_jour    = 0.0
    ) {}

    // ---- GETTERS ----

    public function getId(): int            { return $this->id; }
    public function getUserId(): int        { return $this->user_id; }
    public function getMaterielId(): int    { return $this->materiel_id; }
    public function getDateDebut(): string  { return $this->date_debut; }
    public function getDateFin(): string    { return $this->date_fin; }
    public function getStatut(): string     { return $this->statut; }
    public function getCreatedAt(): string  { return $this->created_at; }
    public function getUserNom(): string    { return $this->user_nom; }
    public function getMaterielNom(): string{ return $this->materiel_nom; }
    public function getPrixJour(): float    { return $this->prix_jour; }

    // ---- SETTERS ----

    public function setStatut(string $statut): void { $this->statut = $statut; }

    // Calcule le nombre de jours de location
    public function getNbJours(): int {
        $debut = new DateTime($this->date_debut);
        $fin   = new DateTime($this->date_fin);
        // diff() retourne un objet DateInterval, ->days = nb de jours
        return (int)$debut->diff($fin)->days;
    }

    // Calcule le prix total = nb jours × prix par jour
    public function getPrixTotal(): float {
        return $this->getNbJours() * $this->prix_jour;
    }
}
