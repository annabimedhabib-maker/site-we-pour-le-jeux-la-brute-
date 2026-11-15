<?php
require __DIR__ . '/includes/init.php';
exigerConnexion();

$maBrute = maBrute($pdo, idJoueur());
if (!$maBrute) {
    rediriger('creer_brute.php', 'warning', 'Tu dois d\'abord créer ta brute avant de pouvoir lancer un défi.');
}

// Règle R3 : un seul défi actif à la fois
$defiActif = defiActifBrute($pdo, (int)$maBrute['id']);
if ($defiActif) {
    rediriger('defi.php?id=' . (int)$defiActif['id'], 'warning',
        'Tu as déjà un défi en cours (« ' . $defiActif['titre'] . ' »). Choisis ton adversaire avant d\'en créer un nouveau.');
}

$erreurs     = [];
$titre       = '';
$description = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre       = texteDepuisPost('titre');
    $description = texteDepuisPost('description');

    if (longueurTexte($titre) < 3 || longueurTexte($titre) > 100) {
        $erreurs[] = 'Le titre du défi doit comporter entre 3 et 100 caractères.';
    }
    if (longueurTexte($description) < 5 || longueurTexte($description) > 1000) {
        $erreurs[] = 'La description du défi doit comporter entre 5 et 1000 caractères.';
    }

    if (!$erreurs) {
        // La brute, la date et l'état sont renseignés par le serveur
        $req = $pdo->prepare(
            "INSERT INTO defi (brute_id, titre, description, etat)
             VALUES (:brute_id, :titre, :description, 'ouvert')"
        );
        $req->execute([
            ':brute_id'    => $maBrute['id'],
            ':titre'       => $titre,
            ':description' => $description,
        ]);
        rediriger('defi.php?id=' . (int)$pdo->lastInsertId(), 'success', 'Ton défi a été publié avec succès !');
    }
}

$titre_page = 'Publier un défi';
include __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-12 col-md-10 col-lg-7">
    <p><a href="defis.php" class="text-decoration-none">&larr; Retour à la liste des défis</a></p>

    <div class="card">
      <div class="card-body p-4">
        <h1 class="h3 mb-3">Lancer un nouveau défi</h1>
        <p class="text-body-secondary">
          Tu engages ta brute dans l'arène. Les autres joueurs pourront postuler en proposant leur combattant ;
          tu choisiras ensuite ton adversaire pour déclencher le combat.
        </p>

        <?php composant('resume_brute', ['brute' => $maBrute, 'libelle' => 'ta brute engagée']); ?>
        <?php composant('erreurs', ['erreurs' => $erreurs]); ?>

        <form method="post" action="creer_defi.php">
          <?= champCsrf() ?>
          <div class="mb-3">
            <label for="titre" class="form-label fw-semibold">Titre du défi</label>
            <input type="text" class="form-control" id="titre" name="titre" minlength="3" maxlength="100"
              value="<?= e($titre) ?>" placeholder="Ex : Qui osera défier la terreur des bacs à sable ?" required>
            <div class="form-text">Entre 3 et 100 caractères.</div>
          </div>

          <div class="mb-4">
            <label for="description" class="form-label fw-semibold">Description du défi</label>
            <textarea class="form-control" id="description" name="description" rows="4" minlength="5" maxlength="1000"
              placeholder="Ex : Je cherche un adversaire digne de ce nom." required><?= e($description) ?></textarea>
            <div class="form-text">Entre 5 et 1000 caractères.</div>
          </div>

          <div class="d-flex flex-wrap gap-2">
            <button type="submit" class="btn btn-lime">Publier le défi</button>
            <a href="defis.php" class="btn btn-arena-outline">Annuler</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
