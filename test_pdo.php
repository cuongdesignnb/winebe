<?php
try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3317;dbname=wine_db", "root", "");
    echo "Connected successfully\n";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
