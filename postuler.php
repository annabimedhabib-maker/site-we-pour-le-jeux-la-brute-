<?php
require __DIR__ . '/includes/init.php';
exigerConnexion();

$maBrute = maBrute($pdo, idJoueur());
if (!$maBrute) {
    rediriger('creer_brute.php', 'warning', 'Tu dois créer ta brute avant de pouvoir postuler à un défi.');
}

$defiId = idDepuis('post', 'defi_id') ?? idDepuis('get', 'id');
$defi   = $defiId ? defiAvecAuteur($pdo, $defiId) : null;
if (!$defi) {
    rediriger('defis.php', 'warning', 'Défi introuvable.');
}
$pageDefi = 'defi.php?id=' . (int)$defi['id'];

// R6 : seuls les défis ouverts acceptent des candidatures
if ($defi['etat'] !== 'ouvert') {
    rediriger($pageDefi, 'warning', 'Ce défi est terminé : il n\'accepte plus de candidatures.');
}
// R4 : on ne répond pas à son propre défi
if ((int)$defi['brute_id'] === (int)$maBrute['id']) {
    rediriger($pageDefi, 'warning', 'Tu ne peux pas postuler à ton propre défi !');
}
// R5 : une seule candidature par défi
if (candidatureDeBrute($pdo, (int)$defi['id'], (int)$maBrute['id'])) {
    rediriger($pageDefi, 'info', 'Tu as déjà postulé à ce défi.');
}

$erreurs = [];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = texteDepuisPost('message');

    if (longueurTexte($message) > 255) {
        $erreurs[] = 'Le message ne doit pas dépasser 255 caractères.';
    }

    if (!$erreurs) {
        try {
            // La brute candidate vient de la session, jamais du formulaire
            $req = $pdo->prepare(
                "INSERT INTO candidature (defi_id, brute_id, message, statut)
                 VALUES (:defi_id, :brute_id, :message, 'en_attente')"
            );
            $req->execute([
                ':defi_id'  => $defi['id'],
                ':brute_id' => $maBrute['id'],
                ':message'  => $message,
            ]);
            rediriger($pageDefi, 'success', 'Ta candidature a bien été envoyée à l\'auteur du défi !');
        } catch (PDOException $e) {
            $erreurs[] = 'Impossible d\'enregistrer ta candidature (as-tu déjà postulé ?).';
        }
    }
}

$titre_page = 'Postuler au défi : ' . $defi['titre'];
include __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-12 col-md-10 col-lg-7">
    <p><a href="<?= e($pageDefi) ?>" class="text-decoration-none">&larr; Retour au défi</a></p>

    <div class="card">
      <div class="card-body p-4">
        <h1 class="h3 mb-3">Postuler au défi</h1>
        <div class="encart mb-4">
          <h2 class="h5 text-lime"><?= e($defi['titre']) ?></h2>
          <p class="mb-2"><?= nl2br(e($defi['description'])) ?></p>
          <p class="small text-body-secondary mb-0">
            Défi lancé par <strong><?= e($defi['auteur_nom']) ?></strong>
            (Force : <?= (int)$defi['auteur_force'] ?> · Défense : <?= (int)$defi['auteur_defense'] ?>)
          </p>
        </div>

        <?php composant('resume_brute', ['brute' => $maBrute, 'libelle' => 'ta brute candidate']); ?>
        <?php composant('erreurs', ['erreurs' => $erreurs]); ?>

        <form method="post" action="postuler.php">
          <?= champCsrf() ?>
          <input type="hidden" name="defi_id" value="<?= (int)$defi['id'] ?>">
          <div class="mb-3">
            <label for="message" class="form-label fw-semibold">Court message d'accompagnement (optionnel)</label>
            <textarea class="form-control" id="message" name="message" rows="3" maxlength="255"
              placeholder="Ex : Je relève ton défi, prépare-toi !"><?= e($message) ?></textarea>
            <div class="form-text">255 caractères maximum.</div>
          </div>
          <div class="d-flex flex-wrap gap-2">
            <button type="submit" class="btn btn-lime">Confirmer ma candidature</button>
            <a href="<?= e($pageDefi) ?>" class="btn btn-arena-outline">Annuler</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
