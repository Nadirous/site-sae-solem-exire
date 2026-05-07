<?php
$titre_page = 'Mon Panier';
require_once 'config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['compte_id'])) {
    header('Location: connexion.php?redirect=panier.php');
    exit;
}

$compte_id = $_SESSION['compte_id'];
$message   = '';
$erreur    = '';

// Supprimer un article
if (isset($_GET['supprimer'])) {
    $item_id = (int)$_GET['supprimer'];
    $del = $pdo->prepare("DELETE FROM panier WHERE id = ? AND id_compte = ?");
    $del->execute([$item_id, $compte_id]);
    header('Location: panier.php');
    exit;
}

// Mettre à jour les quantités
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_panier'])) {
    foreach ($_POST['qty'] as $item_id => $qty) {
        $item_id = (int)$item_id;
        $qty     = max(1, (int)$qty);

        // Vérifier le stock
        $pi = $pdo->prepare("SELECT p.stock FROM panier pan JOIN produits p ON p.nom = pan.nom_produit WHERE pan.id = ? AND pan.id_compte = ?");
        $pi->execute([$item_id, $compte_id]);
        $row = $pi->fetch();
        if ($row && $qty <= $row['stock']) {
            $upd = $pdo->prepare("UPDATE panier SET quantite = ? WHERE id = ? AND id_compte = ?");
            $upd->execute([$qty, $item_id, $compte_id]);
        }
    }
    $message = 'Panier mis à jour.';
}

// Récupérer les articles du panier
$stmt = $pdo->prepare("SELECT pan.*, p.stock, p.id AS produit_id FROM panier pan JOIN produits p ON p.nom = pan.nom_produit WHERE pan.id_compte = ?");
$stmt->execute([$compte_id]);
$articles = $stmt->fetchAll();

$total = array_sum(array_map(fn($a) => $a['prix'] * $a['quantite'], $articles));

require_once 'includes/header.php';
?>

<main>
    <div class="page-header">
        <h1>🛒 Mon <span style="color:var(--accent)">Panier</span></h1>
        <p>Vérifiez votre commande avant de valider</p>
    </div>

    <div class="cart-wrapper">
        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if ($erreur): ?>
            <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <?php if (empty($articles)): ?>
            <div class="empty-state" style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:3rem;">
                <div class="empty-icon">🛒</div>
                <p>Votre panier est vide.</p>
                <a href="produits.php" class="btn btn-primary" style="margin-top:1.2rem;">Découvrir nos bières</a>
            </div>
        <?php else: ?>
            <form method="POST">
                <div class="cart-table-wrap">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Prix unitaire</th>
                            <th>Quantité</th>
                            <th>Sous-total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articles as $a): ?>
                            <tr>
                                <td>
                                    <a href="produit.php?id=<?= $a['produit_id'] ?>" style="font-weight:600;color:var(--text);">
                                        🍺 <?= htmlspecialchars($a['nom_produit']) ?>
                                    </a>
                                </td>
                                <td><?= number_format($a['prix'], 2) ?> €</td>
                                <td>
                                    <input class="qty-input" type="number" name="qty[<?= $a['id'] ?>]"
                                           value="<?= $a['quantite'] ?>" min="1" max="<?= $a['stock'] ?>">
                                </td>
                                <td style="color:var(--accent);font-weight:700;">
                                    <?= number_format($a['prix'] * $a['quantite'], 2) ?> €
                                </td>
                                <td>
                                    <a href="panier.php?supprimer=<?= $a['id'] ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Retirer cet article ?')">✕</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div style="text-align:right;margin-bottom:1rem;">
                    <button type="submit" name="update_panier" class="btn btn-outline btn-sm">
                        ↻ Mettre à jour les quantités
                    </button>
                </div>
            </form>

            <div class="cart-summary">
                <div class="cart-total">Total : <?= number_format($total, 2) ?> €</div>
                <a href="commander.php" class="btn btn-primary">Passer la commande →</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
