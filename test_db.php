<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

try {
    $pdo = new PDO('mysql:host=localhost;dbname=laravel', 'root', '');
    echo "Database connection: SUCCESS\n";
} catch (Exception $e) {
    echo "Database connection: FAILED - " . $e->getMessage() . "\n";
}
