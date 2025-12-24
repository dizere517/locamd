<?php
session_start();
if (!isset($_SESSION["user_id"])) {
  header("Location: login.html");
  exit();
}
$num = $_GET["num"] ?? "";
$num_safe = htmlspecialchars($num, ENT_QUOTES, 'UTF-8');
?><!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Documents machine</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <main class="container">
    <header class="header bordered">
      <div class="header-inner">
        <img src="logo.jpg" alt="Locadour" class="logo" />
        <div class="header-text">
          <h1>Documents</h1>
          <p class="sub">Machine : <b><?php echo $num_safe ? $num_safe : "—"; ?></b></p>
        </div>
      </div>
      <div style="margin-top:10px; display:flex; justify-content:space-between; gap:10px; flex-wrap:wrap;">
        <a href="index_v3.php" class="btn" style="text-decoration:none; display:inline-block; padding:10px 14px;">← Retour</a>
        <a href="logout2.php" class="btn" style="text-decoration:none; display:inline-block; padding:10px 14px;">Se déconnecter</a>
      </div>
    </header>

    <section style="margin-top:12px;">
      <h3>Ajouter un document (PDF, photo, etc.)</h3>
      <form action="upload_document.php" method="POST" enctype="multipart/form-data" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <input type="hidden" name="num" value="<?php echo $num_safe; ?>" />
        <input type="file" name="file" required />
        <button class="btn btn-green" type="submit">Uploader</button>
      </form>
      <p style="color:#666;margin-top:8px;">Les fichiers sont stockés dans <code>/uploads</code> et référencés en base.</p>
    </section>

    <section style="margin-top:18px;">
      <h3>Liste des documents</h3>
      <div id="docsList"></div>
    </section>
  </main>

<script>
async function loadDocs() {
  const num = new URLSearchParams(location.search).get("num") || "";
  if (!num) {
    document.getElementById("docsList").innerHTML = "<p>Aucune machine sélectionnée.</p>";
    return;
  }
  const r = await fetch("api_documents.php?num=" + encodeURIComponent(num));
  if (!r.ok) {
    document.getElementById("docsList").innerHTML = "<p>Erreur chargement.</p>";
    return;
  }
  const data = await r.json();
  const items = data.items || [];
  if (!items.length) {
    document.getElementById("docsList").innerHTML = "<p>Aucun document.</p>";
    return;
  }
  document.getElementById("docsList").innerHTML = items.map(d => `
    <div style="border:1px solid #ccc;padding:12px;margin:10px 0;border-radius:10px;background:#fff;">
      <div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;">
        <div>
          <b>${d.original_name}</b><br>
          <span style="color:#666;">${d.uploaded_at}</span>
        </div>
        <div style="display:flex;gap:8px;">
          <a class="btn" style="text-decoration:none; padding:10px 12px;" href="uploads/${encodeURIComponent(d.stored_name)}" target="_blank">Ouvrir</a>
        </div>
      </div>
    </div>
  `).join("");
}
loadDocs();
</script>
</body>
</html>
