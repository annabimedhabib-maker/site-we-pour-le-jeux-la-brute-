<?php
require __DIR__ . '/includes/init.php';

// Visiteur : aperçu (nom + avatar). Joueur connecté : lien vers la fiche détaillée.
$brutes = $pdo->query('SELECT id, nom, avatar FROM brute ORDER BY nom')->fetchAll();

$titre_page = 'Les brutes';
include __DIR__ . '/includes/header.php';
?>
<div class="mb-4">
  <h1 class="mb-1">Les brutes</h1>
  <p class="text-body-secondary mb-0"><?= pluriel(count($brutes), 'combattant') ?> dans l'arène.</p>
</div>

<?php if (!estConnecte()): ?>
  <div class="alert alert-info" role="alert">
    <a href="connexion.php" class="alert-link">Connecte-toi</a> pour consulter la fiche complète de chaque brute.
  </div>
<?php endif; ?>

<?php if (!$brutes): ?>
  <p class="text-body-secondary">Aucune brute pour le moment.</p>
<?php else: ?>
  <div class="row g-3">
    <?php foreach ($brutes as $b): ?>
      <div class="col-6 col-md-4 col-lg-3">
        <div class="card h-100 text-center">
          <div class="card-body">
            <img class="avatar mb-2" src="<?= e(urlAvatar($b['avatar'])) ?>" alt="Avatar de <?= e($b['nom']) ?>" width="84" height="84">
            <h2 class="h6 card-title mb-2 text-truncate"><?= e($b['nom']) ?></h2>
            <?php if (estConnecte()): ?>
              <a class="btn btn-sm btn-arena-outline stretched-link" href="brute.php?id=<?= (int)$b['id'] ?>">Voir la fiche</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
