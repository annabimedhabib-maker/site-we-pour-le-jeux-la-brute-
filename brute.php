<?php
require __DIR__ . '/includes/init.php';
exigerConnexion();

// L'id vient de l'URL : on le valide (entier positif) avant toute requête
$id    = idDepuis('get', 'id');
$brute = $id ? bruteParId($pdo, $id) : null;
if (!$brute) {
    rediriger('brutes.php', 'warning', 'Brute introuvable.');
}

$stats      = statsBrute($pdo, (int)$brute['id']);
$combats    = combatsBrute($pdo, (int)$brute['id'], 20);
$estMaBrute = (int)$brute['joueur_id'] === idJoueur();

$titre_page = 'Brute : ' . $brute['nom'];
include __DIR__ . '/includes/header.php';
?>
<p><a href="brutes.php" class="text-decoration-none">&larr; Retour à la liste des brutes</a></p>
<div class="row g-4">
  <section class="col-12 col-md-5">
    <?php composant('fiche_brute', [
        'brute'       => $brute,
        'stats'       => $stats,
        'niveauTitre' => 'h1',
        'lienAction'  => $estMaBrute ? ['url' => 'ma_brute.php', 'texte' => 'C’est ma brute – la modifier'] : null,
    ]); ?>
  </section>

  <section class="col-12 col-md-7">
    <h2 class="h4">Derniers combats</h2>
    <?php if (!$combats): ?>
      <p class="text-body-secondary">Cette brute n’a pas encore combattu.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead>
            <tr>
              <th scope="col">Date</th>
              <th scope="col">Duel</th>
              <th scope="col">Issue</th>
              <th scope="col"><span class="visually-hidden">Détail</span></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($combats as $c): ?>
              <tr>
                <td><?= e(dateFr($c['date_combat'])) ?></td>
                <td><?= e($c['nom1']) ?> contre <?= e($c['nom2']) ?></td>
                <td>
                  <?= (int)$c['vainqueur_id'] === (int)$brute['id']
                      ? '<span class="badge text-bg-success">Victoire</span>'
                      : '<span class="badge text-bg-danger">Défaite</span>' ?>
                </td>
                <td><a href="combat.php?id=<?= (int)$c['id'] ?>">Compte rendu</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </section>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
