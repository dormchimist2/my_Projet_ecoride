<?phP
namespace App\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use App\Entity\User;
use App\Entity\Compte;

class UserListener
{
    public function postPersist(User $user, LifecycleEventArgs $event): void
    {
        $entityManager = $event->getEntityManager();

        // Vérifie si l'utilisateur est un passager avant de lui créditer 50 crédits
        if (in_array("ROLE_PASSAGER", $user->getRoles())) {
            $compte = new Compte($user);
            $entityManager->persist($compte);
            $entityManager->flush();
        }
    }
}
