<?php

namespace App\Repository;

use App\Entity\Commentaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commentaire>
 */
class CommentaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commentaire::class);
    }

       /**
        * @return Commentaire[] Returns an array of Commentaire objects
        */
       public function findByVerified(): array
        {
          return $this->createQueryBuilder('c')
               ->andWhere('c.verified = :val')
               ->setParameter('val', "oui")
                ->orderBy('c.createAt', 'DESC')
              ->setMaxResults(3)
                ->getQuery()
               ->getResult()
         ;
       }

       public function findRandomVerifiedComments(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.verified = :verified')
            ->setParameter('verified', 'oui')
            ->orderBy('RAND()')
            ->setMaxResults(3)
            ->orderBy('c.createAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    public function findOneBySomeField($value): ?Commentaire
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
