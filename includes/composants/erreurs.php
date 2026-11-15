<?php
// Liste des erreurs d'un formulaire. Variable : $erreurs (tableau de chaînes)
defined('LABRUTE') or exit;
?>
<?php if (!empty($erreurs)): ?>
  <div class="alert alert-danger" role="alert">
    <?php if (count($erreurs) === 1): ?>
      <?= e($erreurs[0]) ?>
    <?php else: ?>
      <ul class="mb-0 ps-3">
        <?php foreach ($erreurs as $erreur): ?>
          <li><?= e($erreur) ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
<?php endif; ?>
