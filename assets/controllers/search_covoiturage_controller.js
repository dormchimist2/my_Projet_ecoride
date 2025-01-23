import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  static targets = ["pointDepart", "pointArrivee", "dateDepart", "heureDepart", "results", "noResults"];

  async search(event) {
    event.preventDefault();

    const pointDepart = this.pointDepartTarget.value.trim();
    const pointArrivee = this.pointArriveeTarget.value.trim();
    const dateDepart = this.dateDepartTarget.value.trim();
    const heureDepart = this.hasHeureDepartTarget ? this.heureDepartTarget.value.trim() : null;

    // Validation des champs
    if (!pointDepart || !pointArrivee || !dateDepart) {
      this.resultsTarget.innerHTML = `<p class="text-danger">Tous les champs obligatoires doivent être renseignés.</p>`;
      return;
    }

    // Affichage du message de chargement
    this.resultsTarget.innerHTML = "<p>Recherche en cours...</p>";

    try {
      // Construction de l'URL de l'API
      const heureDepartQuery = heureDepart ? `&heureDepart=${encodeURIComponent(heureDepart)}` : "";
      const response = await fetch(
        `/api/search?pointDepart=${encodeURIComponent(pointDepart)}&pointArrivee=${encodeURIComponent(pointArrivee)}&dateDepart=${encodeURIComponent(dateDepart)}${heureDepartQuery}`
      );

      // Gestion des erreurs de réponse
      if (!response.ok) {
        if (response.status === 404) {
          throw new Error("Aucun trajet trouvé pour les critères spécifiés.");
        } else if (response.status === 400) {
          throw new Error("Paramètres invalides. Vérifiez les données saisies.");
        } else {
          throw new Error(`Erreur serveur : ${response.status}`);
        }
      }

      // Lecture des données
      const data = await response.json();

      // Gestion des résultats
      if (data.exactMatches && data.exactMatches.length > 0) {
        this.displayResults(data.exactMatches, "covoiturage correspondants");
      } else if (data.suggestedMatches && data.suggestedMatches.length > 0) {
        this.displayResults(data.suggestedMatches, "Suggestions de covoiturage");
      } else {
        this.resultsTarget.innerHTML = `<p>Aucun trajet trouvé pour les critères spécifiés.</p>`;
      }
    } catch (error) {
      console.error("Erreur lors de la recherche :", error);
      this.resultsTarget.innerHTML = `
        <p class="text-danger">Impossible de compléter la recherche. Vérifiez votre connexion ou réessayez plus tard.</p>
      `;
    }
  }

  // Méthode pour afficher les résultats
  displayResults(results, title) {
    this.resultsTarget.innerHTML = `
      <h4>${title}</h4>
      <ul class="trajet-list">
        ${results
          .map(
            (trajet) => `
              <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                  <strong>${trajet.pointDepart} ➡️ ${trajet.pointArrivee}</strong>
                </div>
                <div class="card-body">
                  <ul class="list-group list-group-flush">
                    
                  <li class="list-group-item">
                      <strong>Date de départ :</strong> 
                      <span>${trajet.dateDepart}</span> à <span>${trajet.heureDepart || "Non spécifiée"}</span>
                    </li>

                    <li class="list-group-item">
                      <strong>Date d'arrivée :</strong> 
                      <span>${trajet.dateArrivee}</span> à <span>${trajet.heureArrivee || "Non spécifiée"}</span>
                    </li>
                    
                    <li class="list-group-item">
                      <strong>Places disponibles :</strong> 
                      <span>${trajet.nbPlace}</span>
                    </li>
                    <li class="list-group-item">
                      <strong>Prix :</strong> 
                      <span>${trajet.prix}</span>
                    </li>
                  </ul>
                </div>
                <div class="card-footer text-end">
                  <button class="btn btn-primary">Réserver</button>
                </div>
              </div>
            `
          )
          .join("")}
      </ul>
    `;
    this.noResultsTarget.classList.add("hidden");
  }
}
