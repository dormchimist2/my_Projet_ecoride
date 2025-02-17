<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        // Vérifier si un utilisateur est déjà connecté
        $user = $this->getUser();
        if ($user) {
            // Vérifier si l'utilisateur a le rôle "ROLE_CONDUCTEUR"
        
            // Utiliser la session pour rediriger vers la bonne page après connexion
            $targetUrl = $request->getSession()->get('_target_path', $this->generateUrl('app_home'));

            return $this->redirect($targetUrl);
        }

        // Gestion des erreurs d'authentification
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Cette méthode est gérée par le pare-feu de sécurité.');
    }
}
