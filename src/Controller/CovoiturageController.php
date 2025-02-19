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
    public function detail(int $id, CovoiturageRepository $covoiturageRepository): Response
    {
        $covoiturage = $covoiturageRepository->find($id);

        if (!$covoiturage) {
            $this->addFlash('error', 'Ce covoiturage n\'existe pas.');
            return $this->redirectToRoute('covoiturage_index');
        }

        return $this->render('covoiturage/detail.html.twig', [
            'covoiturage' => $covoiturage,
            'conducteur' => $covoiturage->getUser(),
            'voiture' => $covoiturage->getVoiture(),
            'preferences' => $covoiturage->getUser()->getPreferenceCdt(),
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

    $prixTotal = $covoiturage->getPrix() * $nbPlaces;
    $commissionEcoRide = 2;
    $totalCredits = $prixTotal + $commissionEcoRide;

    return new JsonResponse(["totalCredits" => $totalCredits]);
}
// Recuperation du credit du passager

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
    CovoiturageRepository $covoiturageRepository,
    UserRepository $userRepository
): JsonResponse {
    $user = $security->getUser();
    if (!$user) {
        return new JsonResponse(["error" => "Utilisateur non connecté"], Response::HTTP_UNAUTHORIZED);
    }

    $covoiturage = $covoiturageRepository->find($id);
    if (!$covoiturage) {
        return new JsonResponse(["error" => "Covoiturage introuvable"], Response::HTTP_NOT_FOUND);
    }

    $data = json_decode($request->getContent(), true);
    $nbPlaces = $data['nbPlaces'] ?? 1;

    if (!is_int($nbPlaces) || $nbPlaces <= 0) {
        return new JsonResponse(["error" => "Nombre de places invalide"], Response::HTTP_BAD_REQUEST);
    }

    if ($covoiturage->getNbPlace() < $nbPlaces) {
        return new JsonResponse(["error" => "Places insuffisantes"], Response::HTTP_BAD_REQUEST);
    }

    $prixTotal = $covoiturage->getPrix() * $nbPlaces;
    $commissionEcoRide = 2;
    $totalCredits = $prixTotal + $commissionEcoRide;

    if ($user->getCredit() < $totalCredits) {
        return new JsonResponse(["error" => "Crédits insuffisants"], Response::HTTP_BAD_REQUEST);
    }

    $user->setCredit($user->getCredit() - $totalCredits);
    $conducteur = $covoiturage->getUser();
    $conducteur->setCredit($conducteur->getCredit() + $prixTotal);
    $covoiturage->setNbPlace($covoiturage->getNbPlace() - $nbPlaces);

    $reservation = new Reservation();
    $reservation->setUser($user);
    $reservation->setCovoiturage($covoiturage);
    $reservation->setNbPlaces($nbPlaces);
    $reservation->setStatus("confirmé");
    $reservation->setTotalPrix($totalCredits);

    $entityManager->persist($reservation);
    $entityManager->flush();

    return new JsonResponse(["message" => "Réservation confirmée"], Response::HTTP_OK);
}

}
