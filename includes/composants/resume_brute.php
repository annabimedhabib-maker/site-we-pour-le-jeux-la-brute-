<?php
// Petit encart "avatar + nom + caractéristiques". Variables : $brute, $libelle (optionnel, ex. "Ta brute")
defined('LABRUTE') or exit;
?>
<div class="encart d-flex align-items-center mb-3">
  <img class="avatar avatar-sm me-3" src="<?= e(urlAvatar($brute['avatar'])) ?>" alt="Avatar de <?= e($brute['nom']) ?>" width="56" height="56">
  <div>
    <div class="fw-bold"><?= e($brute['nom']) ?><?= !empty($libelle) ? ' <span class="text-body-secondary fw-normal">(' . e($libelle) . ')</span>' : '' ?></div>
    <div class="small text-body-secondary">
      PV : <?= (int)$brute['pv'] ?> · Force : <?= (int)$brute['force_combat'] ?> · Défense : <?= (int)$brute['defense'] ?>
    </div>
  </div>
</div>
