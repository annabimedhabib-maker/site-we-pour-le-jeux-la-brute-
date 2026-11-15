<?php
// Requêtes SQL réutilisées par plusieurs pages (toutes préparées).
defined('LABRUTE') or exit;

/* ---------- Brutes ---------- */

// La brute d'un joueur (ou null s'il n'en a pas encore)
function maBrute(PDO $pdo, int $joueurId): ?array {
    $req = $pdo->prepare('SELECT * FROM brute WHERE joueur_id = :id');
    $req->execute([':id' => $joueurId]);
    return $req->fetch() ?: null;
}

function bruteParId(PDO $pdo, int $bruteId): ?array {
    $req = $pdo->prepare('SELECT * FROM brute WHERE id = :id');
    $req->execute([':id' => $bruteId]);
    return $req->fetch() ?: null;
}

// Statistiques d'une brute, CALCULÉES à partir de la table combat (jamais stockées)
function statsBrute(PDO $pdo, int $bruteId): array {
    $req = $pdo->prepare(
        'SELECT COUNT(*) AS combats,
                COALESCE(SUM(vainqueur_id = :id1), 0) AS victoires
         FROM combat
         WHERE brute1_id = :id2 OR brute2_id = :id3'
    );
    $req->execute([':id1' => $bruteId, ':id2' => $bruteId, ':id3' => $bruteId]);
    $ligne = $req->fetch();

    $combats   = (int)$ligne['combats'];
    $victoires = (int)$ligne['victoires'];
    return [
        'combats'   => $combats,
        'victoires' => $victoires,
        'defaites'  => $combats - $victoires,
    ];
}

// Historique des combats d'une brute (les plus récents d'abord)
function combatsBrute(PDO $pdo, int $bruteId, int $limite = 20): array {
    $req = $pdo->prepare(
        'SELECT c.id, c.defi_id, c.date_combat, c.brute1_id, c.brute2_id, c.vainqueur_id,
                b1.nom AS nom1, b2.nom AS nom2, bv.nom AS vainqueur_nom
         FROM combat c
         JOIN brute b1 ON b1.id = c.brute1_id
         JOIN brute b2 ON b2.id = c.brute2_id
         JOIN brute bv ON bv.id = c.vainqueur_id
         WHERE c.brute1_id = :id1 OR c.brute2_id = :id2
         ORDER BY c.date_combat DESC, c.id DESC
         LIMIT ' . max(1, $limite)
    );
    $req->execute([':id1' => $bruteId, ':id2' => $bruteId]);
    return $req->fetchAll();
}

/* ---------- Défis ---------- */

// Le défi ouvert d'une brute (ou null) — règle R3 : un seul défi actif
function defiActifBrute(PDO $pdo, int $bruteId): ?array {
    $req = $pdo->prepare("SELECT * FROM defi WHERE brute_id = :id AND etat = 'ouvert'");
    $req->execute([':id' => $bruteId]);
    return $req->fetch() ?: null;
}

// Un défi avec les informations de la brute auteur
function defiAvecAuteur(PDO $pdo, int $defiId): ?array {
    $req = $pdo->prepare(
        'SELECT d.*, b.nom AS auteur_nom, b.avatar AS auteur_avatar, b.pv AS auteur_pv,
                b.force_combat AS auteur_force, b.defense AS auteur_defense,
                b.joueur_id AS auteur_joueur_id
         FROM defi d
         JOIN brute b ON b.id = d.brute_id
         WHERE d.id = :id'
    );
    $req->execute([':id' => $defiId]);
    return $req->fetch() ?: null;
}

// Liste des défis avec auteur et nombre de candidatures. $etat : 'ouvert', 'termine' ou null (tous)
function listeDefis(PDO $pdo, ?string $etat = null, int $limite = 100): array {
    $where = $etat ? 'WHERE d.etat = :etat' : '';
    $req = $pdo->prepare(
        "SELECT d.id, d.titre, d.description, d.date_creation, d.etat,
                b.id AS auteur_brute_id, b.nom AS auteur_nom, b.avatar AS auteur_avatar,
                COUNT(c.id) AS nb_candidatures
         FROM defi d
         JOIN brute b ON b.id = d.brute_id
         LEFT JOIN candidature c ON c.defi_id = d.id
         $where
         GROUP BY d.id
         ORDER BY d.date_creation DESC, d.id DESC
         LIMIT " . max(1, $limite)
    );
    $req->execute($etat ? [':etat' => $etat] : []);
    return $req->fetchAll();
}

/* ---------- Candidatures ---------- */

function candidaturesDefi(PDO $pdo, int $defiId): array {
    $req = $pdo->prepare(
        'SELECT c.id, c.brute_id, c.message, c.statut, c.date_candidature,
                b.nom, b.avatar, b.pv, b.force_combat, b.defense
         FROM candidature c
         JOIN brute b ON b.id = c.brute_id
         WHERE c.defi_id = :defi_id
         ORDER BY c.date_candidature ASC, c.id ASC'
    );
    $req->execute([':defi_id' => $defiId]);
    return $req->fetchAll();
}

function candidatureDeBrute(PDO $pdo, int $defiId, int $bruteId): ?array {
    $req = $pdo->prepare('SELECT * FROM candidature WHERE defi_id = :defi_id AND brute_id = :brute_id');
    $req->execute([':defi_id' => $defiId, ':brute_id' => $bruteId]);
    return $req->fetch() ?: null;
}

/* ---------- Combats ---------- */

function combatDuDefi(PDO $pdo, int $defiId): ?array {
    $req = $pdo->prepare(
        'SELECT c.id, c.date_combat, c.vainqueur_id,
                b1.nom AS nom1, b1.avatar AS avatar1,
                b2.nom AS nom2, b2.avatar AS avatar2,
                bv.nom AS vainqueur_nom
         FROM combat c
         JOIN brute b1 ON b1.id = c.brute1_id
         JOIN brute b2 ON b2.id = c.brute2_id
         JOIN brute bv ON bv.id = c.vainqueur_id
         WHERE c.defi_id = :defi_id'
    );
    $req->execute([':defi_id' => $defiId]);
    return $req->fetch() ?: null;
}

/* ---------- Questions (bonus) ---------- */

function questionsDefi(PDO $pdo, int $defiId): array {
    $req = $pdo->prepare(
        'SELECT q.*, j.login
         FROM question q
         JOIN joueur j ON j.id = q.joueur_id
         WHERE q.defi_id = :defi_id
         ORDER BY q.date_question ASC, q.id ASC'
    );
    $req->execute([':defi_id' => $defiId]);
    return $req->fetchAll();
}
