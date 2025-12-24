<?php
$host = getenv("DB_HOST");
$port = intval(getenv("DB_PORT") ?: 3306);
$db   = getenv("DB_NAME");
$user = getenv("DB_USER");
$pass = getenv("DB_PASS");

if (!$host || !$db || !$user) {
  die("Config DB manquante: vérifie les variables d'environnement DB_HOST/DB_NAME/DB_USER/DB_PASS.");
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli($host, $user, $pass, $db, $port);
$conn->set_charset("utf8mb4");
?>
