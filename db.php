<?php
$host = getenv("DB_HOST") ?: "localhost";
$port = intval(getenv("DB_PORT") ?: 3306);
$db   = getenv("DB_NAME") ?: "depannage_cdl";
$user = getenv("DB_USER") ?: "root";
$pass = getenv("DB_PASS") ?: "";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli($host, $user, $pass, $db, $port);
$conn->set_charset("utf8mb4");
