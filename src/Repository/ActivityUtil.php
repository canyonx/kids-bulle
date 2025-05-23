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
     * @param \DateTimeImmutable|null $dateEnd, if null end today + 31 days
     * @return QueryBuilder
     */
    public static function ByDateBetween(
        QueryBuilder $qb,
        \DateTimeImmutable $dateStart = null,
        \DateTimeImmutable $dateEnd = null
    ): QueryBuilder {
        if ($dateStart === null) {
            $dateStart = new \DateTimeImmutable("today");
        }

        if ($dateEnd === null) {
            $dateEnd = new \DateTimeImmutable($dateStart->format('Y-m-d') . ' + 1 month');
        }

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
        return $qb->leftjoin('a.childrens', 'ch')
            ->addSelect('ch')
            ->andWhere('ch IN (:userChildren)')
            ->setParameter('userChildren', $user->getChilds());
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
        return $qb->leftjoin('a.childrens', 'ch')
            ->addSelect('ch')
            ->andWhere('ch.id = :childId')
            ->setParameter('childId', $child->getId());
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
            ->leftJoin('a.teacher', 'u')
            ->addSelect('u')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_TEACHER%');
    }

    /**
     * Order Activity by Name
     *
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    public static function OrderByDateAndCategory(QueryBuilder $qb): QueryBuilder
    {
        return  $qb->orderBy('a.dateAt', 'ASC')
            ->leftJoin('a.category', 'c')
            ->addSelect('c')
            ->leftJoin('a.childrens', 'chi')
            ->addSelect('chi')
            ->addOrderBy('c.number', 'ASC');
    }
}
