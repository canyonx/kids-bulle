<?php

namespace App\Form;

use App\Repository\ConfigRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ConfigType extends AbstractType
{
    public function __construct(
        private ConfigRepository $configRepository,
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('brandName', TextType::class, [
                'label' => 'Nom du site',
                'attr' => [
                    'placeholder' => 'MyBrand',
                ],
                'required' => false,
                'data' => $this->getConfigValue($options['data'], 'brandName'),
            ])
            ->add('homepageTitle', TextType::class, [
                'label' => 'Titre de la page d\'accueil',
                'attr' => [
                    'placeholder' => 'Titre de la page d\'accueil',
                ],
                'required' => false,
                'data' => $this->getConfigValue($options['data'], 'homepageTitle'),
            ])
            ->add('homepageDescription', TextareaType::class, [
                'label' => 'Description de la page d\'accueil',
                'attr' => [
                    'placeholder' => 'Description de la page d\'accueil',
                    'rows' => 4,
                ],
                'required' => false,
                'data' => $this->getConfigValue($options['data'], 'homepageDescription'),
            ])
            ->add('color', ColorType::class, [
                'label' => 'Arrière plan',
                'label_attr' => [
                    'class' => 'd-flex flex-column align-items-center',
                ],
                'data' => $this->getConfigValue($options['data'], 'color'),
            ])
            ->add('colorTheme', ColorType::class, [
                'label' => 'Thème',
                'label_attr' => [
                    'class' => 'd-flex flex-column align-items-center',
                ],
                'data' => $this->getConfigValue($options['data'], 'colorTheme'),
            ])
            ->add('homepagePlanning', ChoiceType::class, [
                'label' => 'Afficher le planning sur la page d\'accueil',
                'required' => true,
                'choices' => [
                    'Oui' => 'true',
                    'Non' => 'false',
                ],
                'data' => $this->getConfigValue($options['data'], 'homepagePlanning'),
                'expanded' => true,
                'multiple' => false,
                'label_attr' => [
                    'class' => 'radio-inline',
                ],
            ])
            ->add('code', TextType::class, [
                'label' => 'Code de vérification',
                'data' => $this->getConfigValue($options['data'], 'code'),
            ])
            ->add('logoFilename', FileType::class, [
                'label' => 'Logo du site',
                // 'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'image/webp',
                            'image/x-icon',
                            'image/svg+xml',
                        ],
                        'mimeTypesMessage' => 'Format d\'image non valide.',
                    ])
                ],
            ])
            ->add('columnNumber', ChoiceType::class, [
                'label' => 'Afficher le planning sur la page d\'accueil',
                'required' => true,
                'choices' => [
                    '2' => '6',
                    '3' => '4',
                    '4' => '3',
                ],
                'data' => $this->getConfigValue($options['data'], 'columnNumber'),
                'expanded' => true,
                'multiple' => false,
                'label_attr' => [
                    'class' => 'radio-inline',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }

    /**
     * Get the value of a configuration by its name in an array of objects.
     *
     * @param array $configs The array of Config objects.
     * @param string $name The name of the configuration to retrieve.
     * @param mixed $default The default value to return if the configuration is not found.
     * @return mixed The value of the configuration or the default value.
     */
    private function getConfigValue(array $configs, string $name, mixed $default = null): mixed
    {
        foreach ($configs as $config) {
            if ($config->getName() === $name) {
                return $config->getValue();
            }
        }
        return $default;
    }
}
