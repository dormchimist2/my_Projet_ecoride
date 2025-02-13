<?php
namespace App\Controller;

use App\Entity\Covoiturage;
use App\Form\CovoiturageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; // Ajouter l'import pour Request (effet magique)
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CovoiturageRepository;
use Symfony\Component\HttpKernel\Log\Logger;  // Importation du Logger

class CovoiturageController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;  // Injection  l’EntityManager
    }

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

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Enregistrement du nouveau covoiturage
                $this->entityManager->persist($covoiturage);
                $this->entityManager->flush();

                // Message flash de succès
                $this->addFlash('success', 'Covoiturage créé avec succès !');

                // Redirection vers la même page pour voir la liste mise à jour
                return $this->redirectToRoute('covoiturage_index');
            } 
        }

        // Renvoie la vue avec le formulaire et les covoiturages
        return $this->render('covoiturage/index.html.twig', [
            'covoiturages' => $covoiturages,
            'form' => $form->createView(),
        ]);
    }

        #[Route('/covoiturage/{id}', name: 'covoiturage_detail', methods: ['GET'])]
        public function detail(int $id, EntityManagerInterface $entityManager): Response
        {
            // Récupérer le covoiturage par son ID
            $covoiturage = $entityManager->getRepository(Covoiturage::class)->find($id);
    
            if (!$covoiturage) {
                $this->addFlash('error', 'Ce covoiturage n\'existe pas.');
                return $this->redirectToRoute('app_home');
            }
    
            // Récupérer les informations associées
            $User = $covoiturage->getUser();
            $voiture = $covoiturage->getVoiture();
            $preferenceCdt = $User->getPreferenceCdt();
    
            return $this->render('covoiturage/detail.html.twig', [
                'covoiturage' => $covoiturage,
                'conducteur' => $User,
                'voiture' => $voiture,
                'preferences' => $preferenceCdt,
            ]);
        }
    
    

}
