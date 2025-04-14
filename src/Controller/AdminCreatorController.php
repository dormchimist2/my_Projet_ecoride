<?php

namespace App\Controller;

use App\Entity\Userx;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminCreatorController extends AbstractController
{
    #[Route('/admin/create-employee', name: 'admin_create_employee')]
    public function createEmployee(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = new Userx();

        $form = $this->createForm(UserType::class, $user, [
            'is_admin_creation' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            // Définir un rôle spécifique, ici "employé"
            $user->setRoles(['ROLE_EMPLOYE']);

            // Hachage du mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Employé créé avec succès');
            return $this->redirectToRoute('admin_create_employee');
        }

        return $this->render('user/admin/create_employee.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
