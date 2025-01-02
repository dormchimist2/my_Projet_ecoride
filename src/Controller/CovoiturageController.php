<?php

namespace App\Controller;
use App\Entity\Covoiturage;
use App\Form\CovoiturageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CovoiturageController extends AbstractController
{
    #[Route('/covoiturage/creer', name: 'covoiturage_creer')]
    public function creer(Request $request, EntityManagerInterface $entityManager): Response
    {
        $covoiturage = new Covoiturage();
        $form = $this->createForm(CovoiturageType::class, $covoiturage);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($covoiturage);
            $entityManager->flush();

            $this->addFlash('success', 'Trajet créé avec succès !');

            return $this->redirectToRoute('covoiturage_creer');
        }

        return $this->render('covoiturage/creer.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/covoiturage', name: 'app_covoiturage')]
    public function index(): Response
    {
        return $this->render('covoiturage/index.html.twig', [
            'controller_name' => 'CovoiturageController',
        ]);
    }
}
