<?php
namespace App\Controller;

use App\Entity\Voiture;
use App\Form\VoitureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/conducteur/voitures')]
class VoitureController extends AbstractController
{
    #[Route('/ajouter', name: 'ajouter_voiture')]
    public function ajouterVoiture(Request $request, EntityManagerInterface $em): Response
    {
        // Vérifier que l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Créer une nouvelle voiture liée à l'utilisateur
        $voiture = new Voiture();
        $voiture->setUser($user);

        // Créer et gérer le formulaire
        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($voiture);
            $em->flush();

            // Ajouter un message de confirmation
            $this->addFlash('success', 'Votre voiture a bien été ajoutée.');

            // Redirection vers la liste des voitures ou le profil
            return $this->redirectToRoute('app_profile_show', ['id' => (int) $user->getId()]);
        }

        // Afficher le formulaire
        return $this->render('voiture/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
