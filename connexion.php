<?php
require __DIR__ . '/includes/init.php';

if (estConnecte()) {
    rediriger('mon_espace.php');
}

$erreur = '';
$login  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = texteDepuisPost('login');
    $mdp   = (string)($_POST['mot_de_passe'] ?? '');

    $req = $pdo->prepare('SELECT id, login, mot_de_passe, role, actif FROM joueur WHERE login = :login');
    $req->execute([':login' => $login]);
    $joueur = $req->fetch();

    // Même message pour "login inconnu" et "mauvais mot de passe" (on ne révèle rien)
    if (!$joueur || !password_verify($mdp, $joueur['mot_de_passe'])) {
        $erreur = 'Login ou mot de passe incorrect.';
    } elseif (!(int)$joueur['actif']) {
        $erreur = 'Ce compte a été désactivé par un administrateur.';
    } else {
        $pageDemandee = $_SESSION['page_demandee'] ?? '';
        ouvrirSession($joueur);
        unset($_SESSION['page_demandee']);
        flash('success', 'Bienvenue dans l\'arène, ' . $joueur['login'] . ' !');

        // Retour vers la page qui avait demandé la connexion (chemin local uniquement)
        if (is_string($pageDemandee) && preg_match('#^/[^/\\\\]#', $pageDemandee)) {
            header('Location: ' . $pageDemandee);
            exit;
        }
        rediriger('mon_espace.php');
    }
}

$titre_page = 'Connexion';
include __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-12 col-sm-10 col-md-8 col-lg-5">
    <div class="card">
      <div class="card-body p-4">
        <h1 class="h3 mb-3">Connexion</h1>
        <?php composant('erreurs', ['erreurs' => $erreur ? [$erreur] : []]); ?>
        <form method="post" action="connexion.php">
          <?= champCsrf() ?>
          <div class="mb-3">
            <label for="login" class="form-label">Login</label>
            <input type="text" class="form-control" id="login" name="login" value="<?= e($login) ?>" autocomplete="username" required>
          </div>
          <div class="mb-4">
            <label for="mot_de_passe" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" autocomplete="current-password" required>
          </div>
          <button type="submit" class="btn btn-lime w-100">Se connecter</button>
        </form>
      </div>
    </div>
    <p class="mt-3 text-center">Pas encore de compte ? <a href="inscription.php">Inscris-toi</a></p>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
