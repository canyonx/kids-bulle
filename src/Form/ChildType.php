<?php

namespace App\Form;

use App\Entity\Child;
use App\Entity\Category;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ChildType extends AbstractType
{
    private $security;

    public function __construct(
        Security $security
    ) {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User */
        $user = $this->security->getUser();

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
            ]);

        if ($options['option'] !== 'new') {
            $builder->add('category', EntityType::class, [
                'label' => 'Classe',
                'class' => Category::class,
                'required' => false
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Child::class,
            'option' => 'default'
        ]);
    }
}
