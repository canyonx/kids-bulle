<?php

namespace App\Form;

use App\Entity\Child;
use App\Entity\Activity;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivityAddChildrenType extends AbstractType
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
            ->add('addChildrens', EntityType::class, [
                'label' => 'Mes enfants',
                'class' => Child::class,
                'multiple' => true,
                'choices' => $user->getChilds(),
                'mapped' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
        ]);
    }
}
