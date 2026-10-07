<?php

const DB_HOST = '127.0.0.1';
const DB_NAME = 'demoshop';
const DB_USER = 'root';
const DB_PASS = '';
const DB_PORT = 3306;

$con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

if ($con->connect_error) {
    die("Connection failed: " . $con -> connect_errno);
}

$con -> set_charset("utf8mb4");
