<?php

namespace App\Repository;

use App\Entity\Account;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Account|null find($id, $lockMode = null, $lockVersion = null)
 * @method Account|null findOneBy(array $criteria, array $orderBy = null)
 * @method Account[]    findAll()
 * @method Account[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Account::class);
    }

    public function deleteAll()
    {
        $this->createQueryBuilder('a')
            ->delete(Account::class)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * @return Account[] Returns an array of Account objects
     */
    public function findByName(string $name): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.name = :name')
            ->setParameter('name', $name)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneByName(string $name): ?Account
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
