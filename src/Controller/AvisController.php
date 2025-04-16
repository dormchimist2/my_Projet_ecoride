<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Form\AvisType;
use App\Entity\Covoiturage;
use App\Entity\reservation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

class AvisController extends AbstractController
{
    #[Route('/covoiturage/{id}/avis', name: 'covoiturage_avis_conducteur')]
    public function avis(Request $request, Covoiturage $covoiturage,Security $security, EntityManagerInterface $em)
    {
        $passager = $security->getUser();
        $reservation= $covoiturage->getReservations();
        // Vérif si le user est passager de ce trajet + que le trajet est terminé
        $user = $this->getUser();

       $isPassager = false;
foreach ($covoiturage->getReservations() as $reservation) {
    if ($reservation->getUser() === $passager) {
        $isPassager = true;
        break;
    }
}

if ($covoiturage->getStatus() !== 'terminé' || !$isPassager) {
    throw $this->createAccessDeniedException("Vous ne pouvez pas laisser un avis.");
}


        $avis = new Avis();
// Empêche les doublons
        $existingAvis = $em->getRepository(Avis::class)->findOneBy([
            'auteur' => $user,
            'trajet' => $covoiturage,
        ]);
        
        if ($existingAvis) {
            $this->addFlash('warning', 'Vous avez déjà laissé un avis pour ce trajet.');
            return $this->redirectToRoute('follow_covoiturage');
        }
       //  Sinon, continue normalement 
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avis->setAuteur($user);
            $avis->setConducteur($covoiturage->getUser());
            $avis->setTrajet($covoiturage);

            $em->persist($avis);
            $em->flush();

            $this->addFlash('success', 'Merci pour votre avis !');
            return $this->redirectToRoute('follow_covoiturage'); 
        }

        return $this->render('avis/laisser_avis.html.twig', [
            'form' => $form->createView(),
            'covoiturage' => $covoiturage,
        ]);
    }
}
