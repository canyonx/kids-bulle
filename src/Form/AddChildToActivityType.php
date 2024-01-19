<?php

namespace App\Form;

use App\Entity\Child;
use App\Entity\Activity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddChildToActivityType extends AbstractType
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $activity = $options['activity'];

        if ($activity !== false) {
            $builder
                ->add('childrens', EntityType::class, [
                    'label' => false,
                    'class' => Child::class,
                    'multiple' => true,
                    'query_builder' => function (EntityRepository $er) use ($activity): QueryBuilder {
                        // create a new query builder using entity manager
                        $qb = new QueryBuilder($this->em);
                        // Create query builder on child entity
                        return $er->createQueryBuilder('c')
                            ->andWhere(
                                // is not
                                $qb->expr()->not(
                                    // member of
                                    $qb->expr()->isMemberOf(':activity', 'c.activities')
                                )
                            )
                            ->setParameter('activity', $activity)
                            ->orderBy('c.lastname', 'ASC');
                    }
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
            'activity' => false
        ]);
    }
}
