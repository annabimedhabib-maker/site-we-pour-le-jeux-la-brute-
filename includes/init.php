<?php
/*
 * Point d'entrée commun : À INCLURE EN PREMIÈRE LIGNE DE CHAQUE PAGE.
 *   require __DIR__ . '/includes/init.php';        (pages à la racine)
 *   require __DIR__ . '/../includes/init.php';     (pages du dossier admin/)
 *
 * Il charge, dans l'ordre :
 *   config.php            → connexion PDO ($pdo)
 *   auth.php              → session, connexion, rôles, jeton CSRF
 *   fonctions.php         → utilitaires (affichage, dates, redirections, textes)
 *   requetes.php          → requêtes SQL réutilisées par plusieurs pages
 *   combat_fonctions.php  → simulation d'un combat
 */

define('LABRUTE', true); // les fichiers d'includes refusent d'être appelés directement sans cette constante

// Chemin relatif vers la racine du site ('' pour les pages à la racine, '../' pour admin/…)
$dossierSite  = str_replace('\\', '/', dirname(__DIR__));
$dossierPage  = str_replace('\\', '/', dirname(realpath($_SERVER['SCRIPT_FILENAME'])));
$sousDossier  = trim(substr($dossierPage, strlen($dossierSite)), '/');
define('RACINE', $sousDossier === '' ? '' : str_repeat('../', substr_count($sousDossier, '/') + 1));
unset($dossierSite, $dossierPage, $sousDossier);

require __DIR__ . '/config.php';
require __DIR__ . '/fonctions.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/requetes.php';
require __DIR__ . '/combat_fonctions.php';

demarrerSession();
verifierCompteToujoursActif($pdo);
verifierCsrfSiPost();
