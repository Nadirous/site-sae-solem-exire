<?php
$titre_page = 'Connexion';
require_once 'config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['compte_id'])) {
    header('Location: dashboard.php');
    exit;
}

$erreur  = '';
$redirect = $_GET['redirect'] ?? 'dashboard.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mdp   = $_POST['mot_de_passe'] ?? '';

    if ($email === '' || $mdp === '') {
        $erreur = 'Veuillez remplir tous les champs.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM compte WHERE email = ?");
        $stmt->execute([$email]);
        $compte = $stmt->fetch();

        if ($compte && $compte['mot_de_passe'] === $mdp) {
            $_SESSION['compte_id']  = $compte['id'];
            $_SESSION['utilisateur'] = $compte['Utilisateur'];
            header('Location: ' . $redirect);
            exit;
        } else {
            $erreur = 'Email ou mot de passe incorrect.';
        }
    }
}

require_once 'includes/header.php';
?>

<main>
    <div class="auth-wrapper">
        <div class="auth-card">
            <h2>Connexion</h2>
            <p class="sub">Content de vous revoir 🍺</p>

            <?php if ($erreur): ?>
                <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">

                <div class="form-group">
                    <label for="email">Adresse e-mail</label>
                    <input type="email" id="email" name="email" placeholder="votre@email.fr"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="mot_de_passe">Mot de passe</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;margin-top:.5rem;">
                    Se connecter
                </button>
            </form>

            <p style="text-align:center;margin-top:1.5rem;color:var(--text-muted);font-size:.9rem;">
                Pas encore de compte ?
                <a href="inscription.php">Créer un compte</a>
            </p>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
