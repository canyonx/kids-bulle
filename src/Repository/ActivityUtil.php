<?php

namespace App\Repository;

use App\Entity\Child;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;

/**
 *
 * Utilities functions for Activity search
 *
 */
class ActivityUtil
{
    /**
     * Search in Activity by date between
     *
     * @param QueryBuilder $qb
     * @param \DateTimeImmutable|null $dateStart, if null start today
     * @param integer $days
     * @return QueryBuilder
     */
    public static function ByDateBetween(
        QueryBuilder $qb,
        ?\DateTimeImmutable $dateStart = null,
        ?int $days = 7
    ): QueryBuilder {
        if ($dateStart === null) {
            $dateStart = new \DateTimeImmutable("today");
        }
        $dateEnd = new \DateTimeImmutable($dateStart->format("Y-m-d") . "+ $days days");

        return $qb->andWhere('a.dateAt >= :dateStart')
            ->setParameter('dateStart', $dateStart)
            ->andWhere('a.dateAt <= :dateEnd')
            ->setParameter('dateEnd', $dateEnd)
            ->orderBy('a.dateAt', 'ASC');
    }

    /**
     * Search in Activity where User's Childs are present
     *
     * @param QueryBuilder $qb
     * @param User $user
     * @return QueryBuilder
     */
    public static function ByChildsOfUser(QueryBuilder $qb, User $user): QueryBuilder
    {
        return $qb->andWhere(
            $qb->expr()->isMemberOf(':userChilds', 'a.childrens')
        )
            ->setParameter('userChilds', $user->getChilds());
    }

    /**
     * Search Activity for a Child
     *
     * @param QueryBuilder $qb
     * @param Child $child
     * @return QueryBuilder
     */
    public static function ByChild(QueryBuilder $qb, Child $child): QueryBuilder
    {
        return $qb->andWhere(
            $qb->expr()->isMemberOf(':child', 'a.childrens')
        )
            ->setParameter('child', $child);
    }

    /**
     * Search Activity for a Teacher
     *
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    public static function ByTeacher(QueryBuilder $qb, User $user): QueryBuilder
    {
        return  $qb->andWhere('a.teacher = :user')
            ->setParameter('user', $user)
            ->join('a.teacher', 'u')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_TEACHER%');;
    }
}
