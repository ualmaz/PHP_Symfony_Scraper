<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function preparePostsBeforeParsing()
    {
        $this
            ->createQueryBuilder('p')
            ->update()
            ->set('p.processed', 0)
            ->getQuery()
            ->execute()
        ;

    }

    public function deleteOldPosts()
    {
        $this
            ->createQueryBuilder('p')
            ->delete()
            ->where('p.processed=0')
            ->getQuery()
            ->execute()
            ;
    }

    public function countPages(int $limit): int
    {
        $postCount = $this->createQueryBuilder('p')
            ->select('COUNT(p)')
            ->getQuery()
            ->getScalarResult();

        return $limit;


    }

    public function searchBy($searchData, $limit, $offset): array
    {
        $query = $this->buildSearchQuery($searchData);
        $query
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        return $query->getQuery()->getResult();
    }

    public function countBy($searchData, $limit): int
    {
        $query = $this->buildSearchQuery($searchData);
        $query->select('COUNT(p)');
        $totalCount = $query->getQuery()->getSingleScalarResult();
        return ceil($totalCount / $limit);

    }

    protected function buildSearchQuery($searchData): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p');
        if (!empty($searchData['search'])) {
            $qb
                ->where($qb->expr()->like('p.title', ':text'))
                ->where($qb->expr()->like('p.description', ':text'))
                ->setParameter('text', '%' . $searchData['search'] . '%')
            ;
        }
        return $qb;
    }

    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
