<?php
/*
 * Carte d'un défi dans une liste (defis.php).
 * Variable : $defi (ligne de listeDefis()).
 * Visiteur : aperçu seulement (titre + brute auteur). Joueur connecté : description, date, lien.
 */
defined('LABRUTE') or exit;
$nb = (int)$defi['nb_candidatures'];
?>
<div class="card carte-defi h-100<?= $defi['etat'] === 'ouvert' ? ' carte-defi-ouverte' : '' ?>">
  <div class="card-header d-flex align-items-center justify-content-between gap-2">
    <div class="d-flex align-items-center min-w-0">
      <img class="avatar avatar-xs me-2" src="<?= e(urlAvatar($defi['auteur_avatar'])) ?>" alt="" width="40" height="40">
      <div class="min-w-0">
        <span class="small text-body-secondary d-block">Auteur</span>
        <strong class="d-block text-truncate"><?= e($defi['auteur_nom']) ?></strong>
      </div>
    </div>
    <?= badgeEtatDefi($defi['etat']) ?>
  </div>
  <div class="card-body d-flex flex-column">
    <h2 class="h5 card-title text-lime"><?= e($defi['titre']) ?></h2>
    <?php if (estConnecte()): ?>
      <p class="card-text text-body-secondary small flex-grow-1"><?= e(tronquerTexte($defi['description'], 140)) ?></p>
      <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top small text-body-secondary">
        <span><?= e(dateFr($defi['date_creation'])) ?></span>
        <span class="badge text-bg-dark border"><?= pluriel($nb, 'candidature') ?></span>
      </div>
    <?php endif; ?>
  </div>
  <div class="card-footer border-top-0 pt-0">
    <?php if (estConnecte()): ?>
      <a class="btn btn-arena-outline btn-sm w-100" href="<?= e(url('defi.php?id=' . (int)$defi['id'])) ?>">Consulter le défi &rarr;</a>
    <?php else: ?>
      <a class="btn btn-arena-outline btn-sm w-100" href="<?= e(url('connexion.php')) ?>">Se connecter pour voir le détail</a>
    <?php endif; ?>
  </div>
</div>
