<?php
session_start();
if (!isset($_SESSION["user_id"])) { header("Location: login.html"); exit(); }
require "db.php";
$id = intval($_GET["id"] ?? 0);
if ($id <= 0) { http_response_code(400); echo "ID manquant"; exit(); }
$stmt = $conn->prepare("SELECT * FROM depannages WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$p = $res->fetch_assoc();
if (!$p) { http_response_code(404); echo "Fiche introuvable"; exit(); }
function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
$date = $p["date_depannage"] ?? "";
$heure = date("H:i");
?><!DOCTYPE html>
<html lang="fr"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Demande de dépannage - <?php echo e($p["num_machine"]); ?></title>
<style>
  body{ font-family: Arial, sans-serif; margin:0; padding:24px; background:#fff; color:#000; }
  .page{ max-width: 900px; margin: 0 auto; }
  table{ border-collapse: collapse; width:100%; }
  td{ border:1px solid #000; padding:10px; vertical-align:top; }
  .center{ text-align:center; }
  .big{ font-size:24px; font-weight:800; }
  .label{ font-weight:700; }
  .line{ border-bottom:1px solid #000; display:inline-block; min-width:260px; padding:2px 6px; }
  .block{ margin-top:14px; }
  .box{ border:1px solid #000; min-height:120px; padding:10px; white-space:pre-wrap; }
  .sign{ display:flex; justify-content:space-between; gap:20px; margin-top:36px; }
  .sign .s{ width:45%; }
  .sign .s .box{ min-height:90px; }
  @media print{ .no-print{ display:none; } body{ padding:0; } }
</style></head>
<body><div class="page">
  <div class="no-print" style="display:flex;justify-content:flex-end;gap:10px;margin-bottom:10px;">
    <button onclick="window.print()" style="padding:10px 14px;font-weight:800;cursor:pointer;">🖨️ Imprimer / PDF</button>
  </div>

  <table>
    <tr>
      <td style="width:30%;">
        <div class="center" style="font-weight:800;">LOCADOUR</div>
        <div class="center">63 Bd de Thibaud</div>
        <div class="center">31100 Toulouse</div>
        <div class="center">05 61 41 51 51</div>
      </td>
      <td class="center" style="width:40%;">
        <div class="big">DEMANDE</div>
        <div class="big">DE</div>
        <div class="big">DÉPANNAGE</div>
      </td>
      <td style="width:30%;">
        <div class="label">DATE :</div>
        <div class="line"><?php echo e($date); ?></div>
        <div style="height:14px;"></div>
        <div class="label">HEURE :</div>
        <div class="line"><?php echo e($heure); ?></div>
      </td>
    </tr>
  </table>

  <div class="block">
    <div><span class="label">Numero de parc :</span> <span class="line"><?php echo e($p["num_machine"]); ?></span>
      &nbsp;&nbsp;&nbsp; <span class="label">TYPE de machine :</span> <span class="line"><?php echo e($p["type_machine"]); ?></span>
    </div>
  </div>

  <div class="block">
    <div><span class="label">CLIENT :</span> <span class="line" style="min-width:380px;"><?php echo e($p["client"]); ?></span>
      &nbsp;&nbsp;&nbsp; <span class="label">TÉL :</span> <span class="line"><?php echo e($p["tel"]); ?></span>
    </div>
  </div>

  <div class="block">
    <div><span class="label">INTERLOCUTEUR SUR CHANTIER :</span> <span class="line" style="min-width:520px;"><?php echo e($p["interlocuteur"]); ?></span></div>
  </div>

  <div class="block">
    <div class="label">ADRESSE CHANTIER :</div>
    <div class="box"><?php echo e($p["adresse"]); ?></div>
  </div>

  <div class="block">
    <div class="label">DESCRIPTIF PANNE :</div>
    <div class="box"><?php echo e($p["symptomes"]); ?></div>
  </div>

  <div class="block">
    <div class="label">RÉSOLUTION PANNE :</div>
    <div class="box"><?php echo e($p["solution"]); ?></div>
  </div>

  <div class="block">
    <div><span class="label">NOM DU TECHNICIEN :</span> <span class="line"><?php echo e($p["technicien"]); ?></span></div>
  </div>

  <div class="sign">
    <div class="s">
      <div class="label">NOM ET SIGNATURE CLIENT</div>
      <div class="box"></div>
    </div>
    <div class="s">
      <div class="label">SIGNATURE TECHNICIEN</div>
      <div class="box"></div>
    </div>
  </div>
</div>
<script>window.addEventListener("load",()=>setTimeout(()=>window.print(),400));</script>
</body></html>
