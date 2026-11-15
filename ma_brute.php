<?php
require __DIR__ . '/includes/init.php';
exigerConnexion();

// La brute du joueur connecté : l'id du joueur vient de la SESSION
$brute = maBrute($pdo, idJoueur());
if (!$brute) {
    rediriger('creer_brute.php', 'info', 'Crée d\'abord ta brute.');
}

$erreurs     = [];
$description = $brute['description'];
$avatar      = $brute['avatar'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = texteDepuisPost('description');
    $avatar      = texteDepuisPost('avatar');

    if (longueurTexte($description) > 255) {
        $erreurs[] = 'La description ne doit pas dépasser 255 caractères.';
    }
    if (!in_array($avatar, avatarsAutorises(), true)) {
        $erreurs[] = 'Avatar invalide.';
    }

    if (!$erreurs) {
        // Règle R2 : seuls la description et l'avatar sont modifiables
        $req = $pdo->prepare(
            'UPDATE brute SET description = :description, avatar = :avatar
             WHERE id = :id AND joueur_id = :joueur'
        );
        $req->execute([
            ':description' => $description,
            ':avatar'      => $avatar,
            ':id'          => $brute['id'],
            ':joueur'      => idJoueur(),
        ]);
        rediriger('ma_brute.php', 'success', 'Ta brute a été mise à jour.');
    }
}

$stats = statsBrute($pdo, (int)$brute['id']);

$titre_page = 'Ma brute';
include __DIR__ . '/includes/header.php';
?>
<h1 class="mb-4">Ma brute</h1>
<div class="row g-4">
  <section class="col-12 col-md-5">
    <?php composant('fiche_brute', [
        'brute'      => $brute,
        'stats'      => $stats,
        'lienAction' => ['url' => 'brute.php?id=' . (int)$brute['id'], 'texte' => 'Voir ma fiche publique'],
    ]); ?>
  </section>

  <section class="col-12 col-md-7">
    <div class="card">
      <div class="card-body p-4">
        <h2 class="h4">Modifier ma brute</h2>
        <p class="text-body-secondary">Tu peux changer ta description et ton avatar. Tes caractéristiques et tes résultats ne sont pas modifiables.</p>
        <?php composant('erreurs', ['erreurs' => $erreurs]); ?>
        <form method="post" action="ma_brute.php">
          <?= champCsrf() ?>
          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" maxlength="255"><?= e($description) ?></textarea>
          </div>
          <?php composant('choix_avatar', ['avatarChoisi' => $avatar]); ?>
          <button type="submit" class="btn btn-lime">Enregistrer</button>
        </form>
      </div>
    </div>
  </section>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
