# La Brute – Arène de combats automatiques

Projet *Conception de sites web* – ESIGELEC, S7 2026 – Hachaichi & Annabi.

## Installation locale (XAMPP)
1. Placer le dossier `labrute` dans `htdocs`.
2. Dans phpMyAdmin, créer la base `labrute` puis importer **dans l'ordre** :
   `bdd/creation.sql` (tables) puis `bdd/donnees_test.sql` (jeu de démonstration).
3. Copier `includes/config.example.php` en `includes/config.local.php` et remplir les identifiants
   (local et serveur). Ce fichier n'est pas versionné.
4. Ouvrir http://localhost/labrute/ — les comptes de test sont dans `docs/identifiants.txt`.

## Déploiement sur moduleweb.fr
- Importer les deux scripts SQL dans la base `bdd_6_3` via phpMyAdmin.
- Envoyer tout le dossier par FileZilla/WinSCP **y compris** `includes/config.local.php`,
  **sauf** `docs/` (contient les mots de passe de test).

## Organisation du code
```
labrute/
├── *.php                  Pages du site (une page = un fichier, voir l'arborescence du dossier de conception)
├── question.php           Traitement des questions/réponses (bonus)
├── choisir_adversaire.php Traitement du choix d'adversaire + combat (POST, transaction)
├── admin/                 Pages réservées au rôle admin (bonus) : index, defis, joueurs
├── includes/
│   ├── init.php           À inclure en 1re ligne de chaque page : charge tout le reste
│   ├── config.php         Connexion PDO (lit config.local.php)
│   ├── auth.php           Session, exigerConnexion(), exigerAdmin(), jeton CSRF
│   ├── fonctions.php      e(), url(), rediriger(), dateFr(), badges…
│   ├── requetes.php       Requêtes SQL partagées (maBrute, statsBrute, listeDefis…)
│   ├── combat_fonctions.php  simulerCombat()
│   ├── header.php / footer.php  Gabarit commun
│   └── composants/        Morceaux de HTML réutilisés (fiche_brute, choix_avatar, carte_defi…)
├── css/style.css          Feuille de style unique (thème sombre sur Bootstrap 5.3)
├── js/site.js             Confirmation avant les actions sensibles
├── img/                   Logo et avatars
├── bdd/                   creation.sql + donnees_test.sql
└── docs/                  identifiants.txt (ne pas déployer)
```

## Modèle d'une page
```php
<?php
require __DIR__ . '/includes/init.php';   // session + BDD + fonctions + vérification CSRF
exigerConnexion();                         // si la page est réservée aux joueurs

// … traitement (requêtes préparées, puis rediriger('page.php', 'success', 'Message') après un POST) …

$titre_page = 'Titre';
include __DIR__ . '/includes/header.php';
?>
<!-- HTML : tout affichage venant de la BDD passe par e() ; chaque formulaire POST contient champCsrf() -->
<?php include __DIR__ . '/includes/footer.php'; ?>
```

## Sécurité mise en œuvre
- Requêtes préparées PDO partout (injection SQL), `e()` sur tout affichage (XSS).
- Mots de passe hachés en bcrypt (`password_hash` / `password_verify`).
- Jeton CSRF vérifié automatiquement sur **tous** les POST (`init.php`).
- Pages protégées : redirection avec message vers la connexion, puis retour à la page demandée.
- Un compte désactivé par l'admin est déconnecté immédiatement.
- Choix de l'adversaire : POST + transaction + `UPDATE … WHERE etat = 'ouvert'` → aucun double combat.
