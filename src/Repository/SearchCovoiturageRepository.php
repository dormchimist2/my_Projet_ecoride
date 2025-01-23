<?php
namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SearchCovoiturageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trajet::class);
    }

    /**
     * Recherche des trajets selon les critères spécifiés.
     */
    public function findSearchCovoiturage(
        string $pointDepart,
        string $pointArrivee,
        \DateTime $dateDepart,
        ?\DateTime $heureDepart = null,
        int $nbPlace
    ) {
        $qb = $this->createQueryBuilder('t')
            ->where('t.pointDepart = :pointDepart')
            ->andWhere('t.pointArrivee = :pointArrivee')
            ->andWhere('t.dateDepart = :dateDepart')
            ->andWhere('t.nbPlace >= :nbPlace')
            ->setParameter('pointDepart', $pointDepart)
            ->setParameter('pointArrivee', $pointArrivee)
            ->setParameter('dateDepart', $dateDepart)
            ->setParameter('nbPlace', $nbPlace);

        if ($heureDepart) {
            $qb->andWhere('t.heureDepart >= :heureDepart')
               ->setParameter('heureDepart', $heureDepart);
        }

        // Exécuter la requête Doctrine
        $results = $qb->getQuery()->getResult();
        
        // Transformer les entités en tableaux associatifs
        return array_map(function ($trajet) {
            return [
                'pointDepart' => $trajet->getPointDepart(),
                'pointArrivee' => $trajet->getPointArrivee(),
                'dateDepart' => $trajet->getDateDepart()->format('Y-m-d'),
                'heureDepart' => $trajet->getHeureDepart()->format('H:i'),
                'nbPlace' => $trajet->getNbPlace()
            ];
        }, $results);
    }
}
