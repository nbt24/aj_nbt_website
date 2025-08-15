<?php
$host = 'localhost';
$dbname = 'u148807517_NBT';
$username = 'u148807517_NBT'; 
$password = 'NbT2025123';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>