<?php
// src/Controller/AppiBridgeController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\SecurityBundle\Security;

class AppiBridgeController extends AbstractController
{
    #[Route('/messagerie/fichier/{nom}', name: 'messagerie_fichier')]
public function afficherFichier(string $nom): Response
{
    // Liste blanche des fichiers autorisés
    $fichiersAutorises = [
        'formulaire.html',
        'voir_conversation.php',
        
    ];

    // Refuse si le nom ne correspond pas à un fichier autorisé
    if (!in_array($nom, $fichiersAutorises)) {
        throw $this->createNotFoundException("Fichier non autorisé : $nom");
    }

    // Chemin absolu sécurisé
    $chemin = $this->getParameter('kernel.project_dir') . '/public/ecorideMessageProject/' . $nom;

    if (!file_exists($chemin)) {
        throw $this->createNotFoundException("Fichier introuvable : $nom");
    }

    // Pour les fichiers PHP, on capture leur sortie avec ob_start()
    ob_start();
    include $chemin;
    $contenu = ob_get_clean();

    return new Response($contenu);
}



#[Route('/messagerie-appi', name: 'appi_unified')]
public function showUnified(Security $security): Response
{
    $user = $security->getUser();  // Récupère l'utilisateur connecté
    
    if (!$user) {
        // Si l'utilisateur n'est pas connecté, redirige ou gère le cas
        return $this->redirectToRoute('app_login');  // Redirige vers la page de connexion
    }

    $email = $user->getEmail(); // ou ->getUserIdentifier()
    
    return $this->render('messagerie/appi_embed.html.twig', [
        'email' => urlencode($email),
    ]);
}

}
