<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
if (!isset($_SESSION["user_id"])) { http_response_code(401); echo json_encode(["ok"=>false,"error"=>"NON_AUTH"]); exit(); }
require "db.php";

$num = trim($_GET["num"] ?? "");
if ($num === "") { http_response_code(400); echo json_encode(["ok"=>false,"error"=>"MACHINE_REQUIRED"]); exit(); }

$stmt = $conn->prepare("SELECT id, original_name, stored_name, mime_type, size_bytes, uploaded_at FROM machine_documents WHERE num_machine=? ORDER BY id DESC");
$stmt->bind_param("s", $num);
$stmt->execute();
$res = $stmt->get_result();
$rows = [];
while($r = $res->fetch_assoc()) { $rows[] = $r; }
echo json_encode(["ok"=>true, "items"=>$rows]);
