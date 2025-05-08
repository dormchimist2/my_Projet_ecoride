<?php
/*
$dsn = 'pgsql:host=localhost;dbname=ecoride_messager;port=5432';
$user = 'postgres';
$password = 'lilineX';

try {
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}
?>
*/

// Connexion à la base de messagerie (APPI)
try {
    $pdoAppi = new PDO('pgsql:host=localhost;dbname=ecoride_messager;port=5432', 'postgres', 'lilineX');
    $pdoAppi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("❌ Connexion APPI échouée : " . $e->getMessage());
}

// Connexion à la base Symfony (utilisateurs, covoiturages, etc.)
try {
    $pdoSymfony = new PDO('pgsql:host=localhost;dbname=ecoride_ju;port=5432', 'postgres', 'lilineX');
    $pdoSymfony->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("❌ Connexion Symfony échouée : " . $e->getMessage());
}
?>
