<?php
$titre_page = 'Confirmation de commande';
require_once 'config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['compte_id'])) {
    header('Location: connexion.php?redirect=panier.php');
    exit;
}

$compte_id = $_SESSION['compte_id'];

// Récupérer le panier
$stmt = $pdo->prepare("SELECT pan.*, p.id AS produit_id, p.stock FROM panier pan JOIN produits p ON p.nom = pan.nom_produit WHERE pan.id_compte = ?");
$stmt->execute([$compte_id]);
$articles = $stmt->fetchAll();

if (empty($articles)) {
    header('Location: panier.php');
    exit;
}

$erreurs = [];
$total   = 0;

// Vérifier les stocks et calculer le total
foreach ($articles as $a) {
    if ($a['quantite'] > $a['stock']) {
        $erreurs[] = "Stock insuffisant pour « {$a['nom_produit']} » (stock : {$a['stock']}).";
    }
    $total += $a['prix'] * $a['quantite'];
}

$commande_ok = false;

// Valider la commande
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['valider']) && empty($erreurs)) {
    $pdo->beginTransaction();
    try {
        $date = date('Y-m-d');
        foreach ($articles as $a) {
            // Ajouter à l'historique
            $ins = $pdo->prepare("INSERT INTO historique_commande (id_compte, id_produit, date) VALUES (?, ?, ?)");
            $ins->execute([$compte_id, $a['produit_id'], $date]);

            // Décrémenter le stock
            $upd = $pdo->prepare("UPDATE produits SET stock = stock - ? WHERE id = ?");
            $upd->execute([$a['quantite'], $a['produit_id']]);
        }
        // Vider le panier
        $del = $pdo->prepare("DELETE FROM panier WHERE id_compte = ?");
        $del->execute([$compte_id]);

        $pdo->commit();
        $commande_ok = true;
    } catch (Exception $e) {
        $pdo->rollBack();
        $erreurs[] = 'Une erreur est survenue lors de la commande. Veuillez réessayer.';
    }
}

require_once 'includes/header.php';
?>

<main>
    <?php if ($commande_ok): ?>
        <div class="confirm-box">
            <div class="confirm-icon">🎉</div>
            <h2>Commande confirmée !</h2>
            <p>
                Merci <?= htmlspecialchars($_SESSION['utilisateur']) ?> !<br>
                Votre commande a bien été enregistrée.<br>
                Vous pouvez suivre vos achats depuis votre tableau de bord.
            </p>
            <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
                <a href="dashboard.php" class="btn btn-primary">Mon tableau de bord</a>
                <a href="produits.php" class="btn btn-outline">Continuer mes achats</a>
            </div>
        </div>

    <?php else: ?>
        <div class="page-header">
            <h1>Récapitulatif de <span style="color:var(--accent)">commande</span></h1>
            <p>Vérifiez votre commande avant de valider</p>
        </div>

        <div class="cart-wrapper">
            <?php foreach ($erreurs as $e): ?>
                <div class="alert alert-error"><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>

            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Quantité</th>
                        <th>Prix unit.</th>
                        <th>Sous-total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $a): ?>
                        <tr>
                            <td style="font-weight:600;">🍺 <?= htmlspecialchars($a['nom_produit']) ?></td>
                            <td><?= $a['quantite'] ?></td>
                            <td><?= number_format($a['prix'], 2) ?> €</td>
                            <td style="color:var(--accent);font-weight:700;"><?= number_format($a['prix'] * $a['quantite'], 2) ?> €</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <div class="cart-total">Total : <?= number_format($total, 2) ?> €</div>
                <?php if (empty($erreurs)): ?>
                    <form method="POST" style="display:inline;">
                        <button type="submit" name="valider" class="btn btn-primary">
                            ✓ Confirmer et payer
                        </button>
                    </form>
                <?php endif; ?>
                <a href="panier.php" class="btn btn-outline" style="margin-left:.8rem;">← Modifier le panier</a>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php require_once 'includes/footer.php'; ?>
