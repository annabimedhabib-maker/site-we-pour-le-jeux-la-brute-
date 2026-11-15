<?php
// Connexion à la base de données (PDO).
// Les identifiants ne sont PAS écrits ici : ils sont lus dans includes/config.local.php
// (fichier non versionné, voir config.example.php pour le modèle).
defined('LABRUTE') or exit;

$fichierLocal = __DIR__ . '/config.local.php';
if (!is_file($fichierLocal)) {
    http_response_code(500);
    exit('Configuration manquante : copier includes/config.example.php en includes/config.local.php.');
}

$configs = require $fichierLocal;
$estEnLocal = in_array($_SERVER['SERVER_NAME'] ?? 'localhost', ['localhost', '127.0.0.1'], true);
$bdd = $estEnLocal ? $configs['local'] : $configs['serveur'];

try {
    $pdo = new PDO(
        "mysql:host={$bdd['hote']};dbname={$bdd['base']};charset=utf8mb4",
        $bdd['utilisateur'],
        $bdd['mot_de_passe'],
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit('Erreur de connexion à la base de données.'); // ne jamais afficher $e->getMessage()
}

unset($fichierLocal, $configs, $estEnLocal, $bdd);
