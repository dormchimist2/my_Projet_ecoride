<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Covoiturage;
use App\Form\CovoiturageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CovoiturageRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\AvisRepository;




class CovoiturageController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/covoiturage', name: 'covoiturage_index', methods: ['GET', 'POST'])]
public function index(Request $request, CovoiturageRepository $covoiturageRepository): Response
{
    $covoiturages = $covoiturageRepository->findAll();
    $covoiturage = new Covoiturage();
    $form = $this->createForm(CovoiturageType::class, $covoiturage);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        //  Associer l'utilisateur connecté
        $user = $this->getUser();
        if (!$user) {
            throw new \Exception("Utilisateur non connecté.");
        }
        $covoiturage->setUser($user); // Associe le covoiturage à l'utilisateur

        $this->entityManager->persist($covoiturage);
        $this->entityManager->flush();
        $this->addFlash('success', 'Covoiturage créé avec succès !');

        return $this->redirectToRoute('covoiturage_index');
    }

    return $this->render('covoiturage/index.html.twig', [
        'covoiturages' => $covoiturages,
        'form' => $form->createView(),
    ]);
}



#[Route('/covoiturage/{id}', name: 'covoiturage_detail', methods: ['GET'])]
public function detail(int $id, CovoiturageRepository $covoiturageRepository, AvisRepository $avisRepository): Response
{
    // Récupère le covoiturage
    $covoiturage = $covoiturageRepository->find($id);

    if (!$covoiturage) {
        $this->addFlash('error', 'Ce covoiturage n\'existe pas.');
        return $this->redirectToRoute('covoiturage_index');
    }

    // Récupère le conducteur du covoiturage
    $conducteur = $covoiturage->getUser();

    // Calcul de la moyenne des avis pour ce conducteur
    $moyenne = $avisRepository->getMoyenneNoteParConducteur($conducteur);

    // Affichage dans le template
    return $this->render('covoiturage/detail.html.twig', [
        'covoiturage' => $covoiturage,
        'conducteur' => $conducteur,
        'voiture' => $covoiturage->getVoiture(),
        'preferences' => $covoiturage->getUser()->getPreferenceCdt(),
        'moyenne_note' => $moyenne, // Passer la moyenne des avis ici
    ]);
}

    



    #[Route('/covoiturage/{id}/calcul-credit', name: 'covoiturage_calcul_credit', methods: ['GET'])]
public function calculerCredit(int $id, Request $request, CovoiturageRepository $covoiturageRepository): JsonResponse
{
    $nbPlaces = (int) $request->query->get('nbPlaces');
    $covoiturage = $covoiturageRepository->find($id);

    if (!$covoiturage || $nbPlaces <= 0) {
        return new JsonResponse(["error" => "Données invalides"], Response::HTTP_BAD_REQUEST);
    }

    $prixTotal = $covoiturage->getPrix();
    $commissionEcoRide = 2;
    $totalCredits = ($prixTotal + $commissionEcoRide)*$nbPlaces;

    return new JsonResponse(["totalCredits" => $totalCredits]);
}
// Recuperation du credit du passager

#[Route('/passager/solde', name: 'passager_solde', methods: ['GET'])]
public function getSoldePassager(Security $security): JsonResponse
{
    $user = $security->getUser();

    if (!$user) {
        return new JsonResponse(['error' => 'Utilisateur non connecté'], 401);
    }

    $compte = $user->getCompte(); 
    if (!$compte) {
        return new JsonResponse(['error' => 'Compte introuvable'], 404);
    }

    return new JsonResponse(['soldePassager' => $compte->getSolde()]);
}



#[Route('/covoiturage/{id}/reserver', name: 'covoiturage_reserver', methods: ['POST'])]
public function reserver(
    int $id,
    Request $request,
    Security $security,
    EntityManagerInterface $entityManager,
    CovoiturageRepository $covoiturageRepository
): JsonResponse {
    try {
        // Vérification de l'utilisateur connecté
        $user = $security->getUser();
        if (!$user) {
            return new JsonResponse(["error" => "Utilisateur non connecté"], Response::HTTP_UNAUTHORIZED);
        }

        // Récupération du covoiturage
        $covoiturage = $covoiturageRepository->find($id);
        if (!$covoiturage) {
            return new JsonResponse(["error" => "Covoiturage introuvable"], Response::HTTP_NOT_FOUND);
        }

        // Vérification du compte utilisateur
        $compte = $user->getCompte();
        if (!$compte) {
            return new JsonResponse(["error" => "Compte utilisateur introuvable"], Response::HTTP_NOT_FOUND);
        }

        // Lecture des données JSON envoyées
        $data = json_decode($request->getContent(), true);
        if (!$data || !isset($data['nbPlaces']) || !isset($data['totalCredits'])) {
            return new JsonResponse(["error" => "Données JSON invalides"], Response::HTTP_BAD_REQUEST);
        }

        // Récupération et validation des valeurs
        $nbPlacesReserve = (int) $data['nbPlaces'];
        $totalCredits = (float) $data['totalCredits'];

        if ($nbPlacesReserve <= 0) {
            return new JsonResponse(["error" => "Nombre de places invalide"], Response::HTTP_BAD_REQUEST);
        }

        if ($covoiturage->getNbPlace() < $nbPlacesReserve) {
            return new JsonResponse(["error" => "Places insuffisantes"], Response::HTTP_BAD_REQUEST);
        }

        // Vérification du solde
        if ($compte->getSolde() < $totalCredits) {
            return new JsonResponse(["error" => "Crédits insuffisants"], Response::HTTP_BAD_REQUEST);
        }

        // Déduction du passager
        $compte->setSolde($compte->getSolde() - $totalCredits);

        // Vérification du compte du conducteur
        $conducteur = $covoiturage->getUser();
        $conducteurCompte = $conducteur->getCompte();
        if (!$conducteurCompte) {
            return new JsonResponse(["error" => "Compte du conducteur introuvable"], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Ajout au solde du conducteur après déduction de la commission
        $commissionEcoRide = 2;
        $conducteurCompte->setSolde($conducteurCompte->getSolde() + ($totalCredits - $commissionEcoRide * $nbPlacesReserve));

        // Mise à jour des places restantes
        $covoiturage->setNbPlace($covoiturage->getNbPlace() - $nbPlacesReserve);

        // Création de la réservation
        $reservation = new Reservation();
        $reservation->setUser($user);
        $reservation->setCovoiturage($covoiturage);
        $reservation->setNbPlaces($nbPlacesReserve);
        $reservation->setStatus("confirmé");
        $reservation->setTotalPrix($totalCredits);

        $entityManager->persist($reservation);
        $entityManager->flush();

        return new JsonResponse(["message" => "Réservation confirmée"], Response::HTTP_OK);
    } catch (\Exception $e) {
        return new JsonResponse(["error" => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}


}
