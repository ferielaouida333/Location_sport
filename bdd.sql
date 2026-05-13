-- ============================================================
-- SPORTLOC — Script de création de la base de données
-- À importer dans phpMyAdmin
-- COMMIT PAR : FERIEL (Membre A)
-- ============================================================

CREATE DATABASE IF NOT EXISTS location_sport
  CHARACTER SET utf8
  COLLATE utf8_general_ci;

USE location_sport;

-- ------------------------------------------------------------
-- TABLE: categorie
-- Chaque matériel appartient à une catégorie (Vélos, Ski...)
-- ------------------------------------------------------------
CREATE TABLE categorie (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(100) NOT NULL UNIQUE,   -- pas deux catégories avec le même nom
    description TEXT
);

-- ------------------------------------------------------------
-- TABLE: materiel
-- Le matériel disponible à louer
-- categorie_id relie chaque matériel à sa catégorie
-- ------------------------------------------------------------
CREATE TABLE materiel (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    nom          VARCHAR(100)   NOT NULL,
    description  TEXT,
    prix_jour    DECIMAL(8,2)   NOT NULL,         -- ex: 15.50
    photo        VARCHAR(255)   DEFAULT 'default.jpg',
    disponible   TINYINT(1)     DEFAULT 1,        -- 1=oui 0=non
    categorie_id INT,
    FOREIGN KEY (categorie_id) REFERENCES categorie(id) ON DELETE SET NULL
);

-- ------------------------------------------------------------
-- TABLE: users
-- Clients et administrateurs du site
-- ------------------------------------------------------------
CREATE TABLE users (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    nom          VARCHAR(100)  NOT NULL,
    email        VARCHAR(150)  NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255)  NOT NULL,           -- toujours hashé avec password_hash()
    role         ENUM('admin','client') DEFAULT 'client',
    created_at   DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- TABLE: reservation
-- Une réservation = un client + un matériel + des dates
-- ------------------------------------------------------------
CREATE TABLE reservation (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT  NOT NULL,
    materiel_id  INT  NOT NULL,
    date_debut   DATE NOT NULL,
    date_fin     DATE NOT NULL,
    statut       ENUM('en attente','confirmée','annulée') DEFAULT 'en attente',
    created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)     REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (materiel_id) REFERENCES materiel(id) ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- DONNÉES DE TEST
-- ------------------------------------------------------------

-- Catégories
INSERT INTO categorie (nom, description) VALUES
('Vélos',     'Vélos de route, VTT, BMX'),
('Raquettes', 'Tennis, Badminton, Squash'),
('Ski',       'Skis, snowboards, équipements hiver');

-- Admin (mot de passe : admin123)
INSERT INTO users (nom, email, mot_de_passe, role) VALUES
('Admin SportLoc', 'admin@sport.com',
 '$2y$10$TKh8H1.PfbuNIAkulGl/v.5GliKMBRJpqEQCvhbZ2W1J.vB6BMMIS',
 'admin');

-- Client test (mot de passe : client123)
INSERT INTO users (nom, email, mot_de_passe, role) VALUES
('Jean Dupont', 'jean@email.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uXutHOwOu',
 'client');

-- Matériels
INSERT INTO materiel (nom, description, prix_jour, photo, disponible, categorie_id) VALUES
('VTT Trek',         'Vélo tout terrain robuste, taille M', 15.00, 'default.jpg', 1, 1),
('Vélo de route',    'Vélo léger pour route et piste',      12.00, 'default.jpg', 1, 1),
('Raquette Babolat', 'Raquette de tennis professionelle',    8.00, 'default.jpg', 1, 2),
('Skis Rossignol',   'Skis adulte 170cm, fixations incluses',35.00,'default.jpg', 1, 3);

-- Réservations test
INSERT INTO reservation (user_id, materiel_id, date_debut, date_fin, statut) VALUES
(2, 1, '2025-06-01', '2025-06-03', 'confirmée'),
(2, 3, '2025-06-10', '2025-06-11', 'en attente');
