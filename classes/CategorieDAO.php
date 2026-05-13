<?php
// ============================================================
// classes/CategorieDAO.php — Accès BDD pour Categorie
// COMMIT PAR : FERIEL (Membre A)
//
// CONCEPT DAO (Data Access Object) :
// Cette classe est la SEULE à écrire des requêtes SQL pour
// la table "categorie". Les pages PHP appellent ses méthodes
// sans écrire de SQL elles-mêmes. C'est la séparation des
// responsabilités : chaque classe a un seul rôle.
//
// CONCEPT requêtes préparées (prepare/execute) :
// Au lieu d'écrire SELECT * WHERE id = $id (dangereux),
// on écrit SELECT * WHERE id = ? et on passe $id séparément.
// PDO s'occupe de sécuriser la valeur → protection injection SQL.
// ============================================================

class CategorieDAO {

    private PDO $pdo;

    public function __construct() {
        // On récupère la connexion PDO via le Singleton
        $this->pdo = Database::getInstance()->getPdo();
    }

    // ----------------------------------------------------------
    // getAll() : récupère toutes les catégories
    // Retourne un tableau d'objets Categorie
    // ----------------------------------------------------------
    public function getAll(): array {
        // query() = pas de variable dans la requête → pas besoin de prepare()
        $stmt = $this->pdo->query("SELECT * FROM categorie ORDER BY nom ASC");

        // FETCH_CLASS : PDO crée automatiquement un objet Categorie
        // pour chaque ligne de la base → "hydratation"
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Categorie');
    }

    // ----------------------------------------------------------
    // getById() : récupère une catégorie par son id
    // ----------------------------------------------------------
    public function getById(int $id): ?Categorie {
        $stmt = $this->pdo->prepare("SELECT * FROM categorie WHERE id = ?");
        $stmt->execute([$id]);

        // fetchObject() = hydrate UN SEUL objet Categorie
        // Retourne false si rien trouvé
        $result = $stmt->fetchObject('Categorie');
        return $result ?: null; // si false → retourne null
    }

    // ----------------------------------------------------------
    // add() : insère une nouvelle catégorie dans la BDD
    // Reçoit un objet Categorie, retourne true/false
    // ----------------------------------------------------------
    public function add(Categorie $c): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO categorie (nom, description) VALUES (?, ?)"
        );
        try {
            return $stmt->execute([$c->getNom(), $c->getDescription()]);
        } catch (PDOException $e) {
            // Si le nom est en double (UNIQUE), ça lance une exception
            return false;
        }
    }

    // ----------------------------------------------------------
    // update() : met à jour une catégorie existante
    // ----------------------------------------------------------
    public function update(Categorie $c): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE categorie SET nom = ?, description = ? WHERE id = ?"
        );
        try {
            return $stmt->execute([
                $c->getNom(),
                $c->getDescription(),
                $c->getId()   // le WHERE — identifie quelle ligne modifier
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // ----------------------------------------------------------
    // delete() : supprime une catégorie par son id
    // ----------------------------------------------------------
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM categorie WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
