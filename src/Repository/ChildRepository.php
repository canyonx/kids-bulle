<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Child;
use App\Entity\Activity;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Child>
 *
 * @method Child|null find($id, $lockMode = null, $lockVersion = null)
 * @method Child|null findOneBy(array $criteria, array $orderBy = null)
 * @method Child[]    findAll()
 * @method Child[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChildRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Child::class);
    }

    public function add(Child $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Child $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Retourne les enfants inscrits à une activité, en mettant en priorité ceux du user donné,
     * puis en les triant tous par date de naissance (birthAt) croissante.
     *
     * @param Activity $activity L'activité dont on veut les enfants inscrits
     * @param User|null $user Le user dont les enfants doivent apparaître en premier (facultatif)
     *
     * @return Child[] Liste triée d'enfants inscrits à l'activité
     */
    public function findByActivityOrderedByUserAndBirth(Activity $activity, ?User $user = null): array
    {
        $qb = $this->createQueryBuilder('c')
            // Jointure entre les enfants et les activités auxquelles ils sont inscrits
            ->innerJoin('c.activities', 'a')
            ->andWhere('a = :activity')
            ->setParameter('activity', $activity);

        // Si un user est fourni, on ajoute une colonne virtuelle pour prioriser ses enfants
        if ($user !== null) {
            $qb->addSelect(
                '(CASE WHEN c.user = :user THEN 0 ELSE 1 END) AS HIDDEN user_priority'
            )
                ->setParameter('user', $user)
                ->orderBy('user_priority', 'ASC')  // Les enfants du user seront listés en premier
                ->addOrderBy('c.birthAt', 'DESC');  // Puis tous triés par birthAt (du plus jeune au plus vieux)
        } else {
            // Si aucun user donné, on trie juste par date de naissance
            $qb->orderBy('c.birthAt', 'DESC');
        }

        return $qb->getQuery()->getResult();
    }



    //    /**
    //     * @return Child[] Returns an array of Child objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Child
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
