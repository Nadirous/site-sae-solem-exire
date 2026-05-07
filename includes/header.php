<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$page_courante = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($titre_page) ? htmlspecialchars($titre_page) . ' – Solem Exire' : 'Solem Exire – Bières Artisanales' ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <a class="nav-brand" href="index.php">🍺 Solem Exire</a>

    <button class="nav-toggle" id="navToggle" aria-label="Menu" aria-expanded="false">
        <span></span><span></span><span></span>
    </button>

    <ul class="nav-links" id="navLinks">
        <li><a href="index.php" <?= $page_courante === 'index.php' ? 'class="active"' : '' ?>>Accueil</a></li>
        <li><a href="produits.php" <?= $page_courante === 'produits.php' ? 'class="active"' : '' ?>>Nos Bières</a></li>
        <?php if (isset($_SESSION['compte_id'])): ?>
            <li><a href="panier.php" <?= $page_courante === 'panier.php' ? 'class="active"' : '' ?>>🛒 Panier</a></li>
            <li><a href="dashboard.php" <?= $page_courante === 'dashboard.php' ? 'class="active"' : '' ?>>Mon Compte</a></li>
            <li><a href="deconnexion.php" class="btn-nav">Déconnexion</a></li>
        <?php else: ?>
            <li><a href="connexion.php" <?= $page_courante === 'connexion.php' ? 'class="active"' : '' ?>>Connexion</a></li>
            <li><a href="inscription.php" class="btn-nav">Inscription</a></li>
        <?php endif; ?>
    </ul>
</nav>

<script>
(function () {
    var btn   = document.getElementById('navToggle');
    var menu  = document.getElementById('navLinks');
    btn.addEventListener('click', function () {
        var open = menu.classList.toggle('open');
        btn.classList.toggle('open', open);
        btn.setAttribute('aria-expanded', open);
    });
    // Fermer le menu si on clique sur un lien
    menu.querySelectorAll('a').forEach(function (a) {
        a.addEventListener('click', function () {
            menu.classList.remove('open');
            btn.classList.remove('open');
            btn.setAttribute('aria-expanded', false);
        });
    });
})();
</script>
