<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "bookstore_db";

/** @var mysqli $conn */
$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("❌ Lỗi kết nối DB: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>