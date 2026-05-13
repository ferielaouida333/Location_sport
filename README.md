# 🚲 SportLoc — Location de matériel sportif

Projet PHP/PDO orienté objet — BUT Informatique 2025  
**Équipe :** FERIEL · HOUSSEM · ZEINEB

---

## ⚙️ Installation (XAMPP)

### 1. Copier le projet
Copier ce dossier dans `C:\xampp\htdocs\location_sport\`

### 2. Base de données
1. Ouvrir XAMPP → démarrer Apache + MySQL
2. Aller sur `http://localhost/phpmyadmin`
3. Importer le fichier `bdd.sql`

### 3. Configuration
Copier `config.example.php` → `config.php`  
(ou modifier `config.php` directement avec vos identifiants)

### 4. Accéder au site
- **Site public :** `http://localhost/location_sport/public/index.php`
- **Admin :** `http://localhost/location_sport/admin/dashboard.php`

---

## 🔑 Comptes de test

| Rôle   | Email              | Mot de passe |
|--------|--------------------|--------------|
| Admin  | admin@sport.com    | admin123     |
| Client | jean@email.com     | password     |

---

## 📁 Structure

```
location_sport/
├── classes/         ← Toutes les classes PHP (entités + DAO)
├── public/          ← Pages visiteurs
├── admin/           ← Pages administration
├── uploads/         ← Photos uploadées
├── css/style.css    ← Styles
├── js/validation.js ← Validations JS
├── config.php       ← Identifiants BDD (ne pas commiter !)
└── bdd.sql          ← Script de création de la base
```

---

## 👥 Répartition du travail

| Membre  | Responsabilité |
|---------|----------------|
| FERIEL  | Database, Categorie + CategorieDAO, index.php, admin/categories.php, CSS |
| HOUSSEM | Materiel + MaterielDAO, admin/materiel.php (upload), dashboard.php, JS |
| ZEINEB  | User + UserDAO, Reservation + ReservationDAO, auth, pages compte, admin/reservations.php |
