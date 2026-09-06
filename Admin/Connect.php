<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "myopapakyaw_mental_wellness_service";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (Exception $e) {
    die("Fail to connect: " . $e->getMessage());
}
?>