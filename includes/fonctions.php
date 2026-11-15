<?php
// Fonctions utilitaires générales (sans accès à la base de données).
defined('LABRUTE') or exit;

/* ---------- Affichage sécurisé ---------- */

// Échappe une valeur avant affichage (protection XSS). À utiliser sur TOUT affichage venant de la BDD.
function e($texte): string {
    return htmlspecialchars((string)$texte, ENT_QUOTES, 'UTF-8');
}

// Lien vers une page du site, valable depuis la racine comme depuis admin/
function url(string $page): string {
    return RACINE . $page;
}

// Chemin de l'image d'un avatar
function urlAvatar(string $fichier): string {
    return url('img/avatars/' . rawurlencode($fichier));
}

// Lien de la barre de navigation (classe "active" sur la page courante)
function lienNav(string $page, string $libelle, bool $actif = false): string {
    $attributs = $actif ? ' active" aria-current="page' : '';
    return '<a class="nav-link' . $attributs . '" href="' . e(url($page)) . '">' . e($libelle) . '</a>';
}

// Affiche un composant réutilisable (includes/composants/NOM.php) avec ses variables
function composant(string $nom, array $variables = []): void {
    extract($variables, EXTR_SKIP);
    include __DIR__ . '/composants/' . $nom . '.php';
}

/* ---------- Redirections et messages flash ---------- */

// Message affiché une seule fois sur la page suivante (voir header.php)
function flash(string $type, string $message): void {
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

// Redirige vers une page du site (avec un message flash optionnel) puis arrête le script
function rediriger(string $page, string $type = '', string $message = ''): void {
    if ($message !== '') {
        flash($type ?: 'info', $message);
    }
    header('Location: ' . url($page));
    exit;
}

/* ---------- Lecture des paramètres ---------- */

// Entier strictement positif lu dans $_GET ou $_POST (null sinon)
function idDepuis(string $source, string $nom): ?int {
    $type = $source === 'post' ? INPUT_POST : INPUT_GET;
    $id = filter_input($type, $nom, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $id ?: null;
}

function texteDepuisPost(string $nom): string {
    $valeur = $_POST[$nom] ?? '';
    return is_string($valeur) ? trim($valeur) : '';
}

/* ---------- Textes et dates ---------- */

// Nombre de caractères d'un texte UTF-8 (sans dépendre de l'extension mbstring)
function longueurTexte(string $texte): int {
    return (int)preg_match_all('/./us', $texte);
}

// Tronque un texte proprement
function tronquerTexte(string $texte, int $longueur = 140, string $suffixe = '…'): string {
    if (longueurTexte($texte) <= $longueur) {
        return $texte;
    }
    preg_match('/^.{0,' . $longueur . '}/us', $texte, $morceau);
    return rtrim($morceau[0]) . $suffixe;
}

// "2026-11-13 14:05:00" → "13/11/2026 à 14:05"
function dateFr(?string $date): string {
    return $date ? date('d/m/Y à H:i', strtotime($date)) : '';
}

// "1 candidature" / "3 candidatures"
function pluriel(int $nombre, string $mot): string {
    return $nombre . ' ' . $mot . ($nombre > 1 ? 's' : '');
}

/* ---------- Règles du jeu ---------- */

// Liste blanche des avatars proposés (fichiers présents dans img/avatars/)
function avatarsAutorises(): array {
    return ['avatar1.png', 'avatar2.png', 'avatar3.png', 'avatar4.png'];
}

// Badges Bootstrap pour l'état d'un défi et le statut d'une candidature
function badgeEtatDefi(string $etat): string {
    return $etat === 'ouvert'
        ? '<span class="badge text-bg-success">Ouvert</span>'
        : '<span class="badge text-bg-secondary">Terminé</span>';
}

function badgeStatutCandidature(string $statut): string {
    $badges = [
        'en_attente'  => '<span class="badge text-bg-warning">En attente</span>',
        'acceptee'    => '<span class="badge text-bg-success">Acceptée</span>',
        'non_retenue' => '<span class="badge text-bg-secondary">Non retenue</span>',
    ];
    return $badges[$statut] ?? '';
}
