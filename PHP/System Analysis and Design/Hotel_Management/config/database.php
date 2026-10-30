<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

const DB_HOST = '127.0.0.1';
const DB_NAME = 'Hotel';
const DB_USER = 'root';
const DB_PASS = '';
const DB_PORT = 3306;

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";port=" . DB_PORT . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} 
catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}