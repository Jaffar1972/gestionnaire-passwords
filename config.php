<?php
require_once '/etc/passwords_config.php';

$host = 'localhost';
$db   = 'passwords_db';
$user = 'root';
$pass = DB_PASS;
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Erreur de connexion.");
}

function encrypt_password($plaintext) {
    $iv = random_bytes(16);
    $encrypted = openssl_encrypt($plaintext, 'AES-256-CBC', ENCRYPTION_KEY, 0, $iv);
    return base64_encode($iv . $encrypted);
}

function decrypt_password($ciphertext) {
    $data = base64_decode($ciphertext);
    $iv = substr($data, 0, 16);
    $encrypted = substr($data, 16);
    return openssl_decrypt($encrypted, 'AES-256-CBC', ENCRYPTION_KEY, 0, $iv);
}
?>