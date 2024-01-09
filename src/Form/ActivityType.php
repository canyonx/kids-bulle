<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Child;
use App\Entity\Activity;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class ActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateAt', DateTimeType::class, [
                'label' => 'Date',
                'input' => 'datetime_immutable',
                'widget' => 'single_text'
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'class' => Category::class
            ]);

        if ($options['edit'] === true) {
            $builder->add('teacher', EntityType::class, [
                'label' => 'Moniteur',
                'class' => User::class,
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('u')
                        ->andWhere('u.roles LIKE :role')
                        ->setParameter('role', '%ROLE_TEACHER%');
                }
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
            'edit' => false
        ]);
    }
}
