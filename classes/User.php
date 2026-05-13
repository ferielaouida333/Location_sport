<?php
// ============================================================
// classes/User.php — Entité Utilisateur
// COMMIT PAR : ZEINEB (Membre C)
// ============================================================

class User {

    public function __construct(
        private int    $id           = 0,
        private string $nom          = '',
        private string $email        = '',
        private string $mot_de_passe = '',  // stocké hashé, jamais en clair
        private string $role         = 'client',
        private string $created_at   = ''
    ) {}

    // ---- GETTERS ----

    public function getId(): int            { return $this->id; }
    public function getNom(): string        { return $this->nom; }
    public function getEmail(): string      { return $this->email; }
    public function getMotDePasse(): string { return $this->mot_de_passe; }
    public function getRole(): string       { return $this->role; }
    public function getCreatedAt(): string  { return $this->created_at; }

    // ---- SETTERS ----

    public function setNom(string $nom): void              { $this->nom = $nom; }
    public function setEmail(string $email): void          { $this->email = $email; }
    public function setMotDePasse(string $mdp): void       { $this->mot_de_passe = $mdp; }
    public function setRole(string $role): void            { $this->role = $role; }

    // Vérifie si cet utilisateur est admin
    public function isAdmin(): bool {
        return $this->role === 'admin';
    }
}
