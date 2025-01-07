<?php
namespace App\Controller;

use App\Entity\Covoiturage;
use App\Form\CovoiturageType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; // Ajouter l'import pour Request (effet magique)
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CovoiturageRepository;

class CovoiturageController extends AbstractController
{
    #[Route('/covoiturage', name: 'covoiturage_index', methods: ['GET', 'POST'])]
    public function index(Request $request, CovoiturageRepository $covoiturageRepository): Response
    {
        // Récupère tous les covoiturages existants
        $covoiturages = $covoiturageRepository->findAll();

        // Créer un objet Covoiturage pour le formulaire
        $covoiturage = new Covoiturage();
        
        // Créer le formulaire
        $form = $this->createForm(CovoiturageType::class, $covoiturage);
        
        // Traitement de la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrement du nouveau covoiturage
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($covoiturage);
            $entityManager->flush();

            // Message flash de succès
            $this->addFlash('success', 'Covoiturage créé avec succès !');

            // Redirection vers la même page pour voir la liste mise à jour
            return $this->redirectToRoute('covoiturage_index');
        }

        // Renvoie la vue avec le formulaire et les covoiturages
        return $this->render('covoiturage/index.html.twig', [
            'covoiturages' => $covoiturages,
            'form' => $form->createView(),
        ]);
    }
}
