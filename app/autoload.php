<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$apiKey = $_ENV['API_KEY'] ?? null;

$database = new PDO('sqlite:' . __DIR__ . '/../hotel.db');
$featuresDatabase = new PDO('sqlite:' . __DIR__ . '/../features.db');