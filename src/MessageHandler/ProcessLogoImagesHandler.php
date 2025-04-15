<?php

namespace App\MessageHandler;

use App\Message\ProcessLogoImages;
use Symfony\Component\Filesystem\Filesystem;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ProcessLogoImagesHandler
{
    public function __construct(
        private DataManager $dataManager,
        private FilterManager $filterManager,
        private string $publicDir, // set in services.yaml
        private string $uploadDir,
    ) {}

    public function __invoke(ProcessLogoImages $message): void
    {
        $filesystem = new Filesystem();

        // Tableau de correspondance entre les filtres et les noms de fichiers
        $filtersMap = [
            'android_192'      => 'android-chrome-192x192.png',
            'android_512'      => 'android-chrome-512x512.png',
            'apple_touch_icon' => 'apple-touch-icon.png',
            'favicon_16'       => 'favicon-16x16.png',
            'favicon_32'       => 'favicon-32x32.png',
            'navbar_40'        => 'navbar-40x40.png',
        ];

        // Chemin de l'image d'origine ex: /uploads/logo_original.png
        $relativePath = str_replace($this->publicDir, '', $message->getFilePath());

        // Vérifier que le dossier de destination existe
        $filesystem->mkdir($this->publicDir . "/images/icons/");

        // Appliquer les filtres et sauvegarder les images
        foreach ($filtersMap as $filter => $outputFilename) {
            try {
                // Charger le fichier en tant que BinaryInterface
                $binary = $this->dataManager->find($filter, $relativePath);

                // Appliquer le filtre
                $filteredImage = $this->filterManager->applyFilter($binary, $filter);

                // Sauvegarder l'image filtrée
                $outputPath = $this->publicDir . "/images/icons/" . $outputFilename;
                file_put_contents($outputPath, $filteredImage->getContent());
            } catch (\Exception $e) {
                throw new \Exception("Failed to generate image for filter $filter : " . $e->getMessage());
            }
        }

        // Créer le fichier favicon.ico
        $filesystem->copy(
            $this->publicDir . "/images/icons/" . 'favicon-32x32.png',
            $this->publicDir . '/favicon.ico',
            true
        );
    }
}
