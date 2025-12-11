<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apiKey = $_ENV['API_KEY'] ?? null;

require __DIR__ . '/views/header.php';



require __DIR__ . '/views/footer.php';