<?php
require __DIR__ . '/../includes/init.php';
exigerAdmin();

// Activation / désactivation d'un compte (POST uniquement). Un admin ne peut pas se désactiver lui-même.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id    = idDepuis('post', 'joueur_id');
    $actif = texteDepuisPost('actif') === '1' ? 1 : 0;

    if (!$id || $id === idJoueur()) {
        rediriger('admin/joueurs.php', 'warning', 'Tu ne peux pas modifier ton propre compte.');
    }
    $req = $pdo->prepare("UPDATE joueur SET actif = :actif WHERE id = :id AND role <> 'admin'");
    $req->execute([':actif' => $actif, ':id' => $id]);
    $req->rowCount()
        ? rediriger('admin/joueurs.php', 'success', $actif ? 'Le compte a été réactivé.' : 'Le compte a été désactivé.')
        : rediriger('admin/joueurs.php', 'warning', 'Aucune modification (compte introuvable, administrateur ou déjà dans cet état).');
}

$joueurs = $pdo->query(
    'SELECT j.id, j.login, j.email, j.role, j.actif, j.date_inscription, b.nom AS brute_nom
     FROM joueur j
     LEFT JOIN brute b ON b.joueur_id = j.id
     ORDER BY j.date_inscription DESC, j.id DESC'
)->fetchAll();

$titre_page = 'Administration – Joueurs';
include __DIR__ . '/../includes/header.php';
?>
<p><a href="index.php" class="text-decoration-none">&larr; Retour à l'administration</a></p>
<h1 class="mb-4">Comptes joueurs</h1>

<div class="table-responsive">
  <table class="table align-middle">
    <thead>
      <tr>
        <th scope="col">Login</th>
        <th scope="col">Email</th>
        <th scope="col">Brute</th>
        <th scope="col">Inscription</th>
        <th scope="col">État</th>
        <th scope="col"><span class="visually-hidden">Action</span></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($joueurs as $j): ?>
        <tr>
          <td>
            <?= e($j['login']) ?>
            <?php if ($j['role'] === 'admin'): ?><span class="badge text-bg-primary ms-1">admin</span><?php endif; ?>
          </td>
          <td class="small"><?= e($j['email']) ?></td>
          <td><?= $j['brute_nom'] !== null ? e($j['brute_nom']) : '<span class="text-body-secondary">—</span>' ?></td>
          <td class="text-nowrap small"><?= e(dateFr($j['date_inscription'])) ?></td>
          <td>
            <?= (int)$j['actif']
                ? '<span class="badge text-bg-success">Actif</span>'
                : '<span class="badge text-bg-secondary">Désactivé</span>' ?>
          </td>
          <td>
            <?php if ($j['role'] !== 'admin'): ?>
              <form method="post" action="joueurs.php">
                <?= champCsrf() ?>
                <input type="hidden" name="joueur_id" value="<?= (int)$j['id'] ?>">
                <input type="hidden" name="actif" value="<?= (int)$j['actif'] ? 0 : 1 ?>">
                <?php if ((int)$j['actif']): ?>
                  <button type="submit" class="btn btn-sm btn-outline-danger"
                    data-confirmation="Désactiver le compte de <?= e($j['login']) ?> ?">Désactiver</button>
                <?php else: ?>
                  <button type="submit" class="btn btn-sm btn-arena-outline">Réactiver</button>
                <?php endif; ?>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
