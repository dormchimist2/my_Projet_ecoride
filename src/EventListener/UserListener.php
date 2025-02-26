<?php
namespace App\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs; // Utilisation de PostPersistEventArgs
use App\Entity\Userx;
use App\Entity\Compte;
use Psr\Log\LoggerInterface;

#[AsEntityListener(event: 'postPersist', entity: Userx::class)]
class UserListener
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function postPersist(Userx $user, PostPersistEventArgs $event): void // Utilisation de PostPersistEventArgs
    {
        // Ici, on reçoit d'abord l'objet Userx puis l'événement PostPersistEventArgs
        if (!$user instanceof Userx) {
            return;
        }

        $entityManager = $event->getObjectManager(); // Récupérer l'EntityManager à partir de l'événement

        $this->logger->info("Nouvel utilisateur créé : ID " . $user->getId());

        $roles = $user->getRoles();

        // Déterminer le solde initial en fonction du rôle
        if (in_array("ROLE_PASSAGER", $roles)) {
            $soldeInitial = 50.00;
        } elseif (in_array("ROLE_CONDUCTEUR", $roles) || in_array("ROLE_ADMIN", $roles)) {
            $soldeInitial = 0.00;
        } else {
            // Si c'est un employé, on ne crée pas de compte
            $this->logger->info("Utilisateur ID {$user->getId()} est un employé, pas de compte créé.");
            return;
        }

        // Vérifier si un compte existe déjà pour éviter les doublons
        if ($entityManager->getRepository(Compte::class)->findOneBy(['user' => $user])) {
            $this->logger->warning("Un compte existe déjà pour l'utilisateur ID: " . $user->getId());
            return;
        }

        // Création et persistance du compte
        $compte = new Compte();
        $compte->setUser($user);
        $compte->setSolde($soldeInitial);

        $entityManager->persist($compte);
        $entityManager->flush();

        $this->logger->info("Compte créé avec {$soldeInitial} crédits pour l'utilisateur ID: " . $user->getId());
    }
}
