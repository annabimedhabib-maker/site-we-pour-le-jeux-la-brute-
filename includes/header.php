<?php
/* Gabarit commun : début de page. Définir $titre_page avant l'include. */
defined('LABRUTE') or exit;

$page      = basename($_SERVER['SCRIPT_NAME']);
$dansAdmin = RACINE !== '';
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($titre_page ?? 'Accueil') ?> – La Brute</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= e(url('css/style.css')) ?>">
  <link rel="icon" href="<?= e(url('img/logo_brute.webp')) ?>">
</head>

<body class="d-flex flex-column min-vh-100">
  <header>
    <nav class="navbar navbar-expand-lg navbar-arena" aria-label="Navigation principale">
      <div class="container">
        <a class="navbar-brand d-flex align-items-center fw-bold" href="<?= e(url('index.php')) ?>">
          <img class="brand-logo" src="<?= e(url('img/logo_brute.webp')) ?>" alt="" width="38" height="38">
          <span>LA BRUTE</span>
          <span class="brand-sous-titre ms-2 ps-2">L'ARÈNE</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal"
          aria-controls="menuPrincipal" aria-expanded="false" aria-label="Afficher le menu">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menuPrincipal">
          <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
            <li class="nav-item"><?= lienNav('index.php', 'Accueil', !$dansAdmin && $page === 'index.php') ?></li>
            <li class="nav-item"><?= lienNav('regles.php', 'Règles', $page === 'regles.php') ?></li>
            <li class="nav-item"><?= lienNav('brutes.php', 'Brutes', in_array($page, ['brutes.php', 'brute.php'], true)) ?></li>
            <li class="nav-item"><?= lienNav('defis.php', 'Défis', !$dansAdmin && in_array($page, ['defis.php', 'defi.php', 'creer_defi.php', 'postuler.php', 'combat.php'], true)) ?></li>

            <?php if (estConnecte()): ?>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle<?= in_array($page, ['mon_espace.php', 'ma_brute.php', 'creer_brute.php'], true) ? ' active' : '' ?>"
                  href="<?= e(url('mon_espace.php')) ?>" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Mon espace (<?= e($_SESSION['login']) ?>)
                </a>
                <ul class="dropdown-menu dropdown-menu-lg-end">
                  <li><a class="dropdown-item" href="<?= e(url('mon_espace.php')) ?>">Tableau de bord</a></li>
                  <li><a class="dropdown-item" href="<?= e(url('ma_brute.php')) ?>">Ma brute</a></li>
                  <li><a class="dropdown-item" href="<?= e(url('mon_espace.php#mes-defis')) ?>">Mes défis</a></li>
                  <li><a class="dropdown-item" href="<?= e(url('mon_espace.php#mes-candidatures')) ?>">Mes candidatures</a></li>
                  <li><a class="dropdown-item" href="<?= e(url('mon_espace.php#mes-combats')) ?>">Mes combats</a></li>
                </ul>
              </li>
              <?php if (estAdmin()): ?>
                <li class="nav-item"><?= lienNav('admin/index.php', 'Administration', $dansAdmin) ?></li>
              <?php endif; ?>
              <li class="nav-item ms-lg-2">
                <a class="btn btn-arena-outline btn-sm" href="<?= e(url('deconnexion.php')) ?>">Déconnexion</a>
              </li>
            <?php else: ?>
              <li class="nav-item"><?= lienNav('connexion.php', 'Connexion', $page === 'connexion.php') ?></li>
              <li class="nav-item ms-lg-2">
                <a class="btn btn-lime btn-sm" href="<?= e(url('inscription.php')) ?>">Rejoindre l'arène</a>
              </li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <main class="container my-5 flex-grow-1">
    <?php foreach ($_SESSION['flash'] ?? [] as $flash): ?>
      <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
        <?= e($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
      </div>
    <?php endforeach; ?>
    <?php unset($_SESSION['flash']); ?>
