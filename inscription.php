<?php
$titre_page = 'Inscription';
require_once 'config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['compte_id'])) {
    header('Location: dashboard.php');
    exit;
}

$erreur  = '';
$succes  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $user  = trim($_POST['utilisateur'] ?? '');
    $mdp   = $_POST['mot_de_passe'] ?? '';
    $mdp2  = $_POST['mot_de_passe2'] ?? '';

    if ($email === '' || $user === '' || $mdp === '') {
        $erreur = 'Veuillez remplir tous les champs.';
    } elseif (strlen($user) > 20) {
        $erreur = "Le nom d'utilisateur ne doit pas dépasser 20 caractères.";
    } elseif (strlen($mdp) > 20) {
        $erreur = "Le mot de passe ne doit pas dépasser 20 caractères.";
    } elseif ($mdp !== $mdp2) {
        $erreur = 'Les mots de passe ne correspondent pas.';
    } else {
        // Vérifier si l'email existe déjà
        $check = $pdo->prepare("SELECT id FROM compte WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $erreur = 'Cette adresse e-mail est déjà utilisée.';
        } else {
            $ins = $pdo->prepare("INSERT INTO compte (email, Utilisateur, mot_de_passe) VALUES (?, ?, ?)");
            $ins->execute([$email, $user, $mdp]);
            $nouveau_id = $pdo->lastInsertId();

            $_SESSION['compte_id']   = $nouveau_id;
            $_SESSION['utilisateur'] = $user;
            header('Location: dashboard.php');
            exit;
        }
    }
}

require_once 'includes/header.php';
?>

<main>
    <div class="auth-wrapper">
        <div class="auth-card">
            <h2>Créer un compte</h2>
            <p class="sub">Rejoignez la communauté Solem Exire 🍺</p>

            <?php if ($erreur): ?>
                <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="email">Adresse e-mail</label>
                    <input type="email" id="email" name="email" placeholder="votre@email.fr"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="utilisateur">Nom d'utilisateur <small style="color:var(--text-muted)">(max 20 car.)</small></label>
                    <input type="text" id="utilisateur" name="utilisateur" placeholder="MonPseudo"
                           maxlength="20"
                           value="<?= htmlspecialchars($_POST['utilisateur'] ?? '') ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="mot_de_passe">Mot de passe <small style="color:var(--text-muted)">(max 20 car.)</small></label>
                        <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="••••••••" maxlength="20" required>
                    </div>
                    <div class="form-group">
                        <label for="mot_de_passe2">Confirmer</label>
                        <input type="password" id="mot_de_passe2" name="mot_de_passe2" placeholder="••••••••" maxlength="20" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;margin-top:.5rem;">
                    Créer mon compte
                </button>
            </form>

            <p style="text-align:center;margin-top:1.5rem;color:var(--text-muted);font-size:.9rem;">
                Déjà un compte ?
                <a href="connexion.php">Se connecter</a>
            </p>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
