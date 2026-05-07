<?php
$titre_page = 'Mon Compte';
require_once 'config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['compte_id'])) {
    header('Location: connexion.php?redirect=dashboard.php');
    exit;
}

$compte_id = $_SESSION['compte_id'];
$onglet    = $_GET['tab'] ?? 'commandes';
$message   = '';
$erreur    = '';

// Récupérer les infos du compte
$stmt = $pdo->prepare("SELECT * FROM compte WHERE id = ?");
$stmt->execute([$compte_id]);
$compte = $stmt->fetch();

// ---- Mise à jour du profil ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profil'])) {
    $onglet = 'profil';
    $new_email = trim($_POST['email'] ?? '');
    $new_user  = trim($_POST['utilisateur'] ?? '');
    $new_mdp   = $_POST['mot_de_passe'] ?? '';

    if ($new_email === '' || $new_user === '') {
        $erreur = 'Email et nom d\'utilisateur requis.';
    } elseif (strlen($new_user) > 20) {
        $erreur = "Nom d'utilisateur trop long (max 20).";
    } else {
        if ($new_mdp !== '') {
            if (strlen($new_mdp) > 20) {
                $erreur = 'Mot de passe trop long (max 20).';
            } else {
                $upd = $pdo->prepare("UPDATE compte SET email=?, Utilisateur=?, mot_de_passe=? WHERE id=?");
                $upd->execute([$new_email, $new_user, $new_mdp, $compte_id]);
            }
        } else {
            $upd = $pdo->prepare("UPDATE compte SET email=?, Utilisateur=? WHERE id=?");
            $upd->execute([$new_email, $new_user, $compte_id]);
        }
        if (!$erreur) {
            $_SESSION['utilisateur'] = $new_user;
            $message = 'Profil mis à jour avec succès.';
            $stmt = $pdo->prepare("SELECT * FROM compte WHERE id = ?");
            $stmt->execute([$compte_id]);
            $compte = $stmt->fetch();
        }
    }
}

// ---- Ajout d'un avis ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['poster_avis'])) {
    $onglet = 'avis';
    $etoiles = (int)($_POST['etoiles'] ?? 3);
    $texte   = trim($_POST['text_avis'] ?? '');
    $etoiles = max(1, min(5, $etoiles));

    if ($texte === '') {
        $erreur = 'Le texte de l\'avis ne peut pas être vide.';
    } elseif (strlen($texte) > 50) {
        $erreur = 'L\'avis ne doit pas dépasser 50 caractères.';
    } else {
        $ins = $pdo->prepare("INSERT INTO avis (id_client, etoiles_client, text_avis) VALUES (?, ?, ?)");
        $ins->execute([$compte_id, $etoiles, $texte]);
        $message = 'Votre avis a été publié !';
    }
}

// Récupérer l'historique des commandes
$histo = $pdo->prepare("
    SELECT hc.date, p.nom, p.prix
    FROM historique_commande hc
    JOIN produits p ON p.id = hc.id_produit
    WHERE hc.id_compte = ?
    ORDER BY hc.date DESC, hc.id DESC
");
$histo->execute([$compte_id]);
$commandes = $histo->fetchAll();

// Récupérer les avis de l'utilisateur
$mes_avis = $pdo->prepare("SELECT * FROM avis WHERE id_client = ? ORDER BY id DESC");
$mes_avis->execute([$compte_id]);
$avis_liste = $mes_avis->fetchAll();

require_once 'includes/header.php';
?>

<main>
    <div class="page-header">
        <h1>Bonjour, <span style="color:var(--accent)"><?= htmlspecialchars($_SESSION['utilisateur']) ?></span> 👋</h1>
        <p>Gérez vos commandes, votre profil et vos avis</p>
    </div>

    <div class="dashboard-layout">
        <!-- Menu latéral -->
        <nav class="dashboard-nav">
            <a href="?tab=commandes" <?= $onglet === 'commandes' ? 'class="active"' : '' ?>>📦 Mes commandes</a>
            <a href="?tab=profil"    <?= $onglet === 'profil'    ? 'class="active"' : '' ?>>👤 Mon profil</a>
            <a href="?tab=avis"      <?= $onglet === 'avis'      ? 'class="active"' : '' ?>>⭐ Mes avis</a>
            <a href="sav.php">💬 Service client (SAV)</a>
            <a href="panier.php">🛒 Mon panier</a>
        </nav>

        <!-- Contenu -->
        <div>
            <?php if ($message): ?>
                <div class="alert alert-success" style="margin-bottom:1.5rem;"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <?php if ($erreur): ?>
                <div class="alert alert-error" style="margin-bottom:1.5rem;"><?= htmlspecialchars($erreur) ?></div>
            <?php endif; ?>

            <!-- Onglet : Commandes -->
            <?php if ($onglet === 'commandes'): ?>
                <div class="dashboard-section">
                    <h2>📦 Historique des commandes</h2>
                    <?php if (empty($commandes)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">📦</div>
                            <p>Vous n'avez pas encore passé de commande.</p>
                            <a href="produits.php" class="btn btn-primary" style="margin-top:1rem;">Découvrir nos bières</a>
                        </div>
                    <?php else: ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Produit</th>
                                    <th>Prix</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($commandes as $c): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($c['date']) ?></td>
                                        <td>🍺 <?= htmlspecialchars($c['nom']) ?></td>
                                        <td style="color:var(--accent);font-weight:600;"><?= number_format($c['prix'], 2) ?> €</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

            <!-- Onglet : Profil -->
            <?php elseif ($onglet === 'profil'): ?>
                <div class="dashboard-section">
                    <h2>👤 Mon profil</h2>
                    <form method="POST">
                        <div class="form-group">
                            <label>Adresse e-mail</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($compte['email']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Nom d'utilisateur <small style="color:var(--text-muted)">(max 20 car.)</small></label>
                            <input type="text" name="utilisateur" maxlength="20" value="<?= htmlspecialchars($compte['Utilisateur']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Nouveau mot de passe <small style="color:var(--text-muted)">(laisser vide pour ne pas changer, max 20)</small></label>
                            <input type="password" name="mot_de_passe" maxlength="20" placeholder="••••••••">
                        </div>
                        <button type="submit" name="update_profil" class="btn btn-primary">
                            Enregistrer les modifications
                        </button>
                    </form>
                </div>

            <!-- Onglet : Avis -->
            <?php elseif ($onglet === 'avis'): ?>
                <div class="dashboard-section">
                    <h2>⭐ Mes avis</h2>

                    <form method="POST" style="margin-bottom:2rem;padding:1.5rem;background:var(--bg);border:1px solid var(--border);border-radius:8px;">
                        <h3 style="margin-bottom:1rem;font-size:1rem;">Laisser un nouvel avis</h3>
                        <div class="form-group">
                            <label>Note (1 à 5 étoiles)</label>
                            <select name="etoiles">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <option value="<?= $i ?>"><?= str_repeat('★', $i) . str_repeat('☆', 5 - $i) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Votre avis <small style="color:var(--text-muted)">(max 50 car.)</small></label>
                            <input type="text" name="text_avis" maxlength="50" placeholder="Excellent produit !" required>
                        </div>
                        <button type="submit" name="poster_avis" class="btn btn-primary btn-sm">Publier l'avis</button>
                    </form>

                    <?php if (empty($avis_liste)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">⭐</div>
                            <p>Vous n'avez pas encore posté d'avis.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($avis_liste as $a): ?>
                            <div class="review-card">
                                <div class="review-header">
                                    <span class="reviewer">Votre avis</span>
                                    <span class="stars"><?= str_repeat('★', $a['etoiles_client']) ?></span>
                                </div>
                                <div class="review-text">"<?= htmlspecialchars($a['text_avis']) ?>"</div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
