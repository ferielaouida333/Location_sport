<?php
// ============================================================
// classes/Database.php — Connexion PDO (Patron Singleton)
// COMMIT PAR : FERIEL (Membre A)
//
// CONCEPT SINGLETON :
// Une seule connexion à la base est créée pendant toute
// l'exécution de la page. Si on demande une 2e connexion,
// on reçoit la même. Économise la mémoire et évite les bugs.
// ============================================================

class Database {

    // $instance garde EN MÉMOIRE l'unique objet Database créé.
    // "static" = appartient à la CLASSE, pas à un objet.
    // "?" = peut valoir null (au début, avant création).
    private static ?Database $instance = null;

    // $pdo est l'objet PHP qui parle vraiment à MySQL.
    private PDO $pdo;

    // Le constructeur est PRIVÉ : personne ne peut faire
    // "new Database()" depuis l'extérieur. On passe par getInstance().
    private function __construct() {

        // Le DSN (Data Source Name) = l'adresse de la BDD
        $dsn = "mysql:host=" . DB_HOST
             . ";dbname="    . DB_NAME
             . ";charset="   . DB_CHARSET;

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
                // Lance une exception si une requête échoue
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                // fetch() retourne un tableau associatif par défaut
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            // En cas d'erreur de connexion, on arrête tout
            die("Erreur connexion BDD : " . $e->getMessage());
        }
    }

    // La seule façon d'obtenir la connexion depuis l'extérieur.
    // Exemple d'appel : Database::getInstance()->getPdo()
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database(); // crée UNE SEULE FOIS
        }
        return self::$instance; // retourne toujours le même objet
    }

    // Donne l'objet PDO aux classes DAO qui en ont besoin
    public function getPdo(): PDO {
        return $this->pdo;
    }
}
