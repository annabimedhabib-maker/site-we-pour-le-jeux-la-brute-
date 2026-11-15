<?php
// MODÈLE : copier ce fichier en includes/config.local.php puis remplir les valeurs.
defined('LABRUTE') or exit;

return [
    'local' => [
        'hote'         => 'localhost',
        'base'         => 'labrute',
        'utilisateur'  => 'root',
        'mot_de_passe' => '',
    ],
    'serveur' => [
        'hote'         => 'localhost',
        'base'         => 'bdd_X_Y',
        'utilisateur'  => 'grp_X_Y',
        'mot_de_passe' => 'mot_de_passe_ici',
    ],
];
