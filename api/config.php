<?php
header('Content-Type: application/json; charset=utf-8');

$DB_HOST = 'localhost';
$DB_NAME = 'celebration_db';
$DB_USER = 'root';
$DB_PASS = 'Hung123@';

$dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";
try {
  $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch(Exception $e){
  http_response_code(500);
  echo json_encode(['error' => 'DB connect failed']);
  exit;
}
