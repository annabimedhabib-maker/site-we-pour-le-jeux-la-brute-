<?php
/*
 * BONUS – Traitement (sans affichage) des questions sur un défi.
 *   action=poser     : un joueur (pas l'auteur) pose une question sur un défi ouvert
 *   action=repondre  : l'auteur du défi répond à une question encore sans réponse
 */
require __DIR__ . '/includes/init.php';
exigerConnexion();
exigerPost('defis.php');

$action = texteDepuisPost('action');

if ($action === 'poser') {
    $defi = ($id = idDepuis('post', 'defi_id')) ? defiAvecAuteur($pdo, $id) : null;
    if (!$defi) {
        rediriger('defis.php', 'warning', 'Défi introuvable.');
    }
    $pageDefi = 'defi.php?id=' . (int)$defi['id'] . '#questions';

    if ($defi['etat'] !== 'ouvert') {
        rediriger($pageDefi, 'warning', 'Ce défi est terminé : on ne peut plus poser de question.');
    }
    if ((int)$defi['auteur_joueur_id'] === idJoueur()) {
        rediriger($pageDefi, 'warning', 'Tu ne peux pas te poser une question à toi-même !');
    }

    $texte = texteDepuisPost('question');
    if (longueurTexte($texte) < 3 || longueurTexte($texte) > 500) {
        rediriger($pageDefi, 'danger', 'La question doit comporter entre 3 et 500 caractères.');
    }

    $req = $pdo->prepare('INSERT INTO question (defi_id, joueur_id, texte_question) VALUES (:defi, :joueur, :texte)');
    $req->execute([':defi' => $defi['id'], ':joueur' => idJoueur(), ':texte' => $texte]);
    rediriger($pageDefi, 'success', 'Ta question a été envoyée à l\'auteur du défi.');
}

if ($action === 'repondre') {
    $id  = idDepuis('post', 'question_id');
    $req = $pdo->prepare(
        'SELECT q.id, q.defi_id, q.texte_reponse, d.etat, b.joueur_id AS auteur_joueur_id
         FROM question q
         JOIN defi d  ON d.id = q.defi_id
         JOIN brute b ON b.id = d.brute_id
         WHERE q.id = :id'
    );
    $req->execute([':id' => $id ?? 0]);
    $question = $req->fetch();
    if (!$question) {
        rediriger('defis.php', 'warning', 'Question introuvable.');
    }
    $pageDefi = 'defi.php?id=' . (int)$question['defi_id'] . '#questions';

    if ((int)$question['auteur_joueur_id'] !== idJoueur()) {
        rediriger($pageDefi, 'danger', 'Seul l\'auteur du défi peut répondre aux questions.');
    }
    if ($question['etat'] !== 'ouvert' || $question['texte_reponse'] !== null) {
        rediriger($pageDefi, 'warning', 'Cette question ne peut plus recevoir de réponse.');
    }

    $texte = texteDepuisPost('reponse');
    if (longueurTexte($texte) < 1 || longueurTexte($texte) > 1000) {
        rediriger($pageDefi, 'danger', 'La réponse doit comporter entre 1 et 1000 caractères.');
    }

    $req = $pdo->prepare('UPDATE question SET texte_reponse = :texte, date_reponse = NOW() WHERE id = :id AND texte_reponse IS NULL');
    $req->execute([':texte' => $texte, ':id' => $question['id']]);
    rediriger($pageDefi, 'success', 'Ta réponse est publiée.');
}

rediriger('defis.php', 'warning', 'Action inconnue.');
