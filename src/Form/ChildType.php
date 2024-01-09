<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Child;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ChildType extends AbstractType
{
    public function __construct(
        private Security $security
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User */
        $user = $this->security->getUser();



        if ($options['option'] === 'default') {
            $builder
                ->add('firstname', TextType::class, [
                    'label' => 'Prénom',
                    'attr' => [
                        'placeholder' => 'John'
                    ]
                ])
                ->add('lastname', TextType::class, [
                    'label' => 'Nom',
                    'data' => $user->getLastname()
                ])
                ->add('birthAt', DateType::class, [
                    'label' => 'Date de naissance',
                    'input'  => 'datetime_immutable',
                    'widget' => 'single_text',
                ])
                ->add('levelSki', ChoiceType::class, [
                    'label' => 'Ski level',
                    'choices' => [
                        'débutant' => 'débutant',
                        'flocon' => 'flocon',
                        '1e étoile' => '1e étoile',
                        '2e étoile' => '2e étoile',
                        '3e étoile' => '3e étoile',
                        'étoile de bronze' => 'étoile de bronze',
                        'étoile d\'argent' => 'étoile d\'argent',
                        'étoile d\'or' => 'étoile d\'or'
                    ],
                    'required' => false
                ])
                ->add('levelSlalom', ChoiceType::class, [
                    'label' => 'Slalom level',
                    'choices' => [
                        'flèchette' => 'flèchette',
                        'flèche de bronze' => 'flèche de bronze',
                        'flèche d\'argent' => 'flèche d\'argent',
                        'flèche d\'or' => 'flèche d\'or',
                    ],
                    'required' => false
                ])
                ->add('levelSnowboard', ChoiceType::class, [
                    'label' => 'Snowboard level',
                    'choices' => [
                        'Goomie rider' => 'Goomie rider',
                        'Rookie rider' => 'Rookie rider',
                        'Izzy rider' => 'Izzy rider',
                        '1e snowboard' => '1e snowboard',
                        '2e snowboard' => '2e snowboard',
                        '3e snowboard' => '3e snowboard',
                    ],
                    'required' => false
                ]);
        }

        // if ($options['option'] !== 'new') {
        //     $builder->add('category', EntityType::class, [
        //         'label' => 'Classe',
        //         'class' => Category::class,
        //         'required' => false
        //     ]);
        // }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Child::class,
            'option' => 'default'
        ]);
    }
}
