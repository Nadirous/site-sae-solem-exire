<?php
require_once 'config/db.php';
require_once 'includes/header.php';

// Récupérer les 3 derniers produits en stock
$stmt = $pdo->query("SELECT * FROM produits WHERE stock > 0 ORDER BY id DESC LIMIT 3");
$produits_vedette = $stmt->fetchAll();

// Récupérer les avis récents
$stmt = $pdo->query("
    SELECT a.etoiles_client, a.text_avis, c.Utilisateur
    FROM avis a
    JOIN compte c ON c.id = a.id_client
    ORDER BY a.id DESC
    LIMIT 6
");
$avis = $stmt->fetchAll();
?>

<main>
    <!-- Hero -->
    <section class="hero">
        <span class="hero-badge">🌾 Brasserie Artisanale</span>
        <h1>L'excellence <span>en bouteille</span></h1>
        <p>Solem Exire, c'est l'art de brasser des bières aux caractères uniques, avec des ingrédients soigneusement sélectionnés et un savoir-faire transmis avec passion.</p>
        <div class="hero-actions">
            <a href="produits.php" class="btn btn-primary">Découvrir nos bières</a>
            <a href="inscription.php" class="btn btn-outline">Créer un compte</a>
        </div>
    </section>

    <!-- Caractéristiques -->
    <section class="section" style="background: #0d0d0d;">
        <p class="section-title">Pourquoi <span>Solem Exire</span> ?</p>
        <p class="section-sub">Ce qui nous distingue</p>
        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">🌿</div>
                <h3>Ingrédients naturels</h3>
                <p>Houblon, malt et levures sélectionnés pour des saveurs authentiques sans compromis.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🍺</div>
                <h3>Multiples saveurs</h3>
                <p>Des recettes uniques pour chaque goût : amère, fruitée, légère ou corsée.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🚚</div>
                <h3>Livraison rapide</h3>
                <p>Commandez en ligne, recevez vos bières directement chez vous en 24-48h.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⭐</div>
                <h3>Avis clients</h3>
                <p>Une communauté passionnée qui partage ses découvertes et ses coups de cœur.</p>
            </div>
        </div>
    </section>

    <!-- Produits vedette -->
    <?php if (!empty($produits_vedette)): ?>
    <section class="section">
        <p class="section-title">Nos <span>Bières</span> du moment</p>
        <p class="section-sub">Directement disponibles en stock</p>
        <div class="products-grid">
            <?php foreach ($produits_vedette as $p): ?>
                <div class="product-card">
                    <?php if (!empty($p['photo']) && file_exists('assets/images/' . $p['photo'])): ?>
                        <img class="product-img" src="assets/images/<?= htmlspecialchars($p['photo']) ?>" alt="<?= htmlspecialchars($p['nom']) ?>">
                    <?php else: ?>
                        <div class="product-img-placeholder">🍺</div>
                    <?php endif; ?>
                    <div class="product-body">
                        <div class="product-name"><?= htmlspecialchars($p['nom']) ?></div>
                        <div class="product-desc"><?= htmlspecialchars($p['description']) ?></div>
                        <div class="product-footer">
                            <span class="product-price"><?= number_format($p['prix'], 2) ?> €</span>
                            <a href="produit.php?id=<?= $p['id'] ?>" class="btn btn-primary btn-sm">Voir</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align:center;margin-top:2.5rem;">
            <a href="produits.php" class="btn btn-outline">Voir toute la carte →</a>
        </div>
    </section>
    <?php endif; ?>

    <!-- Avis clients -->
    <?php if (!empty($avis)): ?>
    <section class="section" style="background: #0d0d0d;">
        <p class="section-title">Ce que disent <span>nos clients</span></p>
        <p class="section-sub">Avis authentiques de notre communauté</p>
        <div class="reviews-grid">
            <?php foreach ($avis as $a): ?>
                <div class="review-card">
                    <div class="review-header">
                        <span class="reviewer"><?= htmlspecialchars($a['Utilisateur']) ?></span>
                        <span class="stars"><?= str_repeat('★', max(1, min(5, $a['etoiles_client']))) ?></span>
                    </div>
                    <div class="review-text">"<?= htmlspecialchars($a['text_avis']) ?>"</div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA bas de page -->
    <section class="section" style="text-align:center;">
        <p class="section-title">Prêt à commander ?</p>
        <p class="section-sub">Créez votre compte et profitez de toutes nos bières artisanales.</p>
        <?php if (!isset($_SESSION['compte_id'])): ?>
            <a href="inscription.php" class="btn btn-primary" style="margin-right:.8rem;">Créer un compte</a>
            <a href="connexion.php" class="btn btn-outline">Se connecter</a>
        <?php else: ?>
            <a href="produits.php" class="btn btn-primary">Commander maintenant</a>
        <?php endif; ?>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>
