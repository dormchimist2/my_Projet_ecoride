<?php

namespace App\Controller;

use App\Entity\PreferenceCdt;
use App\Form\PreferenceCdtType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/conducteur/preferences')]
class PreferenceCdtController extends AbstractController
{
    #[Route('/modifier', name: 'modifier_preferences')]
    public function modifierPreferences(Request $request, EntityManagerInterface $em): Response
    {
        // Assurez-vous que l'utilisateur est connecté
        $user = $this->getUser();

        if (!$user) {
            // Si l'utilisateur n'est pas connecté, redirigez vers la page de login
            return $this->redirectToRoute('app_login');
        }

        // Récupérer les préférences existantes ou en créer une nouvelle
        $preference = $user->getPreferenceCdt() ?? new PreferenceCdt();
        $preference->setUser($user); // Assurez-vous que la préférence est liée à l'utilisateur

        // Créer le formulaire de préférence
        $form = $this->createForm(PreferenceCdtType::class, $preference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persister les données et sauvegarder
            $em->persist($preference);
            $em->flush();

            // Rediriger vers la page de profil conducteur après la mise à jour
            return $this->render('profile/show.html.twig', [
                'user' => $user, 
            ]);
        }

        // Retourner la vue avec le formulaire
        return $this->render('preferenceCdt/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
