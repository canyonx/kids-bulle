<?php

namespace App\Message;

final class ProcessLogoImages
{
    public function __construct(
        private string $filePath
    ) {}

    public function getFilePath(): string
    {
        return $this->filePath;
    }
}
