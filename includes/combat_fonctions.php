<?php
// Simulation d'un combat selon les règles du sujet (page 3).
defined('LABRUTE') or exit;

const PV_DEPART = 30;

/*
 * $b1 et $b2 : tableaux contenant au moins id, nom, force_combat, defense.
 * Retourne ['vainqueur_id' => int, 'compte_rendu' => string (une ligne par événement)].
 * Les PV perdus ne concernent que ce combat : la brute en base n'est jamais modifiée.
 */
function simulerCombat(array $b1, array $b2): array {
    $combattants = [
        ['brute' => $b1, 'pv' => PV_DEPART],
        ['brute' => $b2, 'pv' => PV_DEPART],
    ];

    // Le serveur tire au hasard la brute qui attaque en premier
    $attaquant = random_int(0, 1);
    $lignes = [sprintf("Tirage au sort : %s a l'initiative et attaque en premier !", $combattants[$attaquant]['brute']['nom'])];

    $tour = 1;
    while ($combattants[0]['pv'] > 0 && $combattants[1]['pv'] > 0) {
        $defenseur = 1 - $attaquant;
        $att = $combattants[$attaquant]['brute'];
        $def = $combattants[$defenseur]['brute'];

        // Dégâts = force de l'attaquant + aléatoire(0..2) − défense de l'adversaire, minimum 1
        $degats = max(1, (int)$att['force_combat'] + random_int(0, 2) - (int)$def['defense']);
        $combattants[$defenseur]['pv'] = max(0, $combattants[$defenseur]['pv'] - $degats);

        $lignes[] = sprintf(
            'Tour %d : %s attaque %s et lui inflige %s. Il reste %d PV à %s.',
            $tour,
            $att['nom'],
            $def['nom'],
            pluriel($degats, 'dégât'),
            $combattants[$defenseur]['pv'],
            $def['nom']
        );

        $attaquant = $defenseur; // on échange les rôles
        $tour++;
    }

    $vainqueur = $combattants[0]['pv'] > 0 ? $b1 : $b2;
    $lignes[] = sprintf('Victoire de %s !', $vainqueur['nom']);

    return [
        'vainqueur_id' => (int)$vainqueur['id'],
        'compte_rendu' => implode("\n", $lignes),
    ];
}
