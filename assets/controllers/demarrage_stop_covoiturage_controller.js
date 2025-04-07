import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static values = { trajetId: Number };
    static targets = ["startButton", "stopButton", "status"];

    async demarrer() {
        try {
            const response = await fetch(`/covoiturage/${this.trajetIdValue}/demarrer`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
            });
    
            const result = await response.json();

    
            if (response.ok) {
                this.statusTarget.textContent = result.status;
    
                // Ajoute la classe fade et hide pour cacher le bouton "Démarrer"
                this.startButtonTarget.classList.add("fade", "hide");
    
                // Attends que l'animation soit terminée (0.5s), puis retire le bouton "Démarrer"
                setTimeout(() => {
                    this.startButtonTarget.remove();
                    
                    // Crée dynamiquement le bouton "Stopper"
                    const stopButton = document.createElement("button");
                    stopButton.textContent = "Stopper";
                    stopButton.className = "btn btn-danger mt-3 fade";
                    stopButton.addEventListener("click", () => this.stopper());
    
                    // Ajoute le bouton "Stopper" avec animation
                    this.element.appendChild(stopButton);
    
                    // Donne le temps au bouton "Stopper" d'apparaître avec l'effet
                    setTimeout(() => {
                        stopButton.classList.add("show");
                    }, 10);
    
                }, 500); // Cette valeur doit correspondre au temps de l'animation CSS
    
                alert("Ecoride vous souhaite bonne route");
            } else {
                alert(result.error || "Une erreur est survenue");
            }
        } catch (error) {
            alert("Erreur de connexion au serveur");
            console.error("Erreur :", error);
        }
    }
    
    async annuler() {
        const confirmation = confirm("⚠️ Attention !\n\nL'annulation de ce trajet est irréversible et vous perdrez la rémunération associée.\n\nSouhaitez-vous vraiment annuler ce covoiturage ?");
    
        if (!confirmation) {
            return; // L'utilisateur a cliqué sur "Annuler", on arrête ici.
        }
    
        try {
            const response = await fetch(`/covoiturage/${this.trajetIdValue}/annule`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
            });
    
            const result = await response.json();
    
            if (response.ok) {
                alert("Covoiturage annulé.");
                // Redirection vers la page des covoiturages
                window.location.href = "/covoiturage";
            } else {
                alert(result.error || "Une erreur est survenue");
            }
        } catch (error) {
            alert("Erreur de connexion au serveur");
            console.error("Erreur :", error);
        }
    }
    
    
    
    
    async stopper() {
        const response = await fetch(`/covoiturage/${this.trajetIdValue}/stopper`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
        });
    
        const result = await response.json();
        if (response.ok) {
            this.statusTarget.textContent = result.status;
            this.stopButtonTarget.classList.add("d-none");
        } else {
            alert(result.error || "Une erreur est survenue");
        }
    }
    
}
