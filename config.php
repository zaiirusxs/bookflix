<?php
    $conn = new mysqli('localhost','root','','bookflixdb') or die('Connection Failed'.mysqli_error($conn));

?><?php
try {
    $conn = new PDO('mysql:host=localhost;dbname=bookflixdb', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Connection Failed: ' . $e->getMessage());
}
?>