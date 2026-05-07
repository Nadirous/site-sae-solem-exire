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
    <ul class="nav-links">
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
