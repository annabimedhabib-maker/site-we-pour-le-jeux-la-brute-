<?php
/*
 * Carte complète d'une brute : avatar, nom, description, caractéristiques et résultats.
 * Variables : $brute (ligne de la table brute), $stats (statsBrute()),
 *             $niveauTitre ('h1' ou 'h2', défaut h2), $lienAction (['url' => …, 'texte' => …], optionnel)
 */
defined('LABRUTE') or exit;
$niveauTitre = $niveauTitre ?? 'h2';
?>
<div class="card">
  <div class="card-body text-center">
    <img class="avatar mb-3" src="<?= e(urlAvatar($brute['avatar'])) ?>" alt="Avatar de <?= e($brute['nom']) ?>" width="84" height="84">
    <<?= $niveauTitre ?> class="h4"><?= e($brute['nom']) ?></<?= $niveauTitre ?>>
    <p class="text-body-secondary"><?= $brute['description'] !== '' ? e($brute['description']) : '<em>Aucune description.</em>' ?></p>
    <?php if (!empty($lienAction)): ?>
      <a class="btn btn-arena-outline btn-sm" href="<?= e($lienAction['url']) ?>"><?= e($lienAction['texte']) ?></a>
    <?php endif; ?>
  </div>
  <ul class="list-group list-group-flush">
    <li class="list-group-item d-flex justify-content-between"><span>Points de vie</span><strong><?= (int)$brute['pv'] ?></strong></li>
    <li class="list-group-item d-flex justify-content-between"><span>Force</span><strong><?= (int)$brute['force_combat'] ?></strong></li>
    <li class="list-group-item d-flex justify-content-between"><span>Défense</span><strong><?= (int)$brute['defense'] ?></strong></li>
    <li class="list-group-item d-flex justify-content-between"><span>Combats</span><strong><?= $stats['combats'] ?></strong></li>
    <li class="list-group-item d-flex justify-content-between"><span>Victoires</span><strong class="text-success"><?= $stats['victoires'] ?></strong></li>
    <li class="list-group-item d-flex justify-content-between"><span>Défaites</span><strong class="text-danger"><?= $stats['defaites'] ?></strong></li>
  </ul>
</div>
