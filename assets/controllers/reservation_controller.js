import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  static targets = ["reservationForm", "nbPlaces", "creditCost", "confirmButton"];

  async openReservationForm(event) {
    event.preventDefault();
  
    const trajetId = event.currentTarget.dataset.trajetId;
    const availableSeats = event.currentTarget.dataset.trajetPlaces;
  
    if (!confirm("Voulez-vous réserver une place pour ce trajet ?")) {
      return;
    }
  
    // Afficher le formulaire
    this.showReservationForm(trajetId, availableSeats);
  }
  
  showReservationForm(trajetId, availableSeats) {
    const formHtml = `
      <div id="reservation-form-${trajetId}" class="reservation-form">
        <label>Nombre de passagers :</label>
        <input type="number" id="nbPlaces-${trajetId}" min="1" max="${availableSeats}" value="1" class="form-control mb-2">
        <button id="validate-button-${trajetId}" class="btn btn-secondary" data-trajet-id="${trajetId}">Valider</button>
        <p id="credit-cost-${trajetId}"></p>
      </div>
      <div id="reservation-calcule-${trajetId}" class="hidden"></div>
    `;
  
    // Ajouter le formulaire sans effacer les autres éléments
    document.querySelector(`#reserve-container-${trajetId}`).insertAdjacentHTML("beforeend", formHtml);
    //  Ajouter un écouteur d'événement sur le bouton "Valider"
    document.getElementById(`validate-button-${trajetId}`).addEventListener("click", (event) => this.calculateCreditCost(event));
  }
  

  async calculateCreditCost(event) {
    const trajetId = event.currentTarget.dataset.trajetId;
    const nbPlaces = parseInt(document.getElementById(`nbPlaces-${trajetId}`).value);

    if (nbPlaces <= 0) {
        document.getElementById(`credit-cost-${trajetId}`).innerText = "Veuillez choisir un nombre de places valide.";
        return;
    }

    try {
        const response = await fetch(`/covoiturage/${trajetId}/calcul-credit?nbPlaces=${nbPlaces}`);
        if (!response.ok) throw new Error("Erreur lors du calcul du coût.");

        const data = await response.json();
        const totalCredits = data.totalCredits;
        console.log("Total credits:", totalCredits);

        // Afficher le coût et les boutons de confirmation
        this.showReservationCalcule(trajetId, totalCredits, nbPlaces);
    } catch (error) {
        document.getElementById(`credit-cost-${trajetId}`).innerText = "Erreur lors du calcul du coût.";
    }
}


showReservationCalcule(trajetId, totalCredits, nbPlaces) {
    const formHtml = `
      <p id="credit-cost-${trajetId}">Coût total : ${totalCredits} crédits</p>
      <button class="btn btn-success" id="pay-button-${trajetId}" data-action="click->reservation#confirmReservation" 
              data-trajet-id="${trajetId}" data-nb-places="${nbPlaces}" data-total-credits="${totalCredits}">Payer</button>
      <button class="btn btn-danger" data-action="click->reservation#cancelReservation" data-trajet-id="${trajetId}">Annuler</button>
    `;

    // Mettre à jour le contenu du formulaire
    document.getElementById(`reservation-calcule-${trajetId}`).innerHTML = formHtml;

    // Afficher le conteneur
    const reservationContainer = document.getElementById(`reservation-calcule-${trajetId}`);
    reservationContainer.classList.remove("hidden");

    // Ajout de l'événement sur le bouton "Payer"
    document.getElementById(`pay-button-${trajetId}`).addEventListener("click", (event) => this.confirmReservation(event));
}



async confirmReservation(event) {
  const trajetId = event.currentTarget.dataset.trajetId;
  const nbPlaces = parseInt(event.currentTarget.dataset.nbPlaces, 10);
  const creditCostElement = parseInt(event.currentTarget.dataset.totalCredits, 10);

  console.log("Test - Crédit coût:", creditCostElement);
  console.log("Test - Trajet ID:", trajetId);
  console.log("Test - Nombre de places:", nbPlaces);

  if (!creditCostElement || !nbPlaces || !trajetId) {
      alert("Erreur : Informations de réservation incomplètes.");
      return;
  }

  try {
      // Vérifier le solde du passager
      const soldeResponse = await fetch('/passager/solde');
      if (!soldeResponse.ok) throw new Error("Impossible de récupérer votre solde.");

      const soldeData = await soldeResponse.json();
      const soldePassager = soldeData.soldePassager;
      console.log("Solde passager:", soldePassager);

      // Vérifier si le passager a assez de crédits
      if (soldePassager < creditCostElement) {
          alert("Vous n'avez pas assez de crédits.");
          return;
      }

      // 🔹 CORRECTION : JSON.stringify() prend un objet
      const data = {
          nbPlaces: nbPlaces,
          totalCredits: creditCostElement
      };
      console.log("Données envoyées:", JSON.stringify(data));

      // Réaliser la réservation
      const response = await fetch(`/covoiturage/${trajetId}/reserver`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(data),
      });

      const responseData = await response.json();
console.log("Réponse du serveur:", responseData);

      if (!response.ok) throw new Error("Erreur lors de la réservation.");

      alert("Réservation confirmée !");

      // Suppression des éléments
      document.getElementById(`reservation-form-${trajetId}`)?.remove();
      document.getElementById(`reservation-calcule-${trajetId}`)?.remove();
  } catch (error) {
      alert("Erreur : " + error.message);
  }
}


}
