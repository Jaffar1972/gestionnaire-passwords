<?php
session_start();
if (!isset($_SESSION['logged_in'])) { header('Location: login.php'); exit; }
require_once 'config.php';

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: index.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = ?");
$stmt->execute([$id]);
$account = $stmt->fetch();
if (!$account) { header('Location: index.php'); exit; }

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site = $_POST['site_name'];
    $login = $_POST['login'];
    $password = encrypt_password($_POST['password_text']);
    $machine = $_POST['machine_name'];
    $ip = $_POST['machine_ip'];
    $stmt = $pdo->prepare("UPDATE accounts SET site_name=?, login=?, password_text=?, machine_name=?, machine_ip=? WHERE id=?");
    $stmt->execute([$site, $login, $password, $machine, $ip, $id]);
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 400px; }
        h2 { text-align: center; color: #2c3e50; }
        input { width: 100%; padding: 12px; margin: 8px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #f39c12; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: #e67e22; }
        .footer-link { margin-top: 20px; display: block; text-align: center; color: #7f8c8d; text-decoration: none; }
    </style>
</head>
<body>
<div class="card">
    <h2>✏️ Modifier le compte</h2>
    <form method="POST">
        <input type="text" name="site_name" placeholder="Nom du site" value="<?= htmlspecialchars($account['site_name']) ?>" required>
        <input type="text" name="login" placeholder="Identifiant / Email" value="<?= htmlspecialchars($account['login']) ?>" required>
        <input type="text" name="password_text" placeholder="Nouveau mot de passe" value="<?= htmlspecialchars(decrypt_password($account['password_text'])) ?>" required>
        <input type="text" name="machine_name" placeholder="Machine" value="<?= htmlspecialchars($account['machine_name'] ?? '') ?>">
        <input type="text" name="machine_ip" placeholder="IP" value="<?= htmlspecialchars($account['machine_ip'] ?? '') ?>">
        <button type="submit">💾 Enregistrer</button>
    </form>
    <a href="index.php" class="footer-link">← Retour</a>
</div>
</body>
</html>