<?php
require __DIR__ . '/includes/init.php';

// Aperçu : 4 dernières brutes et 3 derniers défis ouverts
$dernieresBrutes = $pdo->query('SELECT id, nom, avatar, force_combat, defense FROM brute ORDER BY date_creation DESC, id DESC LIMIT 4')->fetchAll();
$defisOuverts    = listeDefis($pdo, 'ouvert', 3);

$titre_page = 'Accueil';
include __DIR__ . '/includes/header.php';
?>

<!-- Bienvenue (2 colonnes sur PC, 1 colonne sur mobile) -->
<section class="row align-items-center g-5 py-3 mb-5">
  <div class="col-12 col-lg-7">
    <p class="eyebrow-lime mb-3">Stratégie simple. Rivalité éternelle.</p>
    <h1 class="hero-title mb-4">Une brute. Un défi. <span>Ta légende.</span></h1>
    <p class="hero-texte text-body-secondary fs-5 mb-4">
      Crée ton combattant, choisis ton adversaire et laisse l'arène décider. Chaque duel écrit une nouvelle histoire.
    </p>
    <div class="d-flex flex-wrap gap-3 mb-3">
      <?php if (estConnecte()): ?>
        <a href="mon_espace.php" class="btn btn-lime">Accéder à mon espace &rarr;</a>
      <?php else: ?>
        <a href="inscription.php" class="btn btn-lime">Créer un compte &rarr;</a>
        <a href="connexion.php" class="btn btn-arena-outline">Se connecter</a>
      <?php endif; ?>
      <a href="defis.php" class="btn btn-arena-outline">Explorer les défis</a>
    </div>
    <p class="small text-body-secondary mb-0">Combats automatiques &middot; Sans connexion simultanée</p>
  </div>

  <div class="col-12 col-lg-5">
    <div class="arena-arch shadow-lg">
      <p class="eyebrow-lime arena-arch-titre mb-4">Le prochain duel t'attend</p>
      <svg viewBox="0 0 260 190" width="240" height="175" role="img" aria-label="Deux chevaliers prêts au duel">
        <ellipse cx="130" cy="172" rx="95" ry="12" fill="none" stroke="#4c5c38" stroke-width="1.5" />
        <polygon points="85,35 75,55 95,55" fill="#f4f6f2" />
        <rect x="65" y="55" width="42" height="36" rx="6" fill="#d2f854" />
        <rect x="78" y="68" width="28" height="5" fill="#171a15" />
        <rect x="92" y="60" width="5" height="24" fill="#171a15" />
        <polygon points="55,95 110,95 120,165 45,165" fill="#465734" />
        <rect x="118" y="72" width="7" height="55" rx="2" fill="#d2f854" />
        <polygon points="175,35 165,55 185,55" fill="#f4f6f2" />
        <rect x="153" y="55" width="42" height="36" rx="6" fill="#9fb0a4" />
        <rect x="154" y="68" width="28" height="5" fill="#171a15" />
        <rect x="163" y="60" width="5" height="24" fill="#171a15" />
        <polygon points="150,95 205,95 215,165 140,165" fill="#3a4538" />
        <rect x="135" y="72" width="7" height="55" rx="2" fill="#9fb0a4" />
        <polygon points="133,82 124,102 131,102 127,118 138,98 131,98" fill="#d2f854" />
      </svg>
      <div class="arena-arch-pied d-flex justify-content-around pt-3 mt-3">
        <span class="fw-bold">30 PV</span>
        <span class="fw-bold">1 VAINQUEUR</span>
        <span class="fw-bold">TON HISTOIRE</span>
      </div>
    </div>
  </div>
</section>

<!-- Comment jouer ? -->
<section class="mb-5" aria-labelledby="titre-etapes">
  <div class="d-flex justify-content-between align-items-end mb-3">
    <h2 id="titre-etapes" class="h3 fw-bold mb-0">Comment jouer ?</h2>
    <a href="regles.php" class="fw-semibold small text-decoration-none">Lire les règles &rarr;</a>
  </div>
  <div class="row g-4">
    <?php
    $etapes = [
        ['01', 'Crée ta brute', 'Un nom, un avatar et des caractéristiques attribuées par le serveur.'],
        ['02', "Entre dans l'arène", 'Publie un défi ou propose ta brute à un autre joueur.'],
        ['03', 'Découvre le verdict', 'Un combat automatique, un résultat conservé, une rivalité de plus.'],
    ];
    ?>
    <?php foreach ($etapes as [$numero, $titre, $texte]): ?>
      <div class="col-12 col-md-4">
        <div class="step-box d-flex gap-3">
          <span class="step-num"><?= $numero ?></span>
          <div>
            <h3 class="h6 fw-bold mb-1"><?= e($titre) ?></h3>
            <p class="small text-body-secondary mb-0"><?= e($texte) ?></p>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Aperçu : dernières brutes | défis ouverts -->
<div class="row g-5">
  <section class="col-12 col-lg-6" aria-labelledby="titre-brutes">
    <div class="d-flex justify-content-between align-items-end mb-3">
      <div>
        <p class="eyebrow-lime mb-1">Ils ont rejoint l'arène</p>
        <h2 id="titre-brutes" class="h3 fw-bold mb-0">Les dernières brutes</h2>
      </div>
      <a href="brutes.php" class="text-decoration-none fw-semibold small">Toutes les brutes &rarr;</a>
    </div>

    <?php if (!$dernieresBrutes): ?>
      <p class="text-body-secondary">Aucune brute n'est encore entrée dans l'arène.</p>
    <?php else: ?>
      <div class="row g-3">
        <?php foreach ($dernieresBrutes as $b): ?>
          <div class="col-6">
            <div class="card h-100 p-3 text-center">
              <img class="avatar mx-auto mb-2" src="<?= e(urlAvatar($b['avatar'])) ?>" alt="Avatar de <?= e($b['nom']) ?>" width="84" height="84">
              <h3 class="h6 fw-bold mb-1 text-truncate"><?= e($b['nom']) ?></h3>
              <p class="small text-body-secondary mb-0">
                Force <strong class="text-body"><?= (int)$b['force_combat'] ?></strong> &middot;
                Défense <strong class="text-body"><?= (int)$b['defense'] ?></strong>
              </p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <section class="col-12 col-lg-6" aria-labelledby="titre-defis">
    <div class="d-flex justify-content-between align-items-end mb-3">
      <div>
        <p class="eyebrow-lime mb-1">En quête d'adversaire</p>
        <h2 id="titre-defis" class="h3 fw-bold mb-0">Défis ouverts</h2>
      </div>
      <a href="defis.php" class="text-decoration-none fw-semibold small">Tous les défis &rarr;</a>
    </div>

    <?php if (!$defisOuverts): ?>
      <p class="text-body-secondary">Aucun défi ouvert pour le moment.</p>
    <?php else: ?>
      <div class="list-group">
        <?php foreach ($defisOuverts as $d): ?>
          <a class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3"
            href="defi.php?id=<?= (int)$d['id'] ?>">
            <img class="avatar avatar-xs" src="<?= e(urlAvatar($d['auteur_avatar'])) ?>" alt="" width="40" height="40">
            <span class="flex-grow-1 min-w-0">
              <span class="d-block fw-semibold text-truncate"><?= e($d['titre']) ?></span>
              <span class="d-block small text-body-secondary">par <?= e($d['auteur_nom']) ?> · <?= pluriel((int)$d['nb_candidatures'], 'candidature') ?></span>
            </span>
            <span aria-hidden="true">&rarr;</span>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
