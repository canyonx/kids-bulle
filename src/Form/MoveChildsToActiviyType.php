<?php

namespace App\Form;

use App\Entity\Activity;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MoveChildsToActiviyType extends AbstractType
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $activity = $options['activity'];

        $builder
            ->add('activity', EntityType::class, [
                'label' => false,
                'class' => Activity::class,
                'multiple' => false,
                'autocomplete' => true,
                'query_builder' => function (EntityRepository $er) use ($activity): QueryBuilder {
                    // create a new query builder using entity manager
                    $qb = new QueryBuilder($this->em);
                    // Create query builder on child entity
                    return $er->createQueryBuilder('a')
                        ->andWhere('a.dateAt = :date')
                        ->setParameter('date', $activity->getDateAt())
                        ->andWhere('a != :activity')
                        ->setParameter('activity', $activity)
                        ->orderBy('a.category', 'ASC');
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'activity' => false
        ]);
    }
}
