<?php

namespace App\Repository;

use App\Entity\Covoiturage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Covoiturage>
 */
class CovoiturageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Covoiturage::class);
    }

    /**
     * Recherche des trajets selon les critères spécifiés.
     */
    public function findTrajets(
        string $pointDepart,
        string $pointArrivee,
        \DateTime $dateDepart,
        ?\DateTime $heureDepart = null,
       
    ): array {
        $qb = $this->createQueryBuilder('c')
            ->where('LOWER(c.pointDepart) = LOWER(:pointDepart)')
            ->andWhere('LOWER(c.pointArrivee) = LOWER(:pointArrivee)')
            ->andWhere('c.dateDepart = :dateDepart')
           
            ->setParameter('pointDepart', trim(strtolower($pointDepart)))
            ->setParameter('pointArrivee', trim(strtolower($pointArrivee)))
            ->setParameter('dateDepart', $dateDepart);
            
    
        if ($heureDepart) {
            // cherche covoiturage égale à l'heure demandée ou Cherche des trajets avec heure différente 
            $qb->andWhere('(c.heureDepart = :heureDepart OR c.heureDepart != :heureDepart)')
               ->setParameter('heureDepart', $heureDepart);
        }
    
        $qb->orderBy('c.dateDepart', 'ASC')
           ->addOrderBy('c.heureDepart', 'ASC');
    
           $results = $qb->getQuery()->getResult(); // Obtenez les objets Covoiturage

    
        if (empty($results)) {
            throw new \Exception('Aucun trajet trouvé avec les critères spécifiés.');
        }
        
        return array_map(function (Covoiturage $trajet) {
            return [
                'id' => $trajet->getId(),
                'pointDepart' => $trajet->getPointDepart(),
                'pointArrivee' => $trajet->getPointArrivee(),
                'dateDepart' => $trajet->getDateDepart()->format('Y-m-d'),
                'dateArrivee' => $trajet->getDateArrivee()->format('Y-m-d'),
                'heureDepart' => $trajet->getHeureDepart() ? $trajet->getHeureDepart()->format('H:i') : null,
                'heureArrivee' => $trajet->getHeureArrivee() ? $trajet->getHeureArrivee()->format('H:i') : null,
                'nbPlace' => $trajet->getNbPlace(),
                'prix' => $trajet->getPrix(),
            ];
        }, $results);
    }
    
    
}
