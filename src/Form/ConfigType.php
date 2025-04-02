<?php

namespace App\Form;

use App\Repository\ConfigRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
        dump($this->getConfigValue($options['data'], 'homepageTitle'));
        $builder
            ->add('homepageTitle', TextType::class, [
                'label' => 'Titre de la page d\'accueil',
                'attr' => [
                    'placeholder' => 'Kid\'s Bulle par défaut',
                ],
                'required' => false,
                // 'data' => $this->configRepository->findOneBy(['name' => 'homepageTitle'])->getValue(),
                'data' => $this->getConfigValue($options['data'], 'homepageTitle'),
            ])
            ->add('homepageDescription', TextareaType::class, [
                'label' => 'Description de la page d\'accueil',
                'attr' => [
                    'rows' => 4,
                ],
                'required' => false,
                'data' => $this->getConfigValue($options['data'], 'homepageDescription'),
            ])
            ->add('color', ColorType::class, [
                'label' => 'Couleur',
                'data' => $this->getConfigValue($options['data'], 'color'),
            ])
            ->add('code', TextType::class, [
                'label' => 'Code de vérification',
                'data' => $this->getConfigValue($options['data'], 'code'),
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
     * Get the value of a configuration by its name.
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
