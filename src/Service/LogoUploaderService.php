<?php

namespace App\Service;

use App\Message\ProcessLogoImages;
use Symfony\Component\Filesystem\Filesystem;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
        $filesystem->mkdir($this->uploadDir);

        $originalFilename = 'logo_original.' . $file->guessExtension();
        $file->move($this->uploadDir, $originalFilename);

        // Dispatch du message pour générer les déclinaisons
        $this->bus->dispatch(new ProcessLogoImages($this->uploadDir . $originalFilename));

        return $originalFilename;
    }
}
