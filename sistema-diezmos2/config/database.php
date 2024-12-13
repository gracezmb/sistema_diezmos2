<?php
try {
    $pdo = new PDO(
        "pgsql:host=localhost;dbname=db_sobre4",
        "postgres",
        "GraZam99",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}