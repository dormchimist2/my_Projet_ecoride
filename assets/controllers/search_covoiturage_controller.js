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
  
      // Vérification des erreurs HTTP
      if (!response.ok) {
        throw await this.handleErrorResponse(response);
      }
  
      // Lecture des données
      const data = await response.json();
  
      // Gestion des résultats
      if (data.exactMatches && data.exactMatches.length > 0) {
        this.displayResults(data.exactMatches, "Covoiturage correspondant");
      } else if (data.suggestedMatches && data.suggestedMatches.length > 0) {
        this.displayResults(data.suggestedMatches, "Suggestions de covoiturage");
      } else {
        this.resultsTarget.innerHTML = `<p>Aucun trajet trouvé pour les critères spécifiés.</p>`;
      }
  
    } catch (error) {
      console.error("Erreur lors de la recherche :", error);
  
      let errorMessage = "Impossible de compléter la recherche. Vérifiez votre connexion ou changez votre itinéraire.";
  
      if (error.message) {
        errorMessage += `<br><small class="text-muted">${error.message}</small>`;
      }
  
      this.resultsTarget.innerHTML = `
        <div class="alert alert-danger" role="alert">
          ${errorMessage}
        </div>
      `;
    }
  }
  
  /**
   * Gère les erreurs HTTP en récupérant le message d'erreur approprié.
   */
  async handleErrorResponse(response) {
    let errorMessage = "Une erreur inconnue s'est produite.";
  
    if (response.status === 404) {
      errorMessage = "Aucun trajet trouvé pour les critères spécifiés.";
    } else if (response.status === 400) {
      errorMessage = "Paramètres invalides. Vérifiez les données saisies.";
    } else if (response.status === 500) {
      // Vérifie si le serveur retourne du JSON avec un message d'erreur
      if (response.headers.get("content-type")?.includes("application/json")) {
        try {
          const errorData = await response.json();
          errorMessage = errorData.error || "Erreur interne du serveur lors de la recherche.";
        } catch (e) {
          errorMessage = "Erreur interne du serveur, impossible de récupérer les détails.";
        }
      } else {
        errorMessage = "Erreur interne du serveur.";
      }
    } else {
      errorMessage = `Erreur serveur : ${response.status}`;
    }
  
    return new Error(errorMessage);
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
                    <span>${trajet.dateDepart || "Non spécifiée"}</span> à 
                    <span>${trajet.heureDepart || "Non spécifiée"}</span>
                  </li>

                  <li class="list-group-item">
                    <strong>Places disponibles :</strong> 
                    <span>${trajet.nbPlace || "Non spécifié"}</span>
                  </li>

                  <li class="list-group-item">
                    <strong>Prix :</strong> 
                    <span>${trajet.prix ? `${trajet.prix}` : "Non spécifié"}</span>
                  </li>
                </ul>
              </div>
              <div class="card-footer text-end">
                <a href="/covoiturage/${trajet.id}" class="btn btn-primary">Détail</a>
                <button class="btn btn-success" 
                        data-controller="reservation"
                        data-action="click->reservation#openReservationForm"
                        data-trajet-id="${trajet.id}"
                        data-trajet-places="${trajet.nbPlace || 1}">
                  Réserver
                </button>
                <div id="reserve-container-${trajet.id}"></div>
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