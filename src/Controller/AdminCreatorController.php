<?php
namespace App\Controller;

use App\Entity\Userx;
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
        $user->setEmail($request->get('email'));
        $user->setRoles(['ROLE_EMPLOYE']);
        $hashedPassword = $passwordHasher->hashPassword($user, $request->get('password'));
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        return new Response('Employé créé avec succès.');
    }
}
