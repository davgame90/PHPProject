<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @return Event[] Returns an array of Event objects
     */
    public function findByParticipant(User $user): array
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.participants', 'p')
            ->andWhere('p.id = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Event[] Returns an array of Event objects that are public
     */
    public function findByIsPublic(bool $isPublic): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.isPublic = :val')
            ->setParameter('val', $isPublic)
            ->leftJoin('e.owner', 'o')
            ->addSelect('o')
            ->getQuery()
            ->getResult();
    }

        /**
     * @return \Doctrine\ORM\Query Returns a Doctrine Query object
     */
    public function findAllQuery()
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.date', 'DESC')
            ->getQuery();
    }
}