<?php
namespace App\Controller; 

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\CovoiturageRepository;

class DemarreCovoiturageController extends AbstractController
{
    #[Route('/demarre/covoiturage', name: 'demarre_covoiturage', methods: ['GET'])]
    public function getCovoiturages(Security $security): Response
    {
        $conducteur = $security->getUser();
        if (!$conducteur) {
            return $this->render('demarrage/encour.html.twig', [
                'message' => 'Utilisateur non connecté'
            ]);
        }
    
        // Filtrer les trajets en attente
        $trajets = $conducteur->getCovoiturages()->filter(function($t) {
            return in_array($t->getStatus(), ['en attente', 'en cours']);
        });
        
        
        if ($trajets->isEmpty()) {
            return $this->render('demarrage/encour.html.twig', [
                'message' => 'Aucun covoiturage en attente'
            ]);
        }
    
        // Sélectionner le premier trajet en attente (ou une autre logique de choix)
        $trajet = $trajets->first(); 
    
        // Vérifier qu'on a bien un trajet
        if (!$trajet) {
            return $this->render('demarrage/encour.html.twig', [
                'message' => 'Aucun covoiturage en attente'
            ]);
        }
    
        // Récupérer les passagers ayant réservé ce trajet
        $reservations = $trajet->getReservations();
        $passagers = [];
    
        foreach ($reservations as $reservation) {
            $passager = $reservation->getUser();
            $passagers[] = [
                'pseudo' => $passager->getPseudo(),
            ];
        }
    
        
        return $this->render('demarrage/encour.html.twig', [
            'trajetId' => $trajet->getId(),
            'dateDepart' => $trajet->getDateDepart(),
            'status' => $trajet->getStatus(),
            'heureDepart' => $trajet->getHeureDepart(),
            'dateArrivee' => $trajet->getDateArrivee(),
            'heureArrivee' => $trajet->getHeureArrivee(),
            'pointArrivee' => $trajet->getPointArrivee(),
            'pointDepart' => $trajet->getPointDepart(),
            'passagers' => $passagers
        ]);
    }
    

    #[Route('/covoiturage/{id}/demarrer', name: 'covoiturage_demarrer', methods: ['POST'])]
public function demarrer(
    int $id,
    Security $security,
    EntityManagerInterface $entityManager,
    CovoiturageRepository $covoiturageRepository
): JsonResponse {
    try {
        $user = $security->getUser();
        if (!$user) {
            return new JsonResponse(["error" => "Utilisateur non connecté"], Response::HTTP_UNAUTHORIZED);
        }

        $covoiturage = $covoiturageRepository->find($id);
        if (!$covoiturage) {
            return new JsonResponse(["error" => "Covoiturage introuvable"], Response::HTTP_NOT_FOUND);
        }

        if ($covoiturage->getUser() !== $user) {
            return new JsonResponse(["error" => "Vous n'êtes pas le conducteur"], Response::HTTP_FORBIDDEN);
        }

        $covoiturage->demarrer();
        $entityManager->flush();

        return new JsonResponse(["message" => "Covoiturage démarré", "status" => $covoiturage->getStatus()], Response::HTTP_OK);
    } catch (\Exception $e) {
        return new JsonResponse(["error" => "Une erreur s'est produite", "details" => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

#[Route('/covoiturage/{id}/annule', name: 'covoiturage_annule', methods: ['POST'])]
public function annuler(
    int $id,
    Security $security,
    EntityManagerInterface $entityManager,
    CovoiturageRepository $covoiturageRepository
): JsonResponse {
    try {
        $user = $security->getUser();
        if (!$user) {
            return new JsonResponse(["error" => "Utilisateur non connecté"], Response::HTTP_UNAUTHORIZED);
        }

        $covoiturage = $covoiturageRepository->find($id);
        if (!$covoiturage) {
            return new JsonResponse(["error" => "Covoiturage introuvable"], Response::HTTP_NOT_FOUND);
        }

        if ($covoiturage->getUser() !== $user) {
            return new JsonResponse(["error" => "Vous n'êtes pas le conducteur"], Response::HTTP_FORBIDDEN);
        }

        $covoiturage->annuler();
        $entityManager->flush();

        return new JsonResponse(["message" => "Covoiturage annulé", "status" => $covoiturage->getStatus()], Response::HTTP_OK);

    } catch (\Exception $e) {
        return new JsonResponse(["error" => "Une erreur s'est produite", "details" => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}


    #[Route('/covoiturage/{id}/stopper', name: 'covoiturage_stopper', methods: ['POST'])]
public function stopper(
    int $id,
    Security $security,
    EntityManagerInterface $entityManager,
    CovoiturageRepository $covoiturageRepository
): JsonResponse {
    $user = $security->getUser();
    if (!$user) {
        return new JsonResponse(["error" => "Utilisateur non connecté"], Response::HTTP_UNAUTHORIZED);
    }

    $covoiturage = $covoiturageRepository->find($id);
    if (!$covoiturage) {
        return new JsonResponse(["error" => "Covoiturage introuvable"], Response::HTTP_NOT_FOUND);
    }

    if ($covoiturage->getUser() !== $user) {
        return new JsonResponse(["error" => "Vous n'êtes pas le conducteur"], Response::HTTP_FORBIDDEN);
    }

    $covoiturage->terminer();
          $entityManager->flush();


    return new JsonResponse(["message" => "Covoiturage stoppé"], Response::HTTP_OK);
}

}
