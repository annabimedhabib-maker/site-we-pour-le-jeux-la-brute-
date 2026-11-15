<?php
require __DIR__ . '/includes/init.php';
exigerConnexion();

// Règle R1 : une seule brute par compte
if (maBrute($pdo, idJoueur())) {
    rediriger('ma_brute.php', 'info', 'Tu as déjà une brute.');
}

$erreurs     = [];
$nom         = '';
$description = '';
$avatar      = avatarsAutorises()[0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom         = texteDepuisPost('nom');
    $description = texteDepuisPost('description');
    $avatar      = texteDepuisPost('avatar');

    if (longueurTexte($nom) < 3 || longueurTexte($nom) > 50) {
        $erreurs[] = 'Le nom doit faire entre 3 et 50 caractères.';
    }
    if (longueurTexte($description) > 255) {
        $erreurs[] = 'La description ne doit pas dépasser 255 caractères.';
    }
    if (!in_array($avatar, avatarsAutorises(), true)) {
        $erreurs[] = 'Avatar invalide.';
    }

    if (!$erreurs) {
        $req = $pdo->prepare('SELECT COUNT(*) FROM brute WHERE nom = :nom');
        $req->execute([':nom' => $nom]);
        if ($req->fetchColumn() > 0) {
            $erreurs[] = 'Ce nom de brute est déjà pris.';
        }
    }

    if (!$erreurs) {
        // Caractéristiques attribuées PAR LE SERVEUR (jamais par le formulaire)
        $force = random_int(5, 8);

        try {
            $req = $pdo->prepare(
                'INSERT INTO brute (joueur_id, nom, description, avatar, pv, force_combat, defense)
                 VALUES (:joueur, :nom, :description, :avatar, :pv, :force, :defense)'
            );
            $req->execute([
                ':joueur'      => idJoueur(),   // vient de la session
                ':nom'         => $nom,
                ':description' => $description,
                ':avatar'      => $avatar,
                ':pv'          => PV_DEPART,
                ':force'       => $force,
                ':defense'     => 10 - $force,
            ]);
            rediriger('ma_brute.php', 'success', 'Ta brute est née ! Voici ses caractéristiques.');
        } catch (PDOException $e) {
            // Violation d'unicité : nom pris ou brute créée entre-temps
            $erreurs[] = 'Impossible de créer la brute (nom déjà pris ?).';
        }
    }
}

$titre_page = 'Créer ma brute';
include __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-12 col-md-10 col-lg-7">
    <div class="card">
      <div class="card-body p-4">
        <h1 class="h3">Créer ma brute</h1>
        <p class="text-body-secondary">Tes points de vie, ta force et ta défense seront attribués automatiquement par l'arène.</p>
        <?php composant('erreurs', ['erreurs' => $erreurs]); ?>
        <form method="post" action="creer_brute.php">
          <?= champCsrf() ?>
          <div class="mb-3">
            <label for="nom" class="form-label">Nom de la brute</label>
            <input type="text" class="form-control" id="nom" name="nom" minlength="3" maxlength="50" value="<?= e($nom) ?>" required>
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Courte description</label>
            <textarea class="form-control" id="description" name="description" rows="3" maxlength="255"><?= e($description) ?></textarea>
            <div class="form-text">255 caractères maximum.</div>
          </div>
          <?php composant('choix_avatar', ['avatarChoisi' => $avatar]); ?>
          <button type="submit" class="btn btn-lime">Créer ma brute</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
