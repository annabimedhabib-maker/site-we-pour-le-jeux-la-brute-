<?php
require __DIR__ . '/includes/init.php';
exigerConnexion();

// Accès par l'id du combat (combat.php?id=N) ou par celui du défi (combat.php?defi_id=N)
$combatId = idDepuis('get', 'id');
$defiId   = idDepuis('get', 'defi_id');
if (!$combatId && !$defiId) {
    rediriger('defis.php', 'warning', 'Combat introuvable.');
}

$req = $pdo->prepare(
    'SELECT c.id, c.defi_id, c.vainqueur_id, c.date_combat, c.compte_rendu,
            b1.id AS b1_id, b1.nom AS nom1, b1.avatar AS avatar1, b1.force_combat AS force1, b1.defense AS def1,
            b2.id AS b2_id, b2.nom AS nom2, b2.avatar AS avatar2, b2.force_combat AS force2, b2.defense AS def2,
            bv.nom AS vainqueur_nom, d.titre AS defi_titre
     FROM combat c
     JOIN brute b1 ON b1.id = c.brute1_id
     JOIN brute b2 ON b2.id = c.brute2_id
     JOIN brute bv ON bv.id = c.vainqueur_id
     JOIN defi d   ON d.id = c.defi_id
     WHERE ' . ($combatId ? 'c.id = :id' : 'c.defi_id = :id')
);
$req->execute([':id' => $combatId ?: $defiId]);
$combat = $req->fetch();
if (!$combat) {
    rediriger('defis.php', 'warning', 'Combat introuvable.');
}

// Le compte rendu est RELU depuis la base : la simulation n'est jamais relancée.
// Ligne 1 = tirage au sort, dernière ligne = vainqueur, entre les deux = les attaques.
$lignes   = array_values(array_filter(array_map('trim', explode("\n", $combat['compte_rendu'])), 'strlen'));
$tirage   = array_shift($lignes);
$verdict  = array_pop($lignes);
$attaques = $lignes;

$combattants = [
    ['id' => $combat['b1_id'], 'nom' => $combat['nom1'], 'avatar' => $combat['avatar1'], 'force' => $combat['force1'], 'defense' => $combat['def1'], 'role' => 'Auteur du défi'],
    ['id' => $combat['b2_id'], 'nom' => $combat['nom2'], 'avatar' => $combat['avatar2'], 'force' => $combat['force2'], 'defense' => $combat['def2'], 'role' => 'Adversaire'],
];

$titre_page = 'Combat : ' . $combat['nom1'] . ' contre ' . $combat['nom2'];
include __DIR__ . '/includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <p class="mb-0">
    <a href="defi.php?id=<?= (int)$combat['defi_id'] ?>" class="text-decoration-none">&larr; Revenir au défi</a>
    <span class="text-body-secondary mx-2">|</span>
    <a href="defis.php" class="text-decoration-none">Tous les défis</a>
  </p>
  <span class="text-body-secondary small">Combat du <?= e(dateFr($combat['date_combat'])) ?></span>
</div>

<h1 class="visually-hidden"><?= e($titre_page) ?></h1>

<!-- Face à face -->
<div class="card mb-4">
  <div class="card-body py-4">
    <div class="row align-items-center text-center g-3">
      <?php foreach ($combattants as $i => $b): ?>
        <?php $gagnant = (int)$b['id'] === (int)$combat['vainqueur_id']; ?>
        <div class="col-12 col-md-4<?= $i === 1 ? ' order-md-3' : '' ?>">
          <div class="combattant p-3<?= $gagnant ? ' combattant-vainqueur' : '' ?>">
            <?php if ($gagnant): ?><span class="badge text-bg-success mb-2">Vainqueur</span><?php endif; ?>
            <div><img class="avatar avatar-lg mb-2" src="<?= e(urlAvatar($b['avatar'])) ?>" alt="Avatar de <?= e($b['nom']) ?>" width="96" height="96"></div>
            <h2 class="h5 mb-1"><a href="brute.php?id=<?= (int)$b['id'] ?>" class="text-decoration-none"><?= e($b['nom']) ?></a></h2>
            <span class="badge text-bg-secondary mb-2"><?= $b['role'] ?></span>
            <div class="small text-body-secondary">PV : <?= PV_DEPART ?> · Force : <?= (int)$b['force'] ?> · Défense : <?= (int)$b['defense'] ?></div>
          </div>
        </div>
      <?php endforeach; ?>
      <div class="col-12 col-md-4 order-md-2">
        <div class="display-6 fw-bold text-lime">VS</div>
        <p class="small text-body-secondary mb-0">Défi : « <?= e($combat['defi_titre']) ?> »</p>
      </div>
    </div>
  </div>
</div>

<div class="alert alert-success text-center py-3 mb-4" role="status">
  <p class="h4 mb-1">Victoire de <strong><?= e($combat['vainqueur_nom']) ?></strong> !</p>
  <p class="mb-0 small">Combat résolu automatiquement par l'arène en <?= pluriel(count($attaques), 'attaque') ?>.</p>
</div>

<!-- Compte rendu complet -->
<section class="card mb-4" aria-labelledby="titre-compte-rendu">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h2 id="titre-compte-rendu" class="h5 mb-0">Compte rendu du combat</h2>
    <span class="badge text-bg-secondary"><?= pluriel(count($attaques), 'attaque') ?></span>
  </div>
  <ol class="list-group list-group-flush compte-rendu">
    <?php if ($tirage !== null): ?>
      <li class="list-group-item text-center fst-italic text-info"><?= e($tirage) ?></li>
    <?php endif; ?>
    <?php foreach ($attaques as $ligne): ?>
      <li class="list-group-item"><?= e($ligne) ?></li>
    <?php endforeach; ?>
    <?php if ($verdict !== null): ?>
      <li class="list-group-item list-group-item-success text-center fw-bold"><?= e($verdict) ?></li>
    <?php endif; ?>
  </ol>
</section>

<div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
  <a href="defi.php?id=<?= (int)$combat['defi_id'] ?>" class="btn btn-arena-outline">Retour au défi</a>
  <a href="defis.php" class="btn btn-lime">Voir les autres défis</a>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
