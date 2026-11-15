// Scripts communs à toutes les pages (chargé par includes/footer.php).

// Demande une confirmation avant d'envoyer un formulaire dont le bouton porte data-confirmation="…"
// (choix d'un adversaire, suppression d'un défi, désactivation d'un compte…)
document.addEventListener('submit', function (evenement) {
  var bouton = evenement.submitter || evenement.target.querySelector('[data-confirmation]');
  if (bouton && bouton.dataset.confirmation && !window.confirm(bouton.dataset.confirmation)) {
    evenement.preventDefault();
  }
});
