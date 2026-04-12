<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $site  = $_POST['site_name'] ?? '';
    $login = $_POST['login'] ?? '';
    $mdp   = $_POST['password_text'] ?? '';

    if (!empty($site) && !empty($login) && !empty($mdp)) {
        try {
            // Chiffrement du mot de passe
            $mdp_chiffre = encrypt_password($mdp);
            
            // Récupération IP + nom machine
            $ip = $_SERVER['REMOTE_ADDR'];
            $machine = gethostbyaddr($ip);

            $sql = "INSERT INTO accounts (site_name, login, password_text, machine_ip, machine_name) 
                    VALUES (:site, :login, :mdp, :ip, :machine)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'site'    => $site,
                'login'   => $login,
                'mdp'     => $mdp_chiffre,
                'ip'      => $ip,
                'machine' => $machine
            ]);
            
            $message = "<div class='success'>✅ Enregistré et chiffré pour $site !</div>";
        } catch (Exception $e) {
            $message = "<div class='error'>❌ Erreur : " . $e->getMessage() . "</div>";
        }
    } else {
        $message = "<div class='warning'>⚠️ Remplissez tous les champs.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un mot de passe</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        h2 { color: #333; margin-bottom: 1.5rem; }
        input { width: 100%; padding: 12px; margin: 8px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background-color: #2c3e50; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #34495e; }
        .footer-link { margin-top: 20px; display: block; color: #7f8c8d; text-decoration: none; font-size: 14px; }
        .success { color: green; font-weight: bold; margin-bottom: 15px; }
        .error { color: red; margin-bottom: 15px; }
        .warning { color: orange; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>🔑 Nouveau Compte</h2>
        <?php echo $message; ?>
        <form method="POST">
            <input type="text" name="site_name" placeholder="Nom du site (ex: Facebook)" required>
            <input type="text" name="login" placeholder="Nom d'utilisateur / Email" required>
            <input type="password" name="password_text" placeholder="Mot de passe" required>
            <button type="submit">🔒 Enregistrer en sécurité</button>
        </form>
        <a href="index.php" class="footer-link">← Voir mes mots de passe</a>
    </div>
</body>
</html>