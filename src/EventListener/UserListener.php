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

                // Utiliser la méthode correcte pour récupérer l'EntityManager
                $entityManager = $event->getObjectManager();  // Remplace getEntityManager() par getObjectManager()

                // Persister le compte et sauvegarder dans la base de données
                $entityManager->persist($compte);
                $entityManager->flush(); 
            }
        }
    }
}

