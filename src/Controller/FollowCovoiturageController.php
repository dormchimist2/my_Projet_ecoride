<?php
namespace App\Controller; 

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\CovoiturageRepository;
class FollowCovoiturageController extends AbstractController
{#[Route('/follow/covoiturage', name: 'follow_covoiturage', methods: ['GET'])]
    public function getReservations(Security $security): Response
    {
        $passager = $security->getUser();
    
        if (!$passager) {
            return $this->render('follow/covoiturage.html.twig', [
                'message' => 'Vous n\'êtes pas encore connecté'
            ]);
        }
    
        $reservations = $passager->getReservations();
    
        if ($reservations->isEmpty()) {
            return $this->render('follow/covoiturage.html.twig', [
                'message' => 'Vous n\'avez pas encore fait de réservation'
            ]);
        }
    
        // Affiche les IDs des réservations dans la debug toolbar
        foreach ($reservations as $reservation) {
            
        }
        foreach ($reservations as $reservation) {
            $covoiturage = $reservation->getCovoiturage(); 
           
        }
        

    
        return $this->render('follow/covoiturage.html.twig', [
            'passager' => $passager->getPseudo(),
            'reservations' => $reservations,
            'trajet' => $covoiturage
        ]);
    }
    
    

}