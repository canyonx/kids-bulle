<?php

namespace App\Service;

use App\Message\ProcessLogoImages;
use Symfony\Component\Console\Application;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Input\ArrayInput;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Symfony\Component\Console\Output\NullOutput;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class LogoUploaderService
{

    public function __construct(
        private MessageBusInterface $bus,
        private string $uploadDir, // From service.yaml
        private FilterManager $filterManager,
        private DataManager $dataManager
    ) {}

    public function upload(UploadedFile $file): string
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->uploadDir);
        $filesystem->mkdir($this->uploadDir);


        $originalFilename = 'logo_original.' . $file->guessExtension();
        $file->move($this->uploadDir, $originalFilename);

        // Dispatch du message pour générer les déclinaisons
        // $this->bus->dispatch(new ProcessLogoImages($this->uploadDir . $originalFilename));

        // Génération des déclinaisons
        $filtersMap = [
            'android_192'      => 'android-chrome-192x192.png',
            'android_512'      => 'android-chrome-512x512.png',
            'apple_touch_icon' => 'apple-touch-icon.png',
            'favicon_16'       => 'favicon-16x16.png',
            'favicon_32'       => 'favicon-32x32.png',
        ];

        foreach ($filtersMap as $filter => $outputFilename) {
            try {
                // Charger le fichier en tant que BinaryInterface
                $binary = $this->dataManager->find($filter, '/uploads/' . $originalFilename);

                // Appliquer le filtre
                $filteredImage = $this->filterManager->applyFilter($binary, $filter);

                $filesystem->mkdir($this->uploadDir . "logos/");
                // Sauvegarder l'image filtrée
                $outputPath = $this->uploadDir . "logos/" . $outputFilename;
                file_put_contents($outputPath, $filteredImage->getContent());
            } catch (IOExceptionInterface $exception) {
                throw new \RuntimeException(sprintf('Failed to generate image for filter "%s": %s', $filter, $exception->getMessage()));
            }
        }

        // Créer le fichier favicon.ico
        $filesystem->copy(
            $this->uploadDir . "logos/" . 'favicon-32x32.png',
            $this->uploadDir . "logos/" . 'favicon.ico',
            true
        );

        return $originalFilename;
    }

    public function getPublicPath(string $filename): string
    {
        return '/uploads/logos/' . $filename;
    }
}
