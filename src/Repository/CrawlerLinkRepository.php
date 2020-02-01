<?php

namespace App\Repository;

use App\Entity\CrawlerLink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CrawlerLink|null find($id, $lockMode = null, $lockVersion = null)
 * @method CrawlerLink|null findOneBy(array $criteria, array $orderBy = null)
 * @method CrawlerLink[]    findAll()
 * @method CrawlerLink[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CrawlerLinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CrawlerLink::class);
    }

    // /**
    //  * @return CrawlerLink[] Returns an array of CrawlerLink objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CrawlerLink
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
