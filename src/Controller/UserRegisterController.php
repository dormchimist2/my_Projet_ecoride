<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserRegisterController extends AbstractController
{
    #[Route('/user/register', name: 'app_user_register')]
    public function userRegister(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        // Créez une nouvelle instance de l'entité User
        $user = new User();

        // Créez le formulaire en utilisant UserType
        $form = $this->createForm(UserType::class, $user);

        // Traitez la requête pour remplir le formulaire
        $form->handleRequest($request);

        // Vérifiez si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérez les données soumises depuis le formulaire
            $user = $form->getData();

            // Hash du mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            try {
                // Enregistrez l'utilisateur dans la base de données
                $entityManager->persist($user);
                $entityManager->flush();

                // Message flash de succès
                $this->addFlash('success', sprintf('Bienvenue %s, votre compte a été créé avec succès !', $user->getFirstName()));

                // Redirigez vers une page d'accueil ou une autre route
                return $this->redirectToRoute('app_home');
            } catch (\Exception $e) {
                // Gestion des erreurs en cas de problème avec la base de données
                $this->addFlash('danger', 'Une erreur est survenue lors de la création de votre compte.');
            }
        }

        // Affichez le formulaire dans la vue
        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/users', name: 'app_users_list', methods: ['GET'])]
public function list(UserRepository $userRepository): Response
{
    // Récupérer tous les utilisateurs
    $users = $userRepository->findAll();

    // Vérifiez si la liste est vide
    if (empty($users)) {
        $this->addFlash('info', 'Aucun utilisateur trouvé.');
    }

    return $this->render('user/list.html.twig', [
        'users' => $users, // Passez les utilisateurs à la vue
    ]);
}




}

