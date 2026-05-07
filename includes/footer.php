<footer class="footer">
    <div class="footer-inner">
        <div class="footer-col">
            <h3>🍺 Solem Exire</h3>
            <p>Bières artisanales brassées avec passion.<br>Goût, caractère et authenticité.</p>
        </div>
        <div class="footer-col">
            <h4>Navigation</h4>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="produits.php">Nos Bières</a></li>
                <li><a href="sav.php">Service client</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Mon Compte</h4>
            <ul>
                <?php if (isset($_SESSION['compte_id'])): ?>
                    <li><a href="dashboard.php">Tableau de bord</a></li>
                    <li><a href="panier.php">Mon panier</a></li>
                    <li><a href="deconnexion.php">Déconnexion</a></li>
                <?php else: ?>
                    <li><a href="connexion.php">Connexion</a></li>
                    <li><a href="inscription.php">Créer un compte</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> Solem Exire – Tous droits réservés. L'abus d'alcool est dangereux pour la santé.</p>
    </div>
</footer>

</body>
</html>
