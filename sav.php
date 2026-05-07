<?php
$titre_page = 'Service Client';
require_once 'config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$message = '';
$erreur  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['compte_id'])) {
        header('Location: connexion.php?redirect=sav.php');
        exit;
    }

    $commentaire = trim($_POST['commentaire'] ?? '');
    if ($commentaire === '') {
        $erreur = 'Le message ne peut pas être vide.';
    } else {
        $ins = $pdo->prepare("INSERT INTO sav (id_client, commentaire) VALUES (?, ?)");
        $ins->execute([$_SESSION['compte_id'], $commentaire]);
        $message = 'Votre message a bien été envoyé. Nous vous répondrons dans les plus brefs délais.';
    }
}

require_once 'includes/header.php';
?>

<main>
    <div class="page-header">
        <h1>Service <span style="color:var(--accent)">Client</span></h1>
        <p>Une question, un problème ? Nous sommes là pour vous aider.</p>
    </div>

    <div class="section">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:3rem;max-width:900px;margin:0 auto;">

            <!-- Infos contact -->
            <div>
                <h2 style="margin-bottom:1.5rem;">Nous contacter</h2>
                <div class="feature-card" style="margin-bottom:1rem;">
                    <div class="feature-icon">📧</div>
                    <h3>E-mail</h3>
                    <p>contact@solem-exire.fr</p>
                </div>
                <div class="feature-card" style="margin-bottom:1rem;">
                    <div class="feature-icon">🕐</div>
                    <h3>Horaires</h3>
                    <p>Lundi – Vendredi : 9h – 18h</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📍</div>
                    <h3>Adresse</h3>
                    <p>Brasserie Solem Exire<br>France</p>
                </div>
            </div>

            <!-- Formulaire -->
            <div>
                <div class="dashboard-section">
                    <h2>Envoyer un message</h2>

                    <?php if ($message): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                    <?php endif; ?>
                    <?php if ($erreur): ?>
                        <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
                    <?php endif; ?>

                    <?php if (!isset($_SESSION['compte_id'])): ?>
                        <div class="alert alert-info">
                            Vous devez être <a href="connexion.php?redirect=sav.php">connecté</a> pour envoyer un message.
                        </div>
                    <?php else: ?>
                        <form method="POST">
                            <div class="form-group">
                                <label>Votre message</label>
                                <textarea name="commentaire" placeholder="Décrivez votre problème ou votre question..." rows="6" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary" style="width:100%;">
                                Envoyer le message
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
