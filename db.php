<?php
$url = getenv("mysql://root:zidqEcwBUwFecrYElDFAfyxidwsKOGoJ@mainline.proxy.rlwy.net:48965/railway");
if (!$url) die("DATABASE_URL manquante");

$parts = parse_url($url);
$host = $parts["host"];
$port = $parts["port"] ?? 48965;
$user = $parts["user"];
$pass = $parts["pass"] ?? "";
$db   = ltrim($parts["path"] ?? "", "/");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli($host, $user, $pass, $db, intval($port));
$conn->set_charset("utf8mb4");
?>



