<?php
$servername = "localhost";
$port       = 3307;
$username   = "root";        // change si besoin
$password   = "";            // change si besoin
$dbname     = "depannage_cdl";

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}
?>