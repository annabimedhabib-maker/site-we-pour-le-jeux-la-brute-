<?php /* Gabarit commun : fin de page. */ defined('LABRUTE') or exit; ?>
  </main>

  <footer class="footer-arena py-4 mt-auto">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center small text-body-secondary gap-2">
      <span>&copy; 2026 La Brute – Hachaichi &amp; Annabi – Conception de sites web, ESIGELEC</span>
      <nav class="d-flex gap-3" aria-label="Liens du pied de page">
        <a class="link-secondary text-decoration-none" href="<?= e(url('regles.php')) ?>">Règles du jeu</a>
        <a class="link-secondary text-decoration-none" href="<?= e(url('brutes.php')) ?>">Les brutes</a>
        <a class="link-secondary text-decoration-none" href="<?= e(url('defis.php')) ?>">Les défis</a>
      </nav>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= e(url('js/site.js')) ?>"></script>
</body>

</html>
