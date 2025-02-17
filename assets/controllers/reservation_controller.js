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
    // 🎯 Ajouter un écouteur d'événement sur le bouton "Valider"
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
    
    // Récupérer le nombre de places depuis le bouton "Payer"
    const nbPlaces = parseInt(event.currentTarget.dataset.nbPlaces);
    if (isNaN(nbPlaces) || nbPlaces <= 0) {
        alert("Erreur : Impossible de récupérer le nombre de places.");
        return;
    }

    // Extrait le coût total
    const creditCostElement = document.getElementById(`credit-cost-${trajetId}`);
    console.log("Élément crédit trouvé ?", creditCostElement);

    if (!creditCostElement) {
        alert("Erreur : Élément coût total introuvable.");
        return;
    }

    const creditCostText = creditCostElement.innerText;
    console.log("Texte affiché dans #credit-cost:", creditCostText);

    // Capture uniquement les nombres après "Coût total :"
    const totalCreditsMatch = creditCostText.match(/(\d+)/); // Capture uniquement les nombres
    const totalCredits = totalCreditsMatch ? parseInt(totalCreditsMatch[0], 10) : NaN;

    if (isNaN(totalCredits) || totalCredits <= 0) {
        alert("Erreur : Impossible de récupérer le coût total site en travaux veillez nous exusez.");
        return;
    }

    console.log("Nombre de places:", nbPlaces);
    console.log("Total crédits:", totalCredits);

    try {
        // Vérifier le solde du passager
        const soldeResponse = await fetch('/passager/solde');
        if (!soldeResponse.ok) throw new Error("Impossible de récupérer le solde.");
        const soldeData = await soldeResponse.json();
        const soldePassager = soldeData.soldePassager;

        // Vérifier si le passager a assez de crédits
        if (soldePassager < totalCredits) {
            alert("Vous n'avez pas assez de crédits.");
            return;
        }

        // Réaliser la réservation
        const response = await fetch(`/covoiturage/${trajetId}/reserver`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ nbPlaces, totalCredits }),
        });

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
