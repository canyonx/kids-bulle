<?php

namespace App\Form;

use App\Entity\Activity;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class MoveChildsToActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $activity = $options['activity'];

        // Validation stricte de l'option 'activity'
        if (!$activity instanceof Activity) {
            throw new InvalidOptionsException('The "activity" option must be an instance of Activity.');
        }

        $builder
            ->add('activity', EntityType::class, [
                'label' => 'Sélectionnez une activité',
                'class' => Activity::class,
                'multiple' => false,
                'expanded' => false, // Utilise un select HTML
                'query_builder' => function (EntityRepository $er) use ($activity) {
                    $startOfDay = (clone $activity->getDateAt())->setTime(0, 0, 0);
                    $endOfDay = (clone $activity->getDateAt())->setTime(23, 59, 59);

                    return $er->createQueryBuilder('a')
                        ->andWhere('a.dateAt BETWEEN :start AND :end')
                        ->setParameter('start', $startOfDay)
                        ->setParameter('end', $endOfDay)
                        ->andWhere('a != :activity')
                        ->setParameter('activity', $activity)
                        ->orderBy('a.category', 'ASC');
                },
                'choice_label' => function (Activity $activity) {
                    $time = $activity->getDateAt()->format('H:i');
                    $titleOrCategory = $activity->getTitle() ?: $activity->getCategory()->getName();
                    $count = count($activity->getChildrens());
                    $suffix = $count === 1 ? 'inscrit' : 'inscrits';
                    return sprintf('%s - %s - %d %s', $time, $titleOrCategory, $count, $suffix);
                },
                'placeholder' => 'Choisissez une activité',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null, // Pas de classe de données associée
            'activity' => null, // Option par défaut
        ]);

        // Validation stricte de l'option 'activity'
        $resolver->setAllowedTypes('activity', ['null', Activity::class]);
    }
}
