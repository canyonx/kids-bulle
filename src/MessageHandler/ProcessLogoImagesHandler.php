<?php

namespace App\MessageHandler;

use App\Message\ProcessLogoImages;
use Symfony\Component\Filesystem\Filesystem;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ProcessLogoImagesHandler
{
    private string $logosDir;
    private string $publicDir;

    public function __construct(
        private CacheManager $cacheManager,
        private string $uploadDir,
        private string $projectDir
    ) {
        $this->publicDir = $this->projectDir . '/public';
        $this->logosDir = $this->uploadDir . 'logos/';
    }

    public function __invoke(ProcessLogoImages $message): void
    {
        $filesystem = new Filesystem();

        // Nettoyage du dossier logos
        try {
            $filesystem->remove($this->projectDir . '/media/cache');
        } catch (\Exception $e) {
            throw new \RuntimeException("Erreur lors de la suppression du dossier logos : " . $e->getMessage());
        }

        $filesystem->mkdir($this->logosDir);

        $filePath = $message->getFilePath(); // Chemin absolu
        $relativePath = str_replace($this->publicDir, '', $filePath); // ex: /uploads/logo_original.png
        // dd($filePath);
        $filtersMap = [
            'android_192'      => 'android-chrome-192x192.png',
            'android_512'      => 'android-chrome-512x512.png',
            'apple_touch_icon' => 'apple-touch-icon.png',
            'favicon_16'       => 'favicon-16x16.png',
            'favicon_32'       => 'favicon-32x32.png',
        ];

        foreach ($filtersMap as $filter => $targetFilename) {
            // Génère le cache
            // $this->cacheManager->remove($relativePath, $filter);
            $this->cacheManager->resolve('uploads/logo_original.png', $filter);

            // Construire le chemin du fichier sur le disque
            $absolutePath = $this->publicDir . '/media/cache/' . $filter . $relativePath;

            if (!file_exists($absolutePath)) {
                throw new \RuntimeException("Image générée introuvable pour '$filter' : $absolutePath");
            }

            $filesystem->copy($absolutePath, $this->logosDir . $targetFilename, true);
        }

        // Optionnel : copie favicon.ico
        $filesystem->copy($this->logosDir . 'favicon-32x32.png', $this->logosDir . 'favicon.ico', true);
    }
}
