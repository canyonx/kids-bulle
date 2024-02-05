<?php

namespace App\Form;

use App\Form\CopyDateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class CopyDateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateAt', CollectionType::class, [
                'label' => 'Dates',
                // each entry in the array will be an "email" field
                'entry_type' => CopyDateType::class,
                // these options are passed to each "email" type
                'entry_options' => [
                    'attr' => [
                        'label' => false
                    ],
                ],
                'allow_add' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // 'data_class' => Activity::class,
        ]);
    }
}
