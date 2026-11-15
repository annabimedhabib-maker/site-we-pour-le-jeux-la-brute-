<?php
require __DIR__ . '/includes/init.php';

$maBrute     = estConnecte() ? maBrute($pdo, idJoueur()) : null;
$defiEnCours = $maBrute ? defiActifBrute($pdo, (int)$maBrute['id']) : null;

// Filtre d'affichage (liste blanche)
$filtres = ['ouverts' => 'ouvert', 'termines' => 'termine', 'tous' => null];
$filtre  = $_GET['filtre'] ?? 'ouverts';
if (!is_string($filtre) || !array_key_exists($filtre, $filtres)) {
    $filtre = 'ouverts';
}
$defis = listeDefis($pdo, $filtres[$filtre]);

$titre_page = 'Les défis';
include __DIR__ . '/includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
  <div>
    <h1 class="mb-1">Les défis de l’arène</h1>
    <p class="text-body-secondary mb-0">Lance un défi ou propose ta brute pour affronter les autres combattants.</p>
  </div>
  <?php if ($defiEnCours): ?>
    <a class="btn btn-arena-outline" href="defi.php?id=<?= (int)$defiEnCours['id'] ?>">Mon défi en cours : <?= e(tronquerTexte($defiEnCours['titre'], 30)) ?></a>
  <?php elseif ($maBrute): ?>
    <a class="btn btn-lime" href="creer_defi.php">Publier un défi</a>
  <?php elseif (estConnecte()): ?>
    <a class="btn btn-lime" href="creer_brute.php">Créer ma brute pour défier</a>
  <?php endif; ?>
</div>

<?php if (!estConnecte()): ?>
  <div class="alert alert-info" role="alert">
    Connecte-toi pour publier un défi, postuler ou voir le détail des combats :
    <a href="connexion.php" class="alert-link">se connecter</a> ou <a href="inscription.php" class="alert-link">créer un compte</a>.
  </div>
<?php elseif (!$maBrute): ?>
  <div class="alert alert-warning" role="alert">
    Tu n'as pas encore de brute. <a href="creer_brute.php" class="alert-link">Crée ta brute</a> pour participer aux défis !
  </div>
<?php endif; ?>

<!-- Onglets de filtrage -->
<ul class="nav nav-pills nav-arena mb-4">
  <?php foreach (['ouverts' => 'Défis ouverts', 'termines' => 'Défis terminés', 'tous' => 'Tous les défis'] as $cle => $libelle): ?>
    <li class="nav-item">
      <a class="nav-link<?= $filtre === $cle ? ' active" aria-current="page' : '' ?>" href="defis.php?filtre=<?= $cle ?>"><?= $libelle ?></a>
    </li>
  <?php endforeach; ?>
</ul>

<?php if (!$defis): ?>
  <div class="card text-center py-4">
    <p class="mb-0 fs-5">Aucun défi ne correspond à ce filtre.</p>
    <?php if ($maBrute && !$defiEnCours && $filtre !== 'termines'): ?>
      <div><a class="btn btn-lime mt-3" href="creer_defi.php">Sois le premier à lancer un défi !</a></div>
    <?php endif; ?>
  </div>
<?php else: ?>
  <div class="row g-4">
    <?php foreach ($defis as $d): ?>
      <div class="col-12 col-md-6 col-lg-4">
        <?php composant('carte_defi', ['defi' => $d]); ?>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
