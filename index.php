<?php
session_start();
if (!isset($_SESSION["user_id"])) { header("Location: login.html"); exit(); }
?><!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="theme-color" content="#FFD400" />
  <title>Aide Machines Dépannage LOCADOUR</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <main class="container">
    <header class="header bordered">
      <div class="header-inner">
        <img src="logo.jpg" alt="Locadour" class="logo" />
        <div class="header-text">
          <h1>AIDE MACHINES DÉPANNAGE LOCADOUR</h1>
          <p class="sub">Création • Suivi • Historique</p>
        </div>
      </div>
      <div style="margin-top:10px; display:flex; justify-content:flex-end;">
        <a href="logout.php" class="btn" style="text-decoration:none; display:inline-block; padding:10px 14px;">Se déconnecter</a>
      </div>
    </header>

    <section class="options-container">
      <div class="options">
        <input type="radio" id="tab1" name="tab" checked onchange="openTab('creation')" />
        <label for="tab1">1. Créer dépannage</label>

        <input type="radio" id="tab2" name="tab" onchange="openTab('encours')" />
        <label for="tab2">2. Dépannages en cours</label>

        <input type="radio" id="tab3" name="tab" onchange="openTab('historique')" />
        <label for="tab3">3. Historique</label>
      </div>
    </section>

    <section id="creation" class="tabcontent">
      <form class="form-grid" autocomplete="on">
        <div class="section">
          <label for="date">Date (auto)</label>
          <input type="date" id="date" required />
        </div>

        <div class="section">
          <label for="num">N° Machine (Parc)</label>
          <input type="text" id="num" required placeholder="ex: 535" onblur="detecterTypeMachine()" inputmode="numeric" />
        </div>

        <div class="section">
          <label for="typeMachine">Type Machine (auto)</label>
          <input type="text" id="typeMachine" readonly />
        </div>

        <div class="section">
          <label for="client">Client</label>
          <input type="text" id="client" placeholder="Nom du client" />
        </div>

        <div class="section">
          <label for="tel">Tél Client</label>
          <input type="tel" id="tel" placeholder="06..." inputmode="tel" />
        </div>

        <div class="section span-2">
          <label for="interlocuteur">Interlocuteur chantier</label>
          <input type="text" id="interlocuteur" placeholder="Nom / prénom" />
        </div>

        <div class="section span-2">
          <label for="adresse">Adresse chantier</label>
          <input type="text" id="adresse" placeholder="Adresse complète" />
        </div>

        <div class="section span-2">
          <label for="symptomes">Symptômes / panne</label>
          <textarea id="symptomes" rows="6" required placeholder="Décris la panne (symptômes, contexte, voyants, bruits...)"></textarea>
        </div>

        <div class="section">
          <label for="tech">Technicien</label>
          <input type="text" id="tech" placeholder="Nom" />
        </div>

        <div class="section span-2 form-actions">
          <button type="button" class="btn" onclick="creerFiche()">Créer la fiche dépannage</button>
          <button type="button" class="btn btn-green" onclick="imprimerFicheDepannageDepuisForm()">Imprimer la fiche dépannage</button>
        </div>
      </form>
    </section>

    <section id="encours" class="tabcontent" style="display:none;">
      <h3>Dépannages en cours (à valider)</h3>
      <div id="listeEncours"></div>
    </section>

    <section id="historique" class="tabcontent" style="display:none;">
      <h3>Historique des dépannages résolus</h3>
      <div id="listeHistorique"></div>
    </section>
  </main>

  <script src="app.js"></script>
</body>
</html>
