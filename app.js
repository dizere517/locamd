// app.js - LOCADOUR (MySQL) - FINAL (sans doublons)

(function () {
  // Date auto
  const dateEl = document.getElementById("date");
  if (dateEl) dateEl.value = new Date().toISOString().split("T")[0];

  // Machines (complète ici)
  const machines = {
    "535": "Pelle 5 tonnes",
    "939": "Pelle 2.5 tonnes",
    "101": "Mini-pelle 1 tonne",
    // "XXX": "Type de machine",
  };

  // State
  let enCours = [];
  let resolus = [];

  // Helpers
  function escapeHtml(str) {
    return String(str ?? "").replace(/[&<>"']/g, (m) => ({
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#039;",
    }[m]));
  }

  async function api(action, data = null, method = null) {
    const url = `api_depannage.php?action=${encodeURIComponent(action)}`;
    const opts = { method: method || (data ? "POST" : "GET"), headers: {} };

    if (data && opts.method === "POST") {
      opts.headers["Content-Type"] = "application/json";
      opts.body = JSON.stringify(data);
    }

    const res = await fetch(url, opts);
    if (!res.ok) {
      let t = "";
      try { t = await res.text(); } catch {}
      throw new Error(`API ${res.status}: ${t || "Erreur"}`);
    }
    return res.json();
  }

  // Type auto
  function detecterTypeMachine() {
    const num = (document.getElementById("num")?.value || "").trim();
    const typeEl = document.getElementById("typeMachine");
    if (!typeEl) return;
    typeEl.value = machines[num] || "Type inconnu (ajoute-la dans app.js)";
  }
  window.detecterTypeMachine = detecterTypeMachine;

  // Tabs
  function openTab(id) {
    document.querySelectorAll(".tabcontent").forEach((t) => (t.style.display = "none"));
    const el = document.getElementById(id);
    if (el) el.style.display = "block";
    refreshListes();
  }
  window.openTab = openTab;

  // Fetch lists
  async function refreshListes() {
    try {
      const enc = await fetch("api_depannage.php?action=list&statut=EN_COURS").then((r) => r.json());
      const his = await fetch("api_depannage.php?action=list&statut=RESOLU").then((r) => r.json());

      enCours = enc.items || [];
      resolus = his.items || [];

      updateListes();
    } catch (e) {
      console.error(e);
      alert("Erreur chargement listes (API). Vérifie XAMPP / MySQL / session.");
    }
  }

  // UI lists
  function updateListes() {
    const listeEncours = document.getElementById("listeEncours");
    if (listeEncours) {
      listeEncours.innerHTML =
        (enCours || [])
          .map((p) => {
            const docsUrl = `documents.php?num=${encodeURIComponent(p.num_machine)}`;
            return `
              <div style="border:1px solid #ccc;padding:15px;margin:10px;border-radius:8px;background:#ffffe0;">
                <div style="display:flex;gap:10px;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;">
                  <div style="min-width:260px;">
                    <strong>${escapeHtml(p.num_machine)} (${escapeHtml(p.type_machine || "")})</strong>
                    - ${escapeHtml(p.date_depannage)}<br>
                    Client : ${escapeHtml(p.client || "non renseigné")} ${p.tel ? `(${escapeHtml(p.tel)})` : ""}<br>
                    Interlocuteur : ${escapeHtml(p.interlocuteur || "non renseigné")}<br>
                  </div>

                  <div style="display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end;">
                    <a href="${docsUrl}" class="btn" style="text-decoration:none; display:inline-block; padding:10px 12px;">Documents</a>
                    <button type="button" class="btn btn-green" style="padding:10px 12px;" onclick="imprimerDepannageEncours(${p.id})">
                      Imprimer fiche dépannage
                    </button>
                  </div>
                </div>

                <div style="margin-top:8px;">
                  <div><b>Symptômes :</b> ${escapeHtml(p.symptomes)}</div>
                </div>

                <div style="margin-top:15px;">
                  <textarea placeholder="Cause exacte trouvée" id="cause_${p.id}" rows="2" style="width:100%;"></textarea>
                  <textarea placeholder="Solution appliquée / Étapes" id="solution_${p.id}" rows="4" style="width:100%;"></textarea>

                  <div style="display:flex;gap:10px;flex-wrap:wrap;justify-content:flex-end;margin-top:10px;">
                    <button type="button" class="btn" style="padding:10px 15px;" onclick="validerFiche(${p.id})">Valider</button>
                    <button type="button" class="btn btn-danger" style="padding:10px 15px;" onclick="supprimerFiche(${p.id})">Supprimer</button>
                  </div>
                </div>
              </div>
            `;
          })
          .join("") || "<p>Aucun dépannage en cours.</p>";
    }

    const listeHistorique = document.getElementById("listeHistorique");
    if (listeHistorique) {
      listeHistorique.innerHTML =
        (resolus || [])
          .map((p) => `
            <div style="border:1px solid #ccc;padding:15px;margin:10px;border-radius:8px;background:#e0ffe0;">
              <div style="display:flex;gap:10px;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;">
                <div style="min-width:260px;">
                  <strong>${escapeHtml(p.num_machine)} (${escapeHtml(p.type_machine || "")})</strong>
                  - ${escapeHtml(p.date_depannage)}<br>
                  Client : ${escapeHtml(p.client || "non renseigné")} ${p.tel ? `(${escapeHtml(p.tel)})` : ""}<br>
                  Interlocuteur : ${escapeHtml(p.interlocuteur || "non renseigné")}<br>
                </div>

                <div style="display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end;">
                  <a href="documents.php?num=${encodeURIComponent(p.num_machine)}" class="btn" style="text-decoration:none; display:inline-block; padding:10px 12px;">Documents</a>
                </div>
              </div>

              <div style="margin-top:8px;"><b>Symptômes :</b> ${escapeHtml(p.symptomes)}</div><br>
              <b>Cause :</b> ${escapeHtml(p.cause || "Non précisée")}<br>
              <b>Solution :</b> ${escapeHtml(p.solution || "Non précisée")}<br>
            </div>
          `)
          .join("") || "<p>Aucun historique.</p>";
    }
  }

  // Create
  async function creerFiche() {
    const date = (document.getElementById("date")?.value || "").trim();
    const num = (document.getElementById("num")?.value || "").trim();
    const type = (document.getElementById("typeMachine")?.value || "").trim();
    const client = (document.getElementById("client")?.value || "").trim();
    const tel = (document.getElementById("tel")?.value || "").trim();
    const interlocuteur = (document.getElementById("interlocuteur")?.value || "").trim();
    const adresse = (document.getElementById("adresse")?.value || "").trim();
    const symptomes = (document.getElementById("symptomes")?.value || "").trim();
    const tech = (document.getElementById("tech")?.value || "").trim();

    if (!date || !num || !symptomes) {
      alert("Date, N° Machine et Symptômes sont obligatoires.");
      return;
    }

    try {
      await api("create", { date, num, type, client, tel, interlocuteur, adresse, symptomes, tech }, "POST");
      alert("✅ Fiche créée !");
      refreshListes();
    } catch (e) {
      console.error(e);
      alert("Erreur création : " + e.message);
    }
  }
  window.creerFiche = creerFiche;

  // Print from form (HTML -> print -> PDF)
  function imprimerFicheDepannageDepuisForm() {
    const p = {
      num: (document.getElementById("num")?.value || "").trim() || "__________",
      type: (document.getElementById("typeMachine")?.value || "").trim(),
      date: (document.getElementById("date")?.value || "").trim(),
      client: (document.getElementById("client")?.value || "").trim(),
      tel: (document.getElementById("tel")?.value || "").trim(),
      interlocuteur: (document.getElementById("interlocuteur")?.value || "").trim(),
      adresse: (document.getElementById("adresse")?.value || "").trim(),
      symptomes: (document.getElementById("symptomes")?.value || "").trim(),
      tech: (document.getElementById("tech")?.value || "").trim(),
      solution: "",
    };

    const params = new URLSearchParams(p);
    window.open(`print_depannage_free.php?${params.toString()}`, "_blank");
  }
  window.imprimerFicheDepannageDepuisForm = imprimerFicheDepannageDepuisForm;

  // Print from EN_COURS (server)
  function imprimerDepannageEncours(id) {
    window.open(`print_depannage.php?id=${encodeURIComponent(id)}`, "_blank");
  }
  window.imprimerDepannageEncours = imprimerDepannageEncours;

  // Delete
  async function supprimerFiche(id) {
    if (!confirm("Supprimer cette fiche ?")) return;
    try {
      await api("delete", { id }, "POST");
      refreshListes();
    } catch (e) {
      console.error(e);
      alert("Erreur suppression : " + e.message);
    }
  }
  window.supprimerFiche = supprimerFiche;

  // Resolve
  async function validerFiche(id) {
    const cause = (document.getElementById(`cause_${id}`)?.value || "").trim();
    const solution = (document.getElementById(`solution_${id}`)?.value || "").trim();

    try {
      await api("resolve", { id, cause, solution }, "POST");
      refreshListes();
    } catch (e) {
      console.error(e);
      alert("Erreur validation : " + e.message);
    }
  }
  window.validerFiche = validerFiche;

  // Init
  document.addEventListener("DOMContentLoaded", () => {
    const numEl = document.getElementById("num");
    if (numEl) numEl.addEventListener("blur", detecterTypeMachine);
    refreshListes();
  });
})();
