<?php
require __DIR__ . '/../includes/init.php';
exigerAdmin();

$compteurs = $pdo->query(
    "SELECT
        (SELECT COUNT(*) FROM joueur)                    AS joueurs,
        (SELECT COUNT(*) FROM joueur WHERE actif = 0)    AS desactives,
        (SELECT COUNT(*) FROM brute)                     AS brutes,
        (SELECT COUNT(*) FROM defi WHERE etat = 'ouvert') AS defis_ouverts,
        (SELECT COUNT(*) FROM combat)                    AS combats"
)->fetch();

$titre_page = 'Administration';
include __DIR__ . '/../includes/header.php';
?>
<h1 class="mb-4">Administration</h1>

<div class="row g-3 mb-4">
  <?php foreach ([
      ['Joueurs', $compteurs['joueurs']],
      ['Comptes désactivés', $compteurs['desactives']],
      ['Brutes', $compteurs['brutes']],
      ['Défis ouverts', $compteurs['defis_ouverts']],
      ['Combats', $compteurs['combats']],
  ] as [$libelle, $valeur]): ?>
    <div class="col-6 col-md-4 col-xl">
      <div class="card h-100 text-center p-3">
        <div class="display-6 fw-bold text-lime"><?= (int)$valeur ?></div>
        <div class="small text-body-secondary"><?= e($libelle) ?></div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="row g-4">
  <div class="col-12 col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <h2 class="h5">Modération des défis</h2>
        <p class="text-body-secondary">Supprimer les défis ouverts au contenu inapproprié.</p>
        <a class="btn btn-lime" href="defis.php">Gérer les défis</a>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <h2 class="h5">Comptes joueurs</h2>
        <p class="text-body-secondary">Désactiver ou réactiver un compte joueur.</p>
        <a class="btn btn-lime" href="joueurs.php">Gérer les joueurs</a>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
