<?php
// Galerie de boutons radio pour choisir un avatar. Variable : $avatarChoisi
defined('LABRUTE') or exit;
?>
<fieldset class="mb-3">
  <legend class="form-label fs-6">Avatar</legend>
  <div class="d-flex flex-wrap gap-3">
    <?php foreach (avatarsAutorises() as $i => $fichier): ?>
      <div class="choix-avatar">
        <input class="btn-check" type="radio" name="avatar" id="avatar<?= $i ?>" value="<?= e($fichier) ?>"
          <?= $fichier === $avatarChoisi ? 'checked' : '' ?> required>
        <label class="choix-avatar-label" for="avatar<?= $i ?>">
          <img class="avatar" src="<?= e(urlAvatar($fichier)) ?>" alt="Avatar <?= $i + 1 ?>" width="84" height="84">
        </label>
      </div>
    <?php endforeach; ?>
  </div>
</fieldset>
