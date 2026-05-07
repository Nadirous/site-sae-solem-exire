<?php
$titre_page = 'Nos Bières';
require_once 'config/db.php';
require_once 'includes/header.php';

// Recherche simple
$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE nom LIKE ? OR description LIKE ? ORDER BY nom");
    $like = '%' . $search . '%';
    $stmt->execute([$like, $like]);
} else {
    $stmt = $pdo->query("SELECT * FROM produits ORDER BY nom");
}
$produits = $stmt->fetchAll();
?>

<main>
    <div class="page-header">
        <h1>Nos <span style="color:var(--accent)">Bières</span></h1>
        <p>Découvrez toutes nos recettes artisanales</p>
    </div>

    <div class="section">
        <!-- Barre de recherche -->
        <form method="GET" style="max-width:500px;margin:0 auto 2.5rem;display:flex;gap:.6rem;">
            <div class="form-group" style="flex:1;margin:0;">
                <input type="text" name="q" placeholder="Rechercher une bière..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <button type="submit" class="btn btn-primary">Rechercher</button>
            <?php if ($search): ?>
                <a href="produits.php" class="btn btn-outline">✕</a>
            <?php endif; ?>
        </form>

        <?php if (empty($produits)): ?>
            <div class="empty-state">
                <div class="empty-icon">🔍</div>
                <p>Aucune bière trouvée pour "<?= htmlspecialchars($search) ?>".</p>
                <a href="produits.php" class="btn btn-outline" style="margin-top:1rem;">Voir toutes les bières</a>
            </div>
        <?php else: ?>
            <?php if ($search): ?>
                <p style="color:var(--text-muted);margin-bottom:1.5rem;text-align:center;">
                    <?= count($produits) ?> résultat(s) pour "<?= htmlspecialchars($search) ?>"
                </p>
            <?php endif; ?>
            <div class="products-grid">
                <?php foreach ($produits as $p): ?>
                    <div class="product-card">
                        <?php if (!empty($p['photo']) && file_exists('assets/images/' . $p['photo'])): ?>
                            <img class="product-img" src="assets/images/<?= htmlspecialchars($p['photo']) ?>" alt="<?= htmlspecialchars($p['nom']) ?>">
                        <?php else: ?>
                            <div class="product-img-placeholder">🍺</div>
                        <?php endif; ?>
                        <div class="product-body">
                            <div class="product-name"><?= htmlspecialchars($p['nom']) ?></div>
                            <div class="product-desc"><?= htmlspecialchars($p['description']) ?></div>
                            <?php if ($p['etoiles']): ?>
                                <div class="stars" style="margin-bottom:.4rem;">
                                    <?= str_repeat('★', max(1, min(5, $p['etoiles']))) ?>
                                </div>
                            <?php endif; ?>
                            <div class="product-footer">
                                <span class="product-price"><?= number_format($p['prix'], 2) ?> €</span>
                                <?php if ($p['stock'] > 5): ?>
                                    <span class="badge badge-green">En stock</span>
                                <?php elseif ($p['stock'] > 0): ?>
                                    <span class="badge badge-orange">Stock limité</span>
                                <?php else: ?>
                                    <span class="badge badge-red">Épuisé</span>
                                <?php endif; ?>
                            </div>
                            <a href="produit.php?id=<?= $p['id'] ?>" class="btn btn-primary" style="width:100%;margin-top:.8rem;">Voir le produit</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
