import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  static targets = ["results"];

  async openReservationForm(event) {
    event.preventDefault();

    const trajetId = event.currentTarget.dataset.trajetId;
    const availableSeats = event.currentTarget.dataset.trajetPlaces; // Places disponibles

    // Demander confirmation
    if (!confirm("Voulez-vous réserver une place pour ce trajet ?")) {
      return;
    }

    // Afficher le formulaire dynamique
    this.showReservationForm(trajetId, availableSeats);
  }

  showReservationForm(trajetId, availableSeats) {
    const formHtml = `
      <div id="reservation-form-${trajetId}" class="p-3 border rounded bg-light">
        <label for="nbPlaces-${trajetId}"><strong>Nombre de places :</strong></label>
        <input type="number" id="nbPlaces-${trajetId}" min="1" max="${availableSeats}" value="1" class="form-control mb-2">
        <button class="btn btn-success" data-action="click->reservation#confirmReservation" data-trajet-id="${trajetId}">Confirmer</button>
      </div>
    `;

    // Insérer le formulaire sous le bouton
    document.querySelector(`#reserve-container-${trajetId}`).innerHTML = formHtml;
  }

  async confirmReservation(event) {
    const trajetId = event.currentTarget.dataset.trajetId;
    const nbPlaces = document.querySelector(`#nbPlaces-${trajetId}`).value;

    try {
      const response = await fetch(`/covoiturage/${trajetId}/reserver`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify({ nbPlaces }),
      });

      if (!response.ok) {
        throw new Error(await response.text());
      }

      alert("Votre réservation a été confirmée !");
      this.refreshResults();
    } catch (error) {
      alert("Erreur lors de la réservation : " + error.message);
    }
  }

  refreshResults() {
    console.log("Rafraîchir les trajets...");
  }
}
