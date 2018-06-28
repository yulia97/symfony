<?php

namespace App\Repository;

use App\Entity\Userinfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Userinfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Userinfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Userinfo[]    findAll()
 * @method Userinfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserinfoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Userinfo::class);
    }

//    /**
//     * @return Userinfo[] Returns an array of Userinfo objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Userinfo
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
