<?php
session_start();
if (!isset($_SESSION['logged_in'])) { header('Location: login.php'); exit; }
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'config.php';

// Supprimer
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM accounts WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: index.php');
    exit;
}

$stmt = $pdo->query("SELECT * FROM accounts ORDER BY created_at DESC");
$accounts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Mots de Passe</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7f6; padding: 40px; color: #333; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #2c3e50; color: white; }
        tr:hover { background-color: #f9f9f9; }
        .btn-add { display: inline-block; background: #27ae60; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin-bottom: 20px; }
        .btn-logout { float: right; background: #e74c3c; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; }
        .password-cell { font-family: monospace; background: #eee; padding: 3px 6px; border-radius: 4px; }
        .btn-show { background: #3498db; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 12px; }
        .btn-delete { background: #e74c3c; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 12px; text-decoration: none; }
        .btn-edit { background: #f39c12; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 12px; text-decoration: none; }
        .machine-info { font-size: 12px; color: #666; }
    </style>
</head>
<body>
<div class="container">
    <a href="logout.php" class="btn-logout">🚪 Déconnexion</a>
    <h2>📋 Liste de mes accès</h2>
    <a href="add.php" class="btn-add">➕ Ajouter un nouveau compte</a>
    <table>
        <thead>
            <tr>
                <th>Site</th>
                <th>Identifiant</th>
                <th>Mot de Passe</th>
                <th>Machine</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($accounts)): ?>
                <tr><td colspan="6" style="text-align:center;">Aucun compte enregistré.</td></tr>
            <?php else: ?>
                <?php foreach ($accounts as $account): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($account['site_name']) ?></strong></td>
                    <td><?= htmlspecialchars($account['login']) ?></td>
                    <td>
                        <span class="password-cell" id="pwd-<?= $account['id'] ?>">••••••••</span>
                        <button class="btn-show" onclick="togglePassword(<?= $account['id'] ?>, '<?= addslashes(decrypt_password($account['password_text'])) ?>')">👁️</button>
                    </td>
                    <td class="machine-info">
                        🖥️ <?= htmlspecialchars($account['machine_name'] ?? 'Inconnu') ?><br>
                        <small><?= htmlspecialchars($account['machine_ip'] ?? '') ?></small>
                    </td>
                    <td><small><?= $account['created_at'] ?></small></td>
                    <td>
                        <a href="edit.php?id=<?= $account['id'] ?>" class="btn-edit">✏️ Modifier</a>
                        <a href="index.php?delete=<?= $account['id'] ?>" class="btn-delete" onclick="return confirm('Supprimer ce compte ?')">🗑️ Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<script>
function togglePassword(id, plaintext) {
    const el = document.getElementById('pwd-' + id);
    el.textContent = el.textContent === '••••••••' ? plaintext : '••••••••';
}
</script>
</body>
</html>