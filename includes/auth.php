<?php
// Session, contrôle d'accès et protection CSRF.
defined('LABRUTE') or exit;

function demarrerSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
}

/* ---------- État de l'utilisateur ---------- */

function estConnecte(): bool {
    return isset($_SESSION['joueur_id']);
}

function estAdmin(): bool {
    return estConnecte() && ($_SESSION['role'] ?? '') === 'admin';
}

function idJoueur(): int {
    return (int)($_SESSION['joueur_id'] ?? 0);
}

/* ---------- Connexion / déconnexion ---------- */

function ouvrirSession(array $joueur): void {
    session_regenerate_id(true);
    $_SESSION['joueur_id'] = (int)$joueur['id'];
    $_SESSION['login']     = $joueur['login'];
    $_SESSION['role']      = $joueur['role'];
}

// Vide la session puis en recrée une neuve (pour pouvoir afficher un message flash ensuite)
function fermerSession(): void {
    $_SESSION = [];
    session_regenerate_id(true);
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// Si un admin désactive un compte pendant que le joueur est connecté, on le déconnecte
function verifierCompteToujoursActif(PDO $pdo): void {
    if (!estConnecte()) {
        return;
    }
    $req = $pdo->prepare('SELECT actif FROM joueur WHERE id = :id');
    $req->execute([':id' => idJoueur()]);
    $actif = $req->fetchColumn();
    if ($actif === false || !(int)$actif) {
        fermerSession();
        rediriger('connexion.php', 'danger', 'Ton compte a été désactivé par un administrateur.');
    }
}

/* ---------- Protection des pages ---------- */

function exigerConnexion(): void {
    if (!estConnecte()) {
        // On mémorise la page demandée pour y revenir après la connexion
        $_SESSION['page_demandee'] = $_SERVER['REQUEST_URI'] ?? '';
        rediriger('connexion.php', 'warning', 'Cette page est réservée aux joueurs connectés. Connecte-toi pour y accéder.');
    }
}

function exigerAdmin(): void {
    exigerConnexion();
    if (!estAdmin()) {
        rediriger('index.php', 'danger', 'Accès refusé : cette page est réservée aux administrateurs.');
    }
}

function exigerPost(string $pageRetour): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        rediriger($pageRetour);
    }
}

/* ---------- Jeton CSRF (protège tous les formulaires POST) ---------- */

function champCsrf(): string {
    return '<input type="hidden" name="csrf" value="' . e($_SESSION['csrf']) . '">';
}

function verifierCsrfSiPost(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }
    $jeton = $_POST['csrf'] ?? '';
    if (!is_string($jeton) || !hash_equals($_SESSION['csrf'], $jeton)) {
        rediriger('index.php', 'danger', 'Formulaire expiré ou invalide. Merci de réessayer.');
    }
}
