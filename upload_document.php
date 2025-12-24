<?php
session_start();
if (!isset($_SESSION["user_id"])) { http_response_code(401); echo "NON_AUTH"; exit(); }
require "db.php";

$num = trim($_POST["num"] ?? "");
if ($num === "") { http_response_code(400); echo "MACHINE_REQUIRED"; exit(); }

if (!isset($_FILES["file"])) { http_response_code(400); echo "FILE_REQUIRED"; exit(); }

$uploadsDir = __DIR__ . "/uploads";
if (!is_dir($uploadsDir)) { mkdir($uploadsDir, 0777, true); }

$f = $_FILES["file"];
if ($f["error"] !== UPLOAD_ERR_OK) { http_response_code(400); echo "UPLOAD_ERROR"; exit(); }

$original = basename($f["name"]);
$mime = $f["type"] ?? "";
$size = intval($f["size"] ?? 0);

$ext = pathinfo($original, PATHINFO_EXTENSION);
$stored = uniqid("doc_", true) . ($ext ? ".".$ext : "");
$dest = $uploadsDir . "/" . $stored;

if (!move_uploaded_file($f["tmp_name"], $dest)) {
  http_response_code(500);
  echo "MOVE_FAILED";
  exit();
}

$stmt = $conn->prepare("INSERT INTO machine_documents (num_machine, stored_name, original_name, mime_type, size_bytes) VALUES (?,?,?,?,?)");
$stmt->bind_param("ssssi", $num, $stored, $original, $mime, $size);
if (!$stmt->execute()) {
  http_response_code(500);
  echo "DB_INSERT";
  exit();
}

header("Location: documents.php?num=" . urlencode($num));
