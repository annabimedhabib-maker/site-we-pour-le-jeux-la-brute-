<?php
require __DIR__ . '/includes/init.php';
exigerConnexion(); // le détail d'un défi est réservé aux joueurs connectés

$id   = idDepuis('get', 'id');
$defi = $id ? defiAvecAuteur($pdo, $id) : null;
if (!$defi) {
    rediriger('defis.php', 'warning', 'Défi introuvable.');
}

$estOuvert     = $defi['etat'] === 'ouvert';
$maBrute       = maBrute($pdo, idJoueur());
$estAuteur     = $maBrute && (int)$maBrute['id'] === (int)$defi['brute_id'];
$maCandidature = ($maBrute && !$estAuteur) ? candidatureDeBrute($pdo, (int)$defi['id'], (int)$maBrute['id']) : null;
$candidatures  = candidaturesDefi($pdo, (int)$defi['id']);
$combat        = $estOuvert ? null : combatDuDefi($pdo, (int)$defi['id']);
$questions     = questionsDefi($pdo, (int)$defi['id']);

$titre_page = 'Défi : ' . $defi['titre'];
include __DIR__ . '/includes/header.php';
?>
<p><a href="defis.php" class="text-decoration-none">&larr; Retour à la liste des défis</a></p>

<div class="row g-4">
  <!-- Colonne gauche : le défi, son auteur et le résultat éventuel -->
  <div class="col-12 col-lg-5">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <?= badgeEtatDefi($defi['etat']) ?>
        <small class="text-body-secondary">Publié le <?= e(dateFr($defi['date_creation'])) ?></small>
      </div>
      <div class="card-body">
        <h1 class="h3 text-lime"><?= e($defi['titre']) ?></h1>
        <p><?= nl2br(e($defi['description'])) ?></p>
        <hr>
        <h2 class="h6 text-body-secondary text-uppercase mb-3">Auteur du défi</h2>
        <div class="d-flex align-items-center">
          <img class="avatar avatar-sm me-3" src="<?= e(urlAvatar($defi['auteur_avatar'])) ?>" alt="Avatar de <?= e($defi['auteur_nom']) ?>" width="56" height="56">
          <div>
            <a href="brute.php?id=<?= (int)$defi['brute_id'] ?>" class="fw-bold text-decoration-none"><?= e($defi['auteur_nom']) ?></a>
            <?php if ($estAuteur): ?><span class="badge text-bg-primary ms-1">C'est toi !</span><?php endif; ?>
            <div class="small text-body-secondary">
              PV : <?= (int)$defi['auteur_pv'] ?> · Force : <?= (int)$defi['auteur_force'] ?> · Défense : <?= (int)$defi['auteur_defense'] ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php if ($combat): ?>
      <div class="card carte-resultat mb-4">
        <div class="card-header fw-bold">Résultat du combat</div>
        <div class="card-body text-center">
          <p class="mb-2 small text-body-secondary">Combat résolu le <?= e(dateFr($combat['date_combat'])) ?>.</p>
          <div class="d-flex justify-content-around align-items-center my-3">
            <div>
              <img class="avatar avatar-sm mb-1" src="<?= e(urlAvatar($combat['avatar1'])) ?>" alt="" width="56" height="56">
              <div class="small fw-bold"><?= e($combat['nom1']) ?></div>
            </div>
            <span class="fs-4 fw-bold text-body-secondary">VS</span>
            <div>
              <img class="avatar avatar-sm mb-1" src="<?= e(urlAvatar($combat['avatar2'])) ?>" alt="" width="56" height="56">
              <div class="small fw-bold"><?= e($combat['nom2']) ?></div>
            </div>
          </div>
          <div class="alert alert-success fw-bold py-2 mb-3">Vainqueur : <?= e($combat['vainqueur_nom']) ?></div>
          <a class="btn btn-lime w-100" href="combat.php?id=<?= (int)$combat['id'] ?>">Voir le compte rendu complet</a>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <!-- Colonne droite : actions selon le rôle du joueur -->
  <div class="col-12 col-lg-7">

    <?php if ($estOuvert && $estAuteur): ?>
      <!-- AUTEUR : choix de l'adversaire -->
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h2 class="h5 mb-0">Candidatures reçues</h2>
          <span class="badge rounded-pill text-bg-light"><?= count($candidatures) ?></span>
        </div>
        <div class="card-body">
          <?php if (!$candidatures): ?>
            <p class="text-center text-body-secondary py-3 mb-0">Aucun adversaire n'a encore postulé. Reviens un peu plus tard !</p>
          <?php else: ?>
            <p class="text-body-secondary small">
              <strong>Attention :</strong> choisir un adversaire déclenche immédiatement le combat et clôt ce défi.
            </p>
            <div class="list-group">
              <?php foreach ($candidatures as $cand): ?>
                <div class="list-group-item p-3">
                  <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center">
                      <img class="avatar avatar-sm me-3" src="<?= e(urlAvatar($cand['avatar'])) ?>" alt="Avatar de <?= e($cand['nom']) ?>" width="56" height="56">
                      <div>
                        <a href="brute.php?id=<?= (int)$cand['brute_id'] ?>" class="fw-bold text-decoration-none"><?= e($cand['nom']) ?></a>
                        <div class="small text-body-secondary">
                          PV : <?= (int)$cand['pv'] ?> · Force : <?= (int)$cand['force_combat'] ?> · Défense : <?= (int)$cand['defense'] ?>
                        </div>
                        <?php if ($cand['message'] !== ''): ?>
                          <div class="citation small mt-1">« <?= e($cand['message']) ?> »</div>
                        <?php endif; ?>
                        <div class="text-body-secondary small mt-1">Postulé le <?= e(dateFr($cand['date_candidature'])) ?></div>
                      </div>
                    </div>
                    <form method="post" action="choisir_adversaire.php">
                      <?= champCsrf() ?>
                      <input type="hidden" name="defi_id" value="<?= (int)$defi['id'] ?>">
                      <input type="hidden" name="candidature_id" value="<?= (int)$cand['id'] ?>">
                      <button type="submit" class="btn btn-lime"
                        data-confirmation="Affronter <?= e($cand['nom']) ?> ? Le combat sera calculé immédiatement.">Choisir cet adversaire</button>
                    </form>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

    <?php elseif ($estOuvert): ?>
      <!-- AUTRE JOUEUR : candidature -->
      <?php if (!$maBrute): ?>
        <div class="card mb-4">
          <div class="card-body text-center py-4">
            <h2 class="h5">Tu n'as pas encore de brute !</h2>
            <p class="text-body-secondary">Crée ton personnage pour pouvoir postuler à ce défi.</p>
            <a href="creer_brute.php" class="btn btn-lime">Créer ma brute</a>
          </div>
        </div>
      <?php elseif ($maCandidature): ?>
        <div class="card mb-4">
          <div class="card-header fw-bold">Ta candidature a été envoyée</div>
          <div class="card-body">
            <p class="mb-2">Tu as proposé <strong><?= e($maBrute['nom']) ?></strong> le <?= e(dateFr($maCandidature['date_candidature'])) ?>.</p>
            <?php if ($maCandidature['message'] !== ''): ?>
              <p class="citation">« <?= e($maCandidature['message']) ?> »</p>
            <?php endif; ?>
            <p class="mb-0">Statut : <?= badgeStatutCandidature($maCandidature['statut']) ?> — l'auteur n'a pas encore choisi son adversaire.</p>
          </div>
        </div>
      <?php else: ?>
        <div class="card mb-4">
          <div class="card-header"><h2 class="h5 mb-0">Relever ce défi avec ta brute</h2></div>
          <div class="card-body">
            <?php composant('resume_brute', ['brute' => $maBrute, 'libelle' => 'ta brute']); ?>
            <form method="post" action="postuler.php">
              <?= champCsrf() ?>
              <input type="hidden" name="defi_id" value="<?= (int)$defi['id'] ?>">
              <div class="mb-3">
                <label for="message" class="form-label fw-semibold">Court message d'accompagnement (optionnel)</label>
                <input type="text" class="form-control" id="message" name="message" maxlength="255"
                  placeholder="Ex : Prépare-toi à mordre la poussière !">
                <div class="form-text">255 caractères maximum.</div>
              </div>
              <button type="submit" class="btn btn-lime">Proposer ma brute comme adversaire</button>
            </form>
          </div>
        </div>
      <?php endif; ?>

      <div class="card mb-4">
        <div class="card-header"><h2 class="h6 mb-0">Candidatures en cours (<?= count($candidatures) ?>)</h2></div>
        <div class="card-body">
          <?php if (!$candidatures): ?>
            <p class="text-body-secondary small mb-0">Aucun adversaire ne s'est encore manifesté.</p>
          <?php else: ?>
            <ul class="list-unstyled mb-0">
              <?php foreach ($candidatures as $cand): ?>
                <li class="d-flex align-items-center py-2 border-bottom">
                  <img class="avatar avatar-xs me-2" src="<?= e(urlAvatar($cand['avatar'])) ?>" alt="" width="40" height="40">
                  <a href="brute.php?id=<?= (int)$cand['brute_id'] ?>" class="fw-semibold me-2 text-decoration-none"><?= e($cand['nom']) ?></a>
                  <?php if ($cand['message'] !== ''): ?>
                    <span class="text-body-secondary small text-truncate">« <?= e($cand['message']) ?> »</span>
                  <?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>

    <?php else: ?>
      <!-- DÉFI TERMINÉ : récapitulatif des candidatures -->
      <?php if ($maCandidature): ?>
        <div class="alert alert-<?= $maCandidature['statut'] === 'acceptee' ? 'success' : 'secondary' ?>" role="status">
          <?= $maCandidature['statut'] === 'acceptee'
              ? 'Ta brute a été choisie pour ce combat ! Consulte le résultat ci-contre.'
              : 'Ta candidature n\'a pas été retenue pour ce défi.' ?>
        </div>
      <?php endif; ?>
      <div class="card mb-4">
        <div class="card-header"><h2 class="h5 mb-0">Historique des candidatures</h2></div>
        <div class="list-group list-group-flush">
          <?php foreach ($candidatures as $cand): ?>
            <div class="list-group-item d-flex justify-content-between align-items-center gap-2">
              <div class="d-flex align-items-center min-w-0">
                <img class="avatar avatar-xs me-2" src="<?= e(urlAvatar($cand['avatar'])) ?>" alt="" width="40" height="40">
                <div class="min-w-0">
                  <span class="fw-semibold"><?= e($cand['nom']) ?></span>
                  <?php if ($cand['message'] !== ''): ?>
                    <div class="small text-body-secondary text-truncate">« <?= e($cand['message']) ?> »</div>
                  <?php endif; ?>
                </div>
              </div>
              <?= badgeStatutCandidature($cand['statut']) ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- BONUS : questions / réponses -->
    <section class="card" id="questions" aria-labelledby="titre-questions">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h2 id="titre-questions" class="h5 mb-0">Questions à l'auteur</h2>
        <span class="badge rounded-pill text-bg-light"><?= count($questions) ?></span>
      </div>
      <div class="card-body">
        <?php if (!$questions): ?>
          <p class="text-body-secondary small">Aucune question pour l'instant.</p>
        <?php endif; ?>

        <?php foreach ($questions as $q): ?>
          <div class="question mb-3">
            <p class="mb-1">
              <strong><?= e($q['login']) ?></strong>
              <span class="small text-body-secondary">· <?= e(dateFr($q['date_question'])) ?></span>
            </p>
            <p class="mb-2"><?= nl2br(e($q['texte_question'])) ?></p>

            <?php if ($q['texte_reponse'] !== null): ?>
              <div class="reponse">
                <span class="small fw-semibold text-lime">Réponse de <?= e($defi['auteur_nom']) ?> :</span>
                <?= nl2br(e($q['texte_reponse'])) ?>
              </div>
            <?php elseif ($estAuteur && $estOuvert): ?>
              <form method="post" action="question.php" class="reponse">
                <?= champCsrf() ?>
                <input type="hidden" name="action" value="repondre">
                <input type="hidden" name="question_id" value="<?= (int)$q['id'] ?>">
                <label for="reponse<?= (int)$q['id'] ?>" class="form-label small">Ta réponse</label>
                <textarea class="form-control form-control-sm mb-2" id="reponse<?= (int)$q['id'] ?>" name="reponse" rows="2" maxlength="1000" required></textarea>
                <button type="submit" class="btn btn-lime btn-sm">Répondre</button>
              </form>
            <?php else: ?>
              <p class="small text-body-secondary fst-italic mb-0">En attente de réponse.</p>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>

        <?php if ($estOuvert && !$estAuteur): ?>
          <form method="post" action="question.php" class="mt-3">
            <?= champCsrf() ?>
            <input type="hidden" name="action" value="poser">
            <input type="hidden" name="defi_id" value="<?= (int)$defi['id'] ?>">
            <label for="nouvelle_question" class="form-label fw-semibold">Poser une question à l'auteur</label>
            <textarea class="form-control mb-2" id="nouvelle_question" name="question" rows="2" minlength="3" maxlength="500" required></textarea>
            <button type="submit" class="btn btn-arena-outline btn-sm">Envoyer ma question</button>
          </form>
        <?php endif; ?>
      </div>
    </section>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
