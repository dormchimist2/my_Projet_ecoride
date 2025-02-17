<?php
namespace App\EventListener;

use Doctrine\ORM\Event\PostPersistEventArgs;
use App\Entity\User;
use App\Entity\Compte;

class UserListener
{
    public function postPersist(PostPersistEventArgs $event): void
    {
        // Récupérer l'entité User depuis l'argument $event
        $user = $event->getObject(); 

        // Vérifier si l'entité est bien une instance de User
        if ($user instanceof User) {
            // Vérifie si l'utilisateur est un passager avant de lui créditer 50 crédits
            if (in_array("ROLE_PASSAGER", $user->getRoles())) {
                // Créer un nouveau compte pour cet utilisateur
                $compte = new Compte($user);
                $entityManager = $event->getEntityManager();
                $entityManager->persist($compte);  // Persister le compte
                $entityManager->flush();           // Sauvegarder dans la base de données
            }
        }
    }
}
