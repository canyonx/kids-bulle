<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Child;
use App\Entity\Activity;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\String\Slugger\SluggerInterface;

class ChildType extends AbstractType
{
    private $security;
    private $categoryRepository;
    private $slugger;

    public function __construct(
        Security $security,
        CategoryRepository $categoryRepository,
        SluggerInterface $slugger
    ) {
        $this->security = $security;
        $this->categoryRepository = $categoryRepository;
        $this->slugger = $slugger;
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
                ]);
        }


        // if ($options['option'] !== 'new') {
        //     $builder->add('category', EntityType::class, [
        //         'label' => 'Classe',
        //         'class' => Category::class,
        //         'required' => false
        //     ]);
        // }
        if ($options['option'] === 'add_activities') {
            $categories = $this->categoryRepository->findAll();
            foreach ($categories as $cat) {

                $name = strtolower($this->slugger->slug($cat->getName()));

                $builder->add($name, EntityType::class, [
                    'label' => 'Choix des cours ' . $cat->getName(),
                    'class' => Activity::class,
                    'query_builder' => function (EntityRepository $er) use ($cat): QueryBuilder {
                        return $er->createQueryBuilder('a')
                            ->orderBy('a.dateAt', 'ASC')
                            ->andWhere('a.category = :cat')
                            ->setParameter('cat', $cat);
                        // ->orderBy('a.category', 'ASC');
                    },
                    'multiple' => true,
                    'expanded' => true,
                    'mapped' => false
                ]);
            }
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
