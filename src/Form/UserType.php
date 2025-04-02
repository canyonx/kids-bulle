<?php

namespace App\Form;

use App\Entity\User;
use App\Repository\SettingRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['option'] === 'default') {
            $builder
                ->add('email', EmailType::class, [
                    'label' => 'Email',
                    'attr' => [
                        'placeholder' => 'exemple@whynot.ex'
                    ]
                ])
                ->add('firstname', TextType::class, [
                    'label' => 'Prénom',
                    'attr' => [
                        'placeholder' => 'John'
                    ]
                ])
                ->add('lastname', TextType::class, [
                    'label' => 'Nom',
                    'attr' => [
                        'placeholder' => 'Doe'
                    ]
                ])
                ->add('phone', TextType::class, [
                    'label' => 'Téléphone',
                    'attr' => [
                        'placeholder' => '+33 6 12 34 56 78'
                    ]
                ])
                ->add('fulladress', TextType::class, [
                    'label' => 'Adresse complète',
                    'attr' => [
                        'placeholder' => '123 rue du Soleil, 04567 Astre'
                    ]
                ]);
        }

        if ($options['option'] === 'edit_role') {
            $builder
                ->add('role', ChoiceType::class, [
                    'label' => 'Rôle',
                    'choices' => [
                        'User' => 'ROLE_USER',
                        'Teacher' => 'ROLE_TEACHER',
                        'Admin' => 'ROLE_ADMIN'
                    ],
                    'mapped' => false
                ])
                ->add('code', TextType::class, [
                    'label' => 'Code de l\'utilisateur',
                    'mapped' => false,
                    'data' => $options['data']->getCode(),
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'option' => 'default'
        ]);
    }
}
