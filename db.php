<?php
$servername = getenv("DB_HOST") ?: "localhost";
$username   = getenv("DB_USER") ?: "root";
$password   = getenv("DB_PASS") ?: "";
$dbname     = getenv("DB_NAME") ?: "depannage_cdl";
$port       = intval(getenv("DB_PORT") ?: 3306);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli($servername, $username, $password, $dbname, $port);
$conn->set_charset("utf8mb4");
?>
