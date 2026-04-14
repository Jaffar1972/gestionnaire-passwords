<?php
session_start();
if (!isset($_SESSION['logged_in'])) { 
    header('Location: login.php'); 
    exit; 
}

ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'config.php';

// Fonction de déchiffrement (à adapter selon votre méthode)
function decrypt_password($encrypted) {
    // Si vous utilisez openssl_encrypt, ajoutez votre logique ici
    // Exemple simple (non sécurisé pour production) :
    return $encrypted; // À remplacer par votre vrai déchiffrement
}

// Supprimer un compte
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM accounts WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: index.php');
    exit;
}

// Récupérer tous les comptes
$stmt = $pdo->query("SELECT * FROM accounts ORDER BY created_at DESC");
$accounts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Mots de Passe</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px; 
            color: #333; 
        }
        
        .container { 
            max-width: 1400px; 
            margin: auto; 
            background: white; 
            padding: 30px; 
            border-radius: 15px; 
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        h2 { 
            color: #2c3e50; 
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-add { 
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #27ae60, #229954);
            color: white; 
            padding: 12px 24px; 
            text-decoration: none; 
            border-radius: 8px; 
            font-weight: bold;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(39,174,96,0.3);
        }
        
        .btn-logout { 
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white; 
            padding: 12px 24px; 
            text-decoration: none; 
            border-radius: 8px; 
            font-weight: bold;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(231,76,60,0.3);
        }
        
        .stats {
            background: #f8f9fa;
            padding: 10px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: inline-block;
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        
        th, td { 
            padding: 15px; 
            text-align: left; 
            border-bottom: 1px solid #e0e0e0; 
        }
        
        th { 
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white; 
            font-weight: 600;
            position: sticky;
            top: 0;
        }
        
        tr {
            transition: background-color 0.2s;
        }
        
        tr:hover { 
            background-color: #f5f5f5; 
        }
        
        .password-cell { 
            font-family: 'Courier New', monospace; 
            background: #f0f0f0; 
            padding: 6px 12px; 
            border-radius: 6px; 
            display: inline-block;
            letter-spacing: 1px;
        }
        
        .btn-show { 
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white; 
            border: none; 
            padding: 5px 12px; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 12px;
            margin-left: 8px;
            transition: all 0.2s;
        }
        
        .btn-show:hover {
            transform: scale(1.05);
        }
        
        .btn-delete { 
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white; 
            border: none; 
            padding: 6px 12px; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 12px; 
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
        }
        
        .btn-delete:hover {
            transform: scale(1.05);
        }
        
        .btn-edit { 
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white; 
            border: none; 
            padding: 6px 12px; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 12px; 
            text-decoration: none;
            display: inline-block;
            margin-right: 5px;
            transition: all 0.2s;
        }
        
        .btn-edit:hover {
            transform: scale(1.05);
        }
        
        .machine-info { 
            font-size: 13px; 
            color: #555;
        }
        
        .machine-info small {
            color: #888;
        }
        
        .actions {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        .empty-state p {
            font-size: 18px;
            margin-top: 10px;
        }
        
        @media (max-width: 768px) {
            body { padding: 20px; }
            .container { padding: 15px; }
            th, td { padding: 8px; font-size: 12px; }
            .btn-add, .btn-logout { padding: 8px 16px; font-size: 12px; }
            h2 { font-size: 20px; }
            .actions { flex-direction: column; }
            .btn-edit, .btn-delete { text-align: center; margin: 2px 0; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>
            🔐 Mes Mots de Passe
        </h2>
        <div style="display: flex; gap: 10px;">
            <a href="add.php" class="btn-add">➕ Ajouter</a>
            <a href="logout.php" class="btn-logout">🚪 Déconnexion</a>
        </div>
    </div>
    
    <div class="stats">
        📊 Total : <strong><?= count($accounts) ?></strong> compte(s) enregistré(s)
    </div>
    
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>🌐 Site / Application</th>
                    <th>👤 Identifiant</th>
                    <th>🔒 Mot de Passe</th>
                    <th>💻 Machine / Poste</th>
                    <th>📅 Date d'ajout</th>
                    <th>⚡ Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($accounts)): ?>
                    <tr class="empty-state">
                        <td colspan="6">
                            <div style="text-align:center;">
                                📭 <p>Aucun compte enregistré pour le moment</p>
                                <a href="add.php" class="btn-add" style="margin-top: 15px; display: inline-block;">➕ Ajouter votre premier compte</a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($accounts as $account): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($account['site_name']) ?></strong>
                            </td>
                            <td>
                                <?= htmlspecialchars($account['login']) ?>
                            </td>
                            <td>
                                <span class="password-cell" id="pwd-<?= $account['id'] ?>">••••••••</span>
                                <button class="btn-show" onclick="togglePassword(<?= $account['id'] ?>, '<?= addslashes($account['password_text']) ?>')">👁️ Afficher</button>
                             </td>
                            <td class="machine-info">
                                <?php if (!empty($account['machine_name'])): ?>
                                    🖥️ <?= htmlspecialchars($account['machine_name']) ?><br>
                                <?php endif; ?>
                                <?php if (!empty($account['machine_ip'])): ?>
                                    <small>📡 <?= htmlspecialchars($account['machine_ip']) ?></small>
                                <?php endif; ?>
                                <?php if (empty($account['machine_name']) && empty($account['machine_ip'])): ?>
                                    <span style="color: #999;">Non spécifié</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small><?= date('d/m/Y H:i', strtotime($account['created_at'])) ?></small>
                             </td>
                            <td class="actions">
                                <a href="edit.php?id=<?= $account['id'] ?>" class="btn-edit">✏️ Modifier</a>
                                <a href="index.php?delete=<?= $account['id'] ?>" class="btn-delete" onclick="return confirm('🗑️ Supprimer définitivement ce compte ?\n\nSite : <?= addslashes($account['site_name']) ?>\nCette action est irréversible.')">🗑️ Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function togglePassword(id, plaintext) {
    const el = document.getElementById('pwd-' + id);
    if (el.textContent === '••••••••') {
        el.textContent = plaintext;
    } else {
        el.textContent = '••••••••';
    }
}
</script>
</body>
</html>