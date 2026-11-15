<?php
require __DIR__ . '/includes/init.php';
exigerConnexion();

$brute = maBrute($pdo, idJoueur());

$defisPublies = [];
$candidatures = [];
$combats      = [];
$stats        = null;
$defiEnCours  = null;
$nbEnAttente  = 0;

if ($brute) {
    $bruteId     = (int)$brute['id'];
    $stats       = statsBrute($pdo, $bruteId);
    $defiEnCours = defiActifBrute($pdo, $bruteId);
    $combats     = combatsBrute($pdo, $bruteId, 50);

    // Mes défis publiés (avec le combat éventuel)
    $req = $pdo->prepare(
        'SELECT d.id, d.titre, d.date_creation, d.etat, com.id AS combat_id,
                COUNT(c.id) AS nb_candidatures
         FROM defi d
         LEFT JOIN candidature c ON c.defi_id = d.id
         LEFT JOIN combat com ON com.defi_id = d.id
         WHERE d.brute_id = :id
         GROUP BY d.id, com.id
         ORDER BY d.date_creation DESC, d.id DESC'
    );
    $req->execute([':id' => $bruteId]);
    $defisPublies = $req->fetchAll();

    // Mes candidatures aux défis des autres
    $req = $pdo->prepare(
        'SELECT c.defi_id, c.statut, c.date_candidature,
                d.titre AS defi_titre, ba.nom AS auteur_nom, com.id AS combat_id
         FROM candidature c
         JOIN defi d   ON d.id = c.defi_id
         JOIN brute ba ON ba.id = d.brute_id
         LEFT JOIN combat com ON com.defi_id = d.id
         WHERE c.brute_id = :id
         ORDER BY c.date_candidature DESC, c.id DESC'
    );
    $req->execute([':id' => $bruteId]);
    $candidatures = $req->fetchAll();

    if ($defiEnCours) {
        $req = $pdo->prepare("SELECT COUNT(*) FROM candidature WHERE defi_id = :id AND statut = 'en_attente'");
        $req->execute([':id' => $defiEnCours['id']]);
        $nbEnAttente = (int)$req->fetchColumn();
    }
}

$titre_page = 'Mon espace';
include __DIR__ . '/includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
  <h1 class="mb-0">Mon espace <span class="text-body-secondary fs-4">(<?= e($_SESSION['login']) ?>)</span></h1>
  <?php if ($brute && !$defiEnCours): ?>
    <a class="btn btn-lime" href="creer_defi.php">Publier un défi</a>
  <?php endif; ?>
</div>

<?php if (!$brute): ?>
  <div class="card p-4 text-center">
    <h2>Tu n'as pas encore créé ta brute !</h2>
    <p class="text-body-secondary">Pour participer aux combats et lancer des défis, commence par créer ta brute.</p>
    <div><a class="btn btn-lime btn-lg" href="creer_brute.php">Créer ma brute</a></div>
  </div>
<?php else: ?>

  <!-- Ma brute -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="row align-items-center g-3">
        <div class="col-12 col-md-auto text-center">
          <img class="avatar avatar-lg" src="<?= e(urlAvatar($brute['avatar'])) ?>" alt="Avatar de <?= e($brute['nom']) ?>" width="96" height="96">
        </div>
        <div class="col-12 col-md">
          <h2 class="h4 mb-1"><?= e($brute['nom']) ?></h2>
          <p class="text-body-secondary mb-2"><?= $brute['description'] !== '' ? e($brute['description']) : '<em>Aucune description.</em>' ?></p>
          <div class="d-flex flex-wrap gap-3 small">
            <span><strong>PV :</strong> <?= (int)$brute['pv'] ?></span>
            <span><strong>Force :</strong> <?= (int)$brute['force_combat'] ?></span>
            <span><strong>Défense :</strong> <?= (int)$brute['defense'] ?></span>
            <span><strong>Combats :</strong> <?= $stats['combats'] ?></span>
            <span><strong>Victoires :</strong> <span class="text-success fw-bold"><?= $stats['victoires'] ?></span></span>
            <span><strong>Défaites :</strong> <span class="text-danger fw-bold"><?= $stats['defaites'] ?></span></span>
          </div>
        </div>
        <div class="col-12 col-md-auto d-flex flex-wrap gap-2">
          <a class="btn btn-arena-outline btn-sm" href="ma_brute.php">Modifier ma brute</a>
          <a class="btn btn-arena-outline btn-sm" href="brute.php?id=<?= (int)$brute['id'] ?>">Ma fiche publique</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Mon défi actif -->
  <?php if ($defiEnCours): ?>
    <div class="alert alert-warning d-flex flex-wrap justify-content-between align-items-center gap-2" role="status">
      <span>
        Défi en cours : <strong><?= e($defiEnCours['titre']) ?></strong> —
        <?= $nbEnAttente ? pluriel($nbEnAttente, 'candidature') . ' en attente de ta décision.' : 'aucune candidature pour l\'instant.' ?>
      </span>
      <a class="btn btn-lime btn-sm" href="defi.php?id=<?= (int)$defiEnCours['id'] ?>">Gérer mon défi</a>
    </div>
  <?php endif; ?>

  <div class="row g-4">
    <!-- Mes défis publiés -->
    <section class="col-12 col-lg-6" id="mes-defis" aria-labelledby="titre-mes-defis">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h2 id="titre-mes-defis" class="h5 mb-0">Mes défis publiés</h2>
          <span class="badge text-bg-secondary"><?= count($defisPublies) ?></span>
        </div>
        <div class="card-body">
          <?php if (!$defisPublies): ?>
            <p class="text-body-secondary small mb-0">Tu n'as encore publié aucun défi.</p>
          <?php else: ?>
            <div class="list-group list-group-flush">
              <?php foreach ($defisPublies as $d): ?>
                <div class="list-group-item px-0 d-flex justify-content-between align-items-center gap-2">
                  <div class="min-w-0">
                    <a href="defi.php?id=<?= (int)$d['id'] ?>" class="fw-semibold text-decoration-none"><?= e($d['titre']) ?></a>
                    <div class="small text-body-secondary">
                      <?= e(dateFr($d['date_creation'])) ?> · <?= pluriel((int)$d['nb_candidatures'], 'candidature') ?>
                    </div>
                  </div>
                  <div class="text-end text-nowrap">
                    <?= badgeEtatDefi($d['etat']) ?>
                    <?php if ($d['combat_id']): ?>
                      <a href="combat.php?id=<?= (int)$d['combat_id'] ?>" class="btn btn-sm btn-arena-outline ms-1">Combat</a>
                    <?php else: ?>
                      <a href="defi.php?id=<?= (int)$d['id'] ?>" class="btn btn-sm btn-arena-outline ms-1">Gérer</a>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- Mes candidatures -->
    <section class="col-12 col-lg-6" id="mes-candidatures" aria-labelledby="titre-mes-candidatures">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h2 id="titre-mes-candidatures" class="h5 mb-0">Mes candidatures</h2>
          <span class="badge text-bg-secondary"><?= count($candidatures) ?></span>
        </div>
        <div class="card-body">
          <?php if (!$candidatures): ?>
            <p class="text-body-secondary small mb-0">Tu n'as encore postulé à aucun défi. <a href="defis.php">Voir les défis ouverts</a></p>
          <?php else: ?>
            <div class="list-group list-group-flush">
              <?php foreach ($candidatures as $c): ?>
                <div class="list-group-item px-0 d-flex justify-content-between align-items-center gap-2">
                  <div class="min-w-0">
                    <a href="defi.php?id=<?= (int)$c['defi_id'] ?>" class="fw-semibold text-decoration-none"><?= e($c['defi_titre']) ?></a>
                    <div class="small text-body-secondary">
                      Auteur : <?= e($c['auteur_nom']) ?> · <?= e(dateFr($c['date_candidature'])) ?>
                    </div>
                  </div>
                  <div class="text-end text-nowrap">
                    <?= badgeStatutCandidature($c['statut']) ?>
                    <?php if ($c['statut'] === 'acceptee' && $c['combat_id']): ?>
                      <a href="combat.php?id=<?= (int)$c['combat_id'] ?>" class="btn btn-sm btn-arena-outline ms-1">Résultat</a>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- Mes combats -->
    <section class="col-12" id="mes-combats" aria-labelledby="titre-mes-combats">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h2 id="titre-mes-combats" class="h5 mb-0">Mes combats</h2>
          <span class="badge text-bg-secondary"><?= count($combats) ?></span>
        </div>
        <div class="card-body">
          <?php if (!$combats): ?>
            <p class="text-body-secondary small mb-0">Aucun combat enregistré pour l'instant.</p>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead>
                  <tr>
                    <th scope="col">Date</th>
                    <th scope="col">Adversaire</th>
                    <th scope="col">Issue</th>
                    <th scope="col"><span class="visually-hidden">Détail</span></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($combats as $cb): ?>
                    <?php $adversaire = (int)$cb['brute1_id'] === $bruteId ? $cb['nom2'] : $cb['nom1']; ?>
                    <tr>
                      <td><?= e(dateFr($cb['date_combat'])) ?></td>
                      <td><strong><?= e($adversaire) ?></strong></td>
                      <td>
                        <?= (int)$cb['vainqueur_id'] === $bruteId
                            ? '<span class="badge text-bg-success">Victoire</span>'
                            : '<span class="badge text-bg-danger">Défaite</span>' ?>
                      </td>
                      <td><a href="combat.php?id=<?= (int)$cb['id'] ?>" class="btn btn-sm btn-arena-outline">Compte rendu</a></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>
  </div>

<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
