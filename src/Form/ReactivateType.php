<?php

namespace App\Form;

use App\Repository\ConfigRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ReactivateType extends AbstractType
{
    public function __construct(
        private ConfigRepository $configRepository
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', PasswordType::class, [
                'label' => 'Code fourni par Kid\'s Bulle',
                'mapped' => false,
                'constraints' => [
                    new EqualTo([
                        // 'value' => $this->parameter->get('register_code'),
                        'value' => $this->configRepository->findOneBy(['name' => 'code'])->getValue(),
                        'message' => 'Code erroné',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
