<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Symfony\Bridge\Doctrine\RegistryInterface;

use App\Entity\Fight;

use App\Pagination\Paginator;

/**
 * @method Fight|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fight|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fight[]    findAll()
 * @method Fight[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FightRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Fight::class);
    }

    /**
     * @return Paginator
     */
    public function findLatest(int $page = 1): Paginator
    {
        $qb = $this->createQueryBuilder('f')
            ->orderBy('f.startTime', 'DESC');

        return (new Paginator($qb))->paginate($page);
    }
}
