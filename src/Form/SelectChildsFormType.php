<?php

namespace App\Form;

use App\Entity\Child;
use App\Entity\Activity;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SelectChildsFormType extends AbstractType
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
                    'expanded' => true,
                    'query_builder' => function (EntityRepository $er) use ($activity): QueryBuilder {

                        // Create query builder on child entity
                        return $er->createQueryBuilder('c')
                            ->andWhere(':activity MEMBER OF c.activities')
                            ->setParameter('activity', $activity)
                            ->orderBy('c.lastname', 'ASC');
                    },
                    'attr' => [
                        'class' => 'children-checkbox'
                    ]
                ])
                ->add('remove', SubmitType::class, [
                    'label' => 'Supprimer',
                    'attr' => [
                        'class' => 'btn btn-link'
                    ]
                ])
                ->add('move', SubmitType::class, [
                    'label' => 'Déplacer',
                    'attr' => [
                        'class' => 'btn btn-link'
                    ]
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
