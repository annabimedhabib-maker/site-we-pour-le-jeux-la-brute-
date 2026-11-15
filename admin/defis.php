<?php
require __DIR__ . '/../includes/init.php';
exigerAdmin();

// Suppression d'un défi OUVERT (POST uniquement). Les candidatures et questions sont supprimées en cascade.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id  = idDepuis('post', 'defi_id');
    $req = $pdo->prepare("DELETE FROM defi WHERE id = :id AND etat = 'ouvert'");
    $req->execute([':id' => $id ?? 0]);
    $req->rowCount()
        ? rediriger('admin/defis.php', 'success', 'Le défi a été supprimé.')
        : rediriger('admin/defis.php', 'warning', 'Ce défi n\'existe plus ou est déjà terminé (un défi terminé ne peut pas être supprimé).');
}

$defis = listeDefis($pdo, 'ouvert', 500);

$titre_page = 'Administration – Défis ouverts';
include __DIR__ . '/../includes/header.php';
?>
<p><a href="index.php" class="text-decoration-none">&larr; Retour à l'administration</a></p>
<h1 class="mb-4">Défis ouverts</h1>

<?php if (!$defis): ?>
  <p class="text-body-secondary">Aucun défi ouvert.</p>
<?php else: ?>
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th scope="col">Titre</th>
          <th scope="col">Description</th>
          <th scope="col">Auteur</th>
          <th scope="col">Date</th>
          <th scope="col">Candidatures</th>
          <th scope="col"><span class="visually-hidden">Action</span></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($defis as $d): ?>
          <tr>
            <td><a href="<?= e(url('defi.php?id=' . (int)$d['id'])) ?>"><?= e($d['titre']) ?></a></td>
            <td class="small text-body-secondary"><?= e(tronquerTexte($d['description'], 80)) ?></td>
            <td><?= e($d['auteur_nom']) ?></td>
            <td class="text-nowrap"><?= e(dateFr($d['date_creation'])) ?></td>
            <td><?= (int)$d['nb_candidatures'] ?></td>
            <td>
              <form method="post" action="defis.php">
                <?= champCsrf() ?>
                <input type="hidden" name="defi_id" value="<?= (int)$d['id'] ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger"
                  data-confirmation="Supprimer définitivement le défi « <?= e($d['titre']) ?> » ?">Supprimer</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
