<?php
// ============================================================
// classes/ReservationDAO.php — Accès BDD pour Reservation
// COMMIT PAR : ZEINEB (Membre C)
// ============================================================

class ReservationDAO {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    // ----------------------------------------------------------
    // getAll() : toutes les réservations avec infos client + matériel
    // On fait deux JOIN pour avoir le nom du client et du matériel
    // ----------------------------------------------------------
    public function getAll(): array {
        $stmt = $this->pdo->query("
            SELECT r.*,
                   u.nom  AS user_nom,
                   m.nom  AS materiel_nom,
                   m.prix_jour
            FROM reservation r
            JOIN users    u ON r.user_id     = u.id
            JOIN materiel m ON r.materiel_id = m.id
            ORDER BY r.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Reservation');
    }

    // ----------------------------------------------------------
    // getByUser() : réservations d'un client spécifique
    // Utilisé sur la page "mon compte"
    // ----------------------------------------------------------
    public function getByUser(int $user_id): array {
        $stmt = $this->pdo->prepare("
            SELECT r.*,
                   u.nom  AS user_nom,
                   m.nom  AS materiel_nom,
                   m.prix_jour
            FROM reservation r
            JOIN users    u ON r.user_id     = u.id
            JOIN materiel m ON r.materiel_id = m.id
            WHERE r.user_id = ?
            ORDER BY r.date_debut DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Reservation');
    }

    // ----------------------------------------------------------
    // getById() : une réservation par son id
    // ----------------------------------------------------------
    public function getById(int $id): ?Reservation {
        $stmt = $this->pdo->prepare("
            SELECT r.*,
                   u.nom  AS user_nom,
                   m.nom  AS materiel_nom,
                   m.prix_jour
            FROM reservation r
            JOIN users    u ON r.user_id     = u.id
            JOIN materiel m ON r.materiel_id = m.id
            WHERE r.id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetchObject('Reservation');
        return $result ?: null;
    }

    // ----------------------------------------------------------
    // add() : crée une nouvelle réservation
    // ----------------------------------------------------------
    public function add(Reservation $r): bool {
        $stmt = $this->pdo->prepare("
            INSERT INTO reservation (user_id, materiel_id, date_debut, date_fin)
            VALUES (?, ?, ?, ?)
        ");
        try {
            return $stmt->execute([
                $r->getUserId(),
                $r->getMaterielId(),
                $r->getDateDebut(),
                $r->getDateFin()
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // ----------------------------------------------------------
    // updateStatut() : change le statut d'une réservation
    // Utilisé par l'admin pour confirmer ou annuler
    // ----------------------------------------------------------
    public function updateStatut(int $id, string $statut): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE reservation SET statut = ? WHERE id = ?"
        );
        return $stmt->execute([$statut, $id]);
    }

    // ----------------------------------------------------------
    // delete() : supprime une réservation
    // ----------------------------------------------------------
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM reservation WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // ----------------------------------------------------------
    // getStats() : données pour le tableau de bord admin
    // Retourne un tableau associatif avec les chiffres clés
    // ----------------------------------------------------------
    public function getStats(): array {
        // Nombre total de réservations confirmées
        $s1 = $this->pdo->query(
            "SELECT COUNT(*) AS total FROM reservation WHERE statut = 'confirmée'"
        )->fetch();

        // Nombre total de clients
        $s2 = $this->pdo->query(
            "SELECT COUNT(*) AS total FROM users WHERE role = 'client'"
        )->fetch();

        // Revenu total estimé (sum de prix_jour × nb jours)
        $s3 = $this->pdo->query("
            SELECT SUM(m.prix_jour * DATEDIFF(r.date_fin, r.date_debut)) AS revenu
            FROM reservation r
            JOIN materiel m ON r.materiel_id = m.id
            WHERE r.statut = 'confirmée'
        ")->fetch();

        // Réservations par catégorie (pour le graphique)
        $s4 = $this->pdo->query("
            SELECT c.nom AS categorie, COUNT(*) AS total
            FROM reservation r
            JOIN materiel m  ON r.materiel_id  = m.id
            JOIN categorie c ON m.categorie_id = c.id
            GROUP BY c.nom
            ORDER BY total DESC
        ")->fetchAll();

        return [
            'nb_reservations' => $s1['total'],
            'nb_clients'      => $s2['total'],
            'revenu_total'    => $s3['revenu'] ?? 0,
            'par_categorie'   => $s4
        ];
    }
}
