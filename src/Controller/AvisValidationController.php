<?php

namespace App\Controller;

use App\Entity\Avis;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AvisValidationController extends AbstractController
{
    #[Route('/avis/en-attente', name: 'admin_avis_en_attente')]
    #[IsGranted('ROLE_EMPLOYE')]
    public function avisEnAttente(EntityManagerInterface $em): Response
    {
        $avis = $em->getRepository(Avis::class)->findBy(['status' => Avis::STATUS_PENDING]);
    
        return $this->render('avis/admin/avis_en_attente.html.twig', [
            'avis' => $avis
        ]);
    }
    


    #[Route('/avis/{id}/valider', name: 'admin_avis_valider')]
    #[IsGranted('ROLE_EMPLOYE')]
    public function validerAvis(Avis $avis, EntityManagerInterface $em): Response
    {
        $avis->setStatus('accepted');

        $em->flush();

        $this->addFlash('success', 'Avis validé avec succès.');
        return $this->redirectToRoute('admin_avis_en_attente');
    }

    #[Route('/avis/{id}/rejeter', name: 'admin_avis_rejeter')]
#[IsGranted('ROLE_EMPLOYE')]
public function rejeterAvis(Avis $avis, EntityManagerInterface $em): Response
{
    $avis->setStatus('rejected'); // Marque comme rejeté 
    $em->flush();

    $this->addFlash('danger', 'Avis rejeté.');
    return $this->redirectToRoute('admin_avis_en_attente');
}

    #[Route('/avis/rejeter/enregistrer', name: 'admin_avis_rejeter_enregister')]
public function avisRejeterEnregister(EntityManagerInterface $em): Response
{
    if (!($this->isGranted('ROLE_EMPLOYE') || $this->isGranted('ROLE_ADMIN'))) {
        throw $this->createAccessDeniedException();
    }

    $avis = $em->getRepository(Avis::class)->findBy(['status' => Avis::STATUS_REJECTED]);

    return $this->render('avis/admin/avis_rejeter_enregister.html.twig', [
        'avis' => $avis
    ]);
}

    #[Route('/avis/{id}/restaurer', name: 'admin_avis_restaurer')]
    #[IsGranted('ROLE_EMPLOYE')]
    public function restaurerAvis(int $id, EntityManagerInterface $em): Response
    {
        // Vérifier que l'utilisateur a bien le rôle 'ROLE_EMPLOYE' ou 'ROLE_ADMIN'
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE', 'ROLE_ADMIN');

        // Récupérer l'avis rejeté
        $avis = $em->getRepository(Avis::class)->find($id);

        if ($avis) {
            // Vérifier si l'avis est bien rejeté avant de pouvoir le restaurer
            if ($avis->getStatus() === Avis::STATUS_REJECTED) {
                // Changer le statut de l'avis en "accepté"
                $avis->setStatus(Avis::STATUS_ACCEPTED);

                // Sauvegarder l'avis avec le nouveau statut
                $em->persist($avis);
                $em->flush();

                $this->addFlash('success', 'L\'avis a été accepté et restauré avec succès.');
            } else {
                $this->addFlash('danger', 'Cet avis n\'est pas dans un statut rejeté.');
            }
        } else {
            $this->addFlash('danger', 'Avis introuvable.');
        }

        // Redirige vers la page des avis rejetés
        return $this->redirectToRoute('admin_avis_rejeter_enregister');
    }

    }




