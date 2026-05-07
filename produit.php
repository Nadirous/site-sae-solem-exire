<?php
require_once 'config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: produits.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
$stmt->execute([$id]);
$produit = $stmt->fetch();

if (!$produit) {
    header('Location: produits.php');
    exit;
}

$titre_page = $produit['nom'];
$message = '';
$erreur  = '';

// Ajout au panier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_panier'])) {
    if (!isset($_SESSION['compte_id'])) {
        header('Location: connexion.php?redirect=' . urlencode('produit.php?id=' . $id));
        exit;
    }
    $quantite = max(1, (int)$_POST['quantite']);
    if ($quantite > $produit['stock']) {
        $erreur = 'Stock insuffisant (disponible : ' . $produit['stock'] . ').';
    } else {
        // Si déjà dans le panier, mettre à jour la quantité
        $check = $pdo->prepare("SELECT id, quantite FROM panier WHERE id_compte = ? AND nom_produit = ?");
        $check->execute([$_SESSION['compte_id'], $produit['nom']]);
        $existant = $check->fetch();

        if ($existant) {
            $new_qty = $existant['quantite'] + $quantite;
            $upd = $pdo->prepare("UPDATE panier SET quantite = ? WHERE id = ?");
            $upd->execute([$new_qty, $existant['id']]);
        } else {
            $ins = $pdo->prepare("INSERT INTO panier (id_compte, nom_produit, quantite, prix) VALUES (?, ?, ?, ?)");
            $ins->execute([$_SESSION['compte_id'], $produit['nom'], $quantite, $produit['prix']]);
        }
        $message = 'Produit ajouté au panier !';
    }
}

require_once 'includes/header.php';
?>

<main>
    <div style="max-width:1100px;margin:1.5rem auto;padding:0 2rem;">
        <a href="produits.php" style="color:var(--text-muted);font-size:.9rem;">← Retour au catalogue</a>
    </div>

    <?php if ($message): ?>
        <div class="container" style="margin-top:1rem;">
            <div class="alert alert-success"><?= htmlspecialchars($message) ?> <a href="panier.php">Voir le panier →</a></div>
        </div>
    <?php endif; ?>
    <?php if ($erreur): ?>
        <div class="container" style="margin-top:1rem;">
            <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
        </div>
    <?php endif; ?>

    <div class="product-detail">
        <!-- Image -->
        <div>
            <?php if (!empty($produit['photo']) && file_exists('assets/images/' . $produit['photo'])): ?>
                <img class="product-detail-img" src="assets/images/<?= htmlspecialchars($produit['photo']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>">
            <?php else: ?>
                <div class="product-detail-placeholder">🍺</div>
            <?php endif; ?>
        </div>

        <!-- Infos -->
        <div class="product-detail-info">
            <h1><?= htmlspecialchars($produit['nom']) ?></h1>

            <?php if ($produit['etoiles']): ?>
                <div class="stars" style="font-size:1.2rem;">
                    <?= str_repeat('★', max(1, min(5, $produit['etoiles']))) ?>
                    <span style="color:var(--text-muted);font-size:.85rem;margin-left:.4rem;">(note globale)</span>
                </div>
            <?php endif; ?>

            <div class="product-detail-price"><?= number_format($produit['prix'], 2) ?> €</div>

            <?php if ($produit['stock'] > 5): ?>
                <span class="badge badge-green">✓ En stock (<?= $produit['stock'] ?> unités)</span>
            <?php elseif ($produit['stock'] > 0): ?>
                <span class="badge badge-orange">⚠ Stock limité (<?= $produit['stock'] ?> unités)</span>
            <?php else: ?>
                <span class="badge badge-red">✗ Épuisé</span>
            <?php endif; ?>

            <div class="info-block">
                <h4>Description</h4>
                <p><?= nl2br(htmlspecialchars($produit['description'])) ?></p>
            </div>

            <div class="info-block">
                <h4>Ingrédients</h4>
                <p><?= nl2br(htmlspecialchars($produit['ingredients'])) ?></p>
            </div>

            <?php if ($produit['stock'] > 0): ?>
                <form method="POST" class="add-to-cart-form">
                    <input type="number" name="quantite" value="1" min="1" max="<?= $produit['stock'] ?>">
                    <button type="submit" name="ajouter_panier" class="btn btn-primary">
                        🛒 Ajouter au panier
                    </button>
                </form>
            <?php else: ?>
                <p class="alert alert-error" style="margin-top:1rem;">Ce produit est actuellement épuisé.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
