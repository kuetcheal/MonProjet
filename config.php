<?php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

define('GOOGLE_MAPS_API_KEY', $_ENV['GOOGLE_MAPS_API_KEY']);

define('DB_HOST', $_ENV['DB_HOST']);
define('DB_NAME', $_ENV['DB_NAME']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASS', $_ENV['DB_PASS']);

define('MAILJET_PUBLIC_KEY', $_ENV['MAILJET_PUBLIC_KEY']);
define('MAILJET_PRIVATE_KEY', $_ENV['MAILJET_PRIVATE_KEY']);
define('MAIL_FROM_EMAIL', $_ENV['MAIL_FROM_EMAIL']);
define('MAIL_FROM_NAME', $_ENV['MAIL_FROM_NAME']);

define('DOLIBARR_API_URL', $_ENV['DOLIBARR_API_URL']);
define('DOLIBARR_API_KEY', $_ENV['DOLIBARR_API_KEY']);