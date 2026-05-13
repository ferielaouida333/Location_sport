<?php
// ============================================================
// classes/UserDAO.php — Accès BDD pour User
// COMMIT PAR : ZEINEB (Membre C)
//
// SÉCURITÉ :
// - Les mots de passe sont TOUJOURS hashés avec password_hash()
//   avant d'être stockés en base.
// - password_verify() compare le mot de passe saisi avec le hash.
// - On ne stocke JAMAIS le mot de passe en clair.
// ============================================================

class UserDAO {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    // ----------------------------------------------------------
    // inscrire() : crée un nouveau compte client
    // Retourne true si succès, false si email déjà utilisé
    // ----------------------------------------------------------
    public function inscrire(string $nom, string $email, string $mdp): bool {
        // password_hash() transforme "monmotdepasse" en quelque chose
        // comme "$2y$10$XyZ..." que personne ne peut deviner
        $hash = password_hash($mdp, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare(
            "INSERT INTO users (nom, email, mot_de_passe) VALUES (?, ?, ?)"
        );
        try {
            return $stmt->execute([$nom, $email, $hash]);
        } catch (PDOException $e) {
            // L'email est UNIQUE en BDD → exception si déjà pris
            return false;
        }
    }

    // ----------------------------------------------------------
    // connecter() : vérifie email + mot de passe
    // Retourne l'objet User si OK, null sinon
    // ----------------------------------------------------------
    public function connecter(string $email, string $mdp): ?User {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);

        // On récupère un tableau associatif (pas encore un objet User)
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null; // email introuvable
        }

        // password_verify() compare le mot de passe saisi ($mdp)
        // avec le hash stocké en BDD ($data['mot_de_passe'])
        if (!password_verify($mdp, $data['mot_de_passe'])) {
            return null; // mot de passe incorrect
        }

        // On construit et retourne un objet User
        return new User(
            $data['id'],
            $data['nom'],
            $data['email'],
            $data['mot_de_passe'],
            $data['role'],
            $data['created_at']
        );
    }

    // ----------------------------------------------------------
    // getAll() : tous les utilisateurs (pour l'admin)
    // ----------------------------------------------------------
    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'User');
    }

    // ----------------------------------------------------------
    // getById() : un utilisateur par son id
    // ----------------------------------------------------------
    public function getById(int $id): ?User {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetchObject('User');
        return $result ?: null;
    }

    // ----------------------------------------------------------
    // delete() : supprime un compte
    // ----------------------------------------------------------
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
