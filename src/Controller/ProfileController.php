<?php
namespace App\Controller;

use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityManagerInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile/{id}', name: 'app_profile_show', methods: ['GET'])]

    public function show( $id, UserInterface $currentUser, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur à partir de la base de données
        $user = $entityManager->getRepository(User::class)->find($id);
       
        // Vérifier si l'utilisateur existe
        if (!$user) {
            // Si l'utilisateur n'existe pas, afficher une erreur
            $this->addFlash('error', 'L\'utilisateur demandé n\'existe pas.');
            return $this->redirectToRoute('app_home'); // Rediriger vers la page d'accueil
        }

        // Vérifier si l'utilisateur connecté est bien celui qui est demandé dans l'URL
        if ($user !== $currentUser && !in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            // Si l'utilisateur n'est pas le même ou n'est pas administrateur, rediriger
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à voir ce profil.');
            return $this->redirectToRoute('app_home'); // Rediriger vers la page d'accueil ou autre
        }

        if ($user) {
            // Vérifier si l'utilisateur a le rôle "ROLE_CONDUCTEUR"
            if (in_array('ROLE_CONDUCTEUR', $user->getRoles())) {
                if ($user->getVoitures() === null || $user->getVoitures()->count() === 0) {
                    return $this->redirectToRoute('ajouter_voiture');
                }
                
                if ($user->getPreferenceCdt() === null) {
                    return $this->redirectToRoute('modifier_preferences');
                }
                
            
                // Afficher directement le profil au lieu de rediriger à nouveau
                return $this->render('profile/show.html.twig', [
                    'user' => $user, 
                ]);
            }
            

        }
        
        // Si l'utilisateur est autorisé, afficher la vue du profil avec les données de l'utilisateur
        return $this->render('profile/show.html.twig', [
            'user' => $user, // Passer les données utilisateur à la vue
        ]);
    }
}
