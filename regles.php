<?php
require __DIR__ . '/includes/init.php';

$titre_page = 'Règles du jeu';
include __DIR__ . '/includes/header.php';
?>
<h1 class="mb-4">Présentation et règles du jeu</h1>

<div class="row g-4">
  <section class="col-12 col-lg-6">
    <div class="card h-100">
      <div class="card-body">
        <h2 class="h4 text-lime">Le principe</h2>
        <p>La Brute est une arène où chaque joueur possède un personnage, sa <strong>brute</strong>, et participe à des combats automatiques. Il n’y a ni animation ni temps réel : chaque combat est résolu par le serveur et raconté dans un compte rendu.</p>
        <ol class="mb-0">
          <li>Crée un compte, puis <strong>ta brute</strong> (une seule par compte).</li>
          <li>Publie un <strong>défi</strong> ou réponds à celui d’un autre joueur avec ta brute.</li>
          <li>L’auteur du défi choisit <strong>un adversaire</strong> parmi les candidatures.</li>
          <li>Le combat est résolu automatiquement et son résultat reste consultable.</li>
        </ol>
      </div>
    </div>
  </section>

  <section class="col-12 col-lg-6">
    <div class="card h-100">
      <div class="card-body">
        <h2 class="h4 text-lime">Ta brute</h2>
        <ul class="mb-0">
          <li>Tu choisis son <strong>nom</strong>, sa <strong>description</strong> et son <strong>avatar</strong>.</li>
          <li>Ses caractéristiques sont attribuées par le serveur : <strong>30 points de vie</strong>, une <strong>force</strong> tirée au hasard entre 5 et 8, et une <strong>défense</strong> égale à 10 moins la force.</li>
          <li>Tu peux modifier ta description et ton avatar, mais pas tes caractéristiques ni tes résultats.</li>
        </ul>
      </div>
    </div>
  </section>

  <section class="col-12 col-lg-6">
    <div class="card h-100">
      <div class="card-body">
        <h2 class="h4 text-lime">Les défis</h2>
        <ul class="mb-0">
          <li>Un joueur ne peut avoir <strong>qu’un seul défi actif</strong> à la fois.</li>
          <li>Tu ne peux pas répondre à ton propre défi, ni candidater deux fois au même défi.</li>
          <li>Seuls les défis <strong>ouverts</strong> acceptent des candidatures.</li>
          <li>Le choix d’un adversaire clôt le défi : la candidature choisie est acceptée, les autres ne sont pas retenues.</li>
          <li>Un défi terminé ne peut plus être modifié ni donner lieu à un nouveau combat.</li>
          <li>Les candidats peuvent poser des <strong>questions</strong> à l’auteur d’un défi ouvert, qui peut y répondre.</li>
        </ul>
      </div>
    </div>
  </section>

  <section class="col-12 col-lg-6">
    <div class="card h-100">
      <div class="card-body">
        <h2 class="h4 text-lime">Le déroulement d’un combat</h2>
        <ol>
          <li>Les deux brutes commencent avec leurs <strong>30 points de vie</strong>.</li>
          <li>Le serveur tire au hasard celle qui attaque en premier.</li>
          <li>Les brutes attaquent à tour de rôle. Les dégâts valent la <strong>force de l’attaquant</strong>, plus un nombre aléatoire entre 0 et 2, moins la <strong>défense de l’adversaire</strong>. Chaque attaque inflige <strong>au moins 1 point de dégât</strong>.</li>
          <li>Le combat s’arrête dès qu’une brute n’a plus de points de vie : l’autre remporte le combat.</li>
        </ol>
        <p class="mb-0 text-body-secondary">Les points de vie perdus ne comptent que pour le combat en cours : les caractéristiques de ta brute ne changent jamais.</p>
      </div>
    </div>
  </section>
</div>

<?php if (!estConnecte()): ?>
  <p class="mt-4 d-flex flex-wrap gap-2">
    <a class="btn btn-lime" href="inscription.php">Créer un compte</a>
    <a class="btn btn-arena-outline" href="connexion.php">Se connecter</a>
  </p>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
