<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Symfony\Bridge\Doctrine\RegistryInterface;

use App\Entity\User;

use App\Pagination\Paginator;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @return Paginator
     */
    public function findLatestAndSortByPoints(int $page = 1): Paginator
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.points > 0')
            ->addOrderBy('u.points', 'DESC')
            ->addOrderBy('u.username', 'ASC');

        return (new Paginator($qb))->paginate($page);
    }
}
