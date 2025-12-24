<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require 'db.php';

function json_input() {
  $raw = file_get_contents("php://input");
  if (!$raw) return [];
  $data = json_decode($raw, true);
  return is_array($data) ? $data : [];
}

function require_login() {
  if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["ok"=>false, "error"=>"NON_AUTH"]);
    exit();
  }
}

require_login();

$action = $_GET["action"] ?? "";
$payload = array_merge($_POST, json_input());

if ($action === "create") {
  $num = trim($payload["num"] ?? "");
  $type = trim($payload["type"] ?? "");
  $date = trim($payload["date"] ?? "");
  $client = trim($payload["client"] ?? "");
  $tel = trim($payload["tel"] ?? "");
  $interlocuteur = trim($payload["interlocuteur"] ?? "");
  $adresse = trim($payload["adresse"] ?? "");
  $symptomes = trim($payload["symptomes"] ?? "");
  $tech = trim($payload["tech"] ?? "");

  if ($num === "" || $date === "" || $symptomes === "") {
    http_response_code(400);
    echo json_encode(["ok"=>false, "error"=>"MISSING_FIELDS"]);
    exit();
  }

  $stmt = $conn->prepare("INSERT INTO depannages (num_machine, type_machine, date_depannage, client, tel, interlocuteur, adresse, symptomes, technicien, statut)
                          VALUES (?,?,?,?,?,?,?,?,?, 'EN_COURS')");
  $stmt->bind_param("sssssssss", $num, $type, $date, $client, $tel, $interlocuteur, $adresse, $symptomes, $tech);

  if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(["ok"=>false, "error"=>"DB_INSERT", "details"=>$conn->error]);
    exit();
  }

  echo json_encode(["ok"=>true, "id"=>$stmt->insert_id]);
  exit();
}

if ($action === "list") {
  $statut = $_GET["statut"] ?? "EN_COURS";
  if ($statut !== "EN_COURS" && $statut !== "RESOLU") $statut = "EN_COURS";

  $stmt = $conn->prepare("SELECT id, num_machine, type_machine, date_depannage, client, tel, interlocuteur, adresse, symptomes, technicien, statut, cause, solution, created_at, resolved_at
                          FROM depannages WHERE statut=? ORDER BY id DESC");
  $stmt->bind_param("s", $statut);
  $stmt->execute();

  $res = $stmt->get_result();
  $rows = [];
  while ($r = $res->fetch_assoc()) { $rows[] = $r; }

  echo json_encode(["ok"=>true, "items"=>$rows]);
  exit();
}

if ($action === "resolve") {
  $id = intval($payload["id"] ?? 0);
  $cause = trim($payload["cause"] ?? "");
  $solution = trim($payload["solution"] ?? "");

  if ($id <= 0) {
    http_response_code(400);
    echo json_encode(["ok"=>false, "error"=>"MISSING_ID"]);
    exit();
  }
  if ($cause === "") $cause = "Non précisée";
  if ($solution === "") $solution = "Non précisée";

  $stmt = $conn->prepare("UPDATE depannages SET statut='RESOLU', cause=?, solution=?, resolved_at=NOW() WHERE id=?");
  $stmt->bind_param("ssi", $cause, $solution, $id);

  if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(["ok"=>false, "error"=>"DB_UPDATE", "details"=>$conn->error]);
    exit();
  }

  echo json_encode(["ok"=>true]);
  exit();
}

http_response_code(400);
echo json_encode(["ok"=>false, "error"=>"UNKNOWN_ACTION"]);
