<?php
/*
 * Traitement (sans affichage) : l'auteur d'un défi choisit son adversaire.
 * Le combat est simulé, puis tout est enregistré dans UNE transaction (règle R7).
 */
require __DIR__ . '/includes/init.php';
exigerConnexion();
exigerPost('defis.php');

$defiId = idDepuis('post', 'defi_id');
$candId = idDepuis('post', 'candidature_id');
if (!$defiId || !$candId) {
    rediriger('defis.php', 'warning', 'Paramètres de combat invalides.');
}

$maBrute = maBrute($pdo, idJoueur());
$defi    = defiAvecAuteur($pdo, $defiId);
if (!$defi) {
    rediriger('defis.php', 'warning', 'Défi introuvable.');
}
$pageDefi = 'defi.php?id=' . (int)$defi['id'];

// Seul l'auteur peut choisir l'adversaire
if (!$maBrute || (int)$defi['brute_id'] !== (int)$maBrute['id']) {
    rediriger($pageDefi, 'danger', 'Seul l’auteur du défi peut choisir son adversaire.');
}

// R8 : un défi terminé ne donne jamais lieu à un nouveau combat (ex. : page rechargée)
if ($defi['etat'] !== 'ouvert') {
    $combat = combatDuDefi($pdo, (int)$defi['id']);
    rediriger($combat ? 'combat.php?id=' . (int)$combat['id'] : $pageDefi, 'warning', 'Ce défi est déjà terminé.');
}

// La candidature doit appartenir à CE défi et être encore en attente
$req = $pdo->prepare("SELECT * FROM candidature WHERE id = :id AND defi_id = :defi_id AND statut = 'en_attente'");
$req->execute([':id' => $candId, ':defi_id' => $defi['id']]);
$candidature = $req->fetch();
if (!$candidature) {
    rediriger($pageDefi, 'warning', 'Cette candidature n’est plus disponible.');
}

$bruteAdversaire = bruteParId($pdo, (int)$candidature['brute_id']);
$resultat        = simulerCombat($maBrute, $bruteAdversaire);

try {
    $pdo->beginTransaction();

    // 1. Clôturer le défi (la condition etat = 'ouvert' bloque un double clic simultané)
    $req = $pdo->prepare("UPDATE defi SET etat = 'termine' WHERE id = :id AND etat = 'ouvert'");
    $req->execute([':id' => $defi['id']]);
    if ($req->rowCount() === 0) {
        $pdo->rollBack();
        rediriger($pageDefi, 'warning', 'Ce défi a déjà été clôturé.');
    }

    // 2. Candidature choisie → acceptée, les autres → non retenues
    $req = $pdo->prepare(
        "UPDATE candidature
         SET statut = IF(id = :choisie, 'acceptee', 'non_retenue')
         WHERE defi_id = :defi_id"
    );
    $req->execute([':choisie' => $candidature['id'], ':defi_id' => $defi['id']]);

    // 3. Enregistrer le combat (participants, vainqueur, compte rendu ; la date est automatique)
    $req = $pdo->prepare(
        'INSERT INTO combat (defi_id, brute1_id, brute2_id, vainqueur_id, compte_rendu)
         VALUES (:defi_id, :brute1_id, :brute2_id, :vainqueur_id, :compte_rendu)'
    );
    $req->execute([
        ':defi_id'      => $defi['id'],
        ':brute1_id'    => $maBrute['id'],
        ':brute2_id'    => $bruteAdversaire['id'],
        ':vainqueur_id' => $resultat['vainqueur_id'],
        ':compte_rendu' => $resultat['compte_rendu'],
    ]);
    $combatId = (int)$pdo->lastInsertId();

    $pdo->commit();
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    rediriger($pageDefi, 'danger', 'Une erreur est survenue lors de la résolution du combat.');
}

rediriger('combat.php?id=' . $combatId, 'success', 'Le combat a eu lieu ! Voici le compte rendu.');
