<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Symfony\Bridge\Doctrine\RegistryInterface;

use App\Entity\User;

use App\Entity\Fight;
use App\Entity\FightBet;

use App\Pagination\Paginator;

/**
 * @method FightBet|null find($id, $lockMode = null, $lockVersion = null)
 * @method FightBet|null findOneBy(array $criteria, array $orderBy = null)
 * @method FightBet[]    findAll()
 * @method FightBet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FightBetRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FightBet::class);
    }

    /**
     * @return Paginator
     */
    public function findLatestByUser(User $user, int $page = 1): Paginator
    {
        $qb = $this->createQueryBuilder('b')
            ->where('b.user = :user')
            ->setParameter('user', $user)
            ->orderBy('b.id', 'DESC');

        return (new Paginator($qb))->paginate($page);
    }

    /**
     * @return Paginator
     */
    public function findLatestByFight(Fight $fight, int $page = 1): Paginator
    {
        $qb = $this->createQueryBuilder('b')
            ->where('b.fight = :fight')
            ->setParameter('fight', $fight)
            ->orderBy('b.id', 'DESC');

        return (new Paginator($qb))->paginate($page);
    }
}
