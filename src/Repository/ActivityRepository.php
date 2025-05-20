<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Child;
use App\Entity\Activity;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Activity>
 *
 * @method Activity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activity[]    findAll()
 * @method Activity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    public function add(Activity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Activity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Activity[] Returns an array of Activity objects
     */
    public function findByDateBetween(\DateTimeImmutable $dateStart = null, \DateTimeImmutable $dateEnd = null): array
    {
        $qb = $this->createQueryBuilder('a');
        ActivityUtil::ByDateBetween($qb, $dateStart, $dateEnd);
        ActivityUtil::OrderByDateAndCategory($qb);
        // dd($qb->getQuery());
        return $qb->getQuery()
            ->getResult();
    }

    /**
     * @return Activity[] Returns an array of Activity objects
     */
    public function findByUser(User $user, \DateTimeImmutable $dateStart = null): array
    {
        $qb = $this->createQueryBuilder('a');
        ActivityUtil::ByDateBetween($qb, $dateStart);
        ActivityUtil::ByChildsOfUser($qb, $user);
        ActivityUtil::OrderByDateAndCategory($qb);
        return $qb->getQuery()
            ->getResult();
    }

    /**
     * @return Activity[] Returns an array of Activity objects
     */
    public function findByChild(Child $child, \DateTimeImmutable $dateStart = null): array
    {
        $qb = $this->createQueryBuilder('a');
        ActivityUtil::ByDateBetween($qb, $dateStart);
        ActivityUtil::ByChild($qb, $child);
        ActivityUtil::OrderByDateAndCategory($qb);
        return $qb->getQuery()
            ->getResult();
    }

    /**
     * @return Activity[] Returns an array of Activity objects
     */
    public function findByTeacher(User $user, \DateTimeImmutable $dateStart = null): array
    {
        $qb = $this->createQueryBuilder('a');
        ActivityUtil::ByDateBetween($qb, $dateStart);
        ActivityUtil::ByTeacher($qb, $user);
        ActivityUtil::OrderByDateAndCategory($qb);
        return $qb->getQuery()
            ->getResult();
    }

    /**
     * Retourne un tableau de dates 
     * Premier jour du mois ou il y a des activités
     *
     * @return array
     */
    public function findAllMonths(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT DISTINCT DATE_FORMAT(date_at, "%Y-%m-01") AS month FROM activity ORDER BY month ASC';

        $stmt = $conn->executeQuery($sql);

        return array_map(
            fn($row) => new \DateTimeImmutable($row['month']),
            $stmt->fetchAllAssociative()
        );
    }

    //    /**
    //     * @return Activity[] Returns an array of Activity objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Activity
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
