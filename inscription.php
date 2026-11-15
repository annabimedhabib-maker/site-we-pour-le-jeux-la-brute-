<?php
require __DIR__ . '/includes/init.php';

if (estConnecte()) {
    rediriger('mon_espace.php');
}

$erreurs = [];
$login   = '';
$email   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = texteDepuisPost('login');
    $email = texteDepuisPost('email');
    $mdp   = (string)($_POST['mot_de_passe'] ?? '');
    $mdp2  = (string)($_POST['mot_de_passe2'] ?? '');

    if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $login)) {
        $erreurs[] = 'Le login doit faire 3 à 30 caractères (lettres, chiffres, _).';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
        $erreurs[] = 'Adresse email invalide.';
    }
    if (strlen($mdp) < 8) {
        $erreurs[] = 'Le mot de passe doit faire au moins 8 caractères.';
    }
    if ($mdp !== $mdp2) {
        $erreurs[] = 'Les deux mots de passe sont différents.';
    }

    if (!$erreurs) {
        // Unicité du login et de l'email
        $req = $pdo->prepare('SELECT login, email FROM joueur WHERE login = :login OR email = :email');
        $req->execute([':login' => $login, ':email' => $email]);
        foreach ($req->fetchAll() as $existant) {
            if (strcasecmp($existant['login'], $login) === 0) {
                $erreurs[] = 'Ce login est déjà utilisé.';
            }
            if (strcasecmp($existant['email'], $email) === 0) {
                $erreurs[] = 'Cet email est déjà utilisé.';
            }
        }
    }

    if (!$erreurs) {
        $req = $pdo->prepare('INSERT INTO joueur (login, email, mot_de_passe) VALUES (:login, :email, :mdp)');
        $req->execute([
            ':login' => $login,
            ':email' => $email,
            ':mdp'   => password_hash($mdp, PASSWORD_BCRYPT), // bcrypt = blowfish
        ]);
        rediriger('connexion.php', 'success', 'Compte créé avec succès ! Tu peux maintenant te connecter.');
    }
}

$titre_page = 'Inscription';
include __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-12 col-sm-10 col-md-8 col-lg-5">
    <div class="card">
      <div class="card-body p-4">
        <h1 class="h3 mb-3">Créer un compte</h1>
        <?php composant('erreurs', ['erreurs' => $erreurs]); ?>
        <form method="post" action="inscription.php">
          <?= champCsrf() ?>
          <div class="mb-3">
            <label for="login" class="form-label">Login</label>
            <input type="text" class="form-control" id="login" name="login" value="<?= e($login) ?>"
              minlength="3" maxlength="30" pattern="[A-Za-z0-9_]{3,30}" autocomplete="username" required>
            <div class="form-text">3 à 30 caractères : lettres, chiffres ou _.</div>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= e($email) ?>" maxlength="100" autocomplete="email" required>
          </div>
          <div class="mb-3">
            <label for="mot_de_passe" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" minlength="8" autocomplete="new-password" required>
            <div class="form-text">8 caractères minimum.</div>
          </div>
          <div class="mb-4">
            <label for="mot_de_passe2" class="form-label">Confirmer le mot de passe</label>
            <input type="password" class="form-control" id="mot_de_passe2" name="mot_de_passe2" minlength="8" autocomplete="new-password" required>
          </div>
          <button type="submit" class="btn btn-lime w-100">Créer mon compte</button>
        </form>
      </div>
    </div>
    <p class="mt-3 text-center">Déjà inscrit ? <a href="connexion.php">Se connecter</a></p>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
