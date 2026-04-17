<?php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'bd_stock');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');


define('CLOUDINARY_URL', $_ENV['CLOUDINARY_URL'] ?? '');

define('GOOGLE_MAPS_API_KEY', $_ENV['GOOGLE_MAPS_API_KEY'] ?? '');

define('MAILJET_PUBLIC_KEY', $_ENV['MAILJET_PUBLIC_KEY'] ?? '');
define('MAILJET_PRIVATE_KEY', $_ENV['MAILJET_PRIVATE_KEY'] ?? '');
define('MAIL_FROM_EMAIL', $_ENV['MAIL_FROM_EMAIL'] ?? '');
define('MAIL_FROM_NAME', $_ENV['MAIL_FROM_NAME'] ?? '');

define('DOLIBARR_API_URL', $_ENV['DOLIBARR_API_URL'] ?? '');
define('DOLIBARR_API_KEY', $_ENV['DOLIBARR_API_KEY'] ?? '');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}