<?php

namespace App\Entity;

use App\Repository\SettingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettingRepository::class)]
class Setting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $homepageTitle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $homepageDescription = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $signCode = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $expireAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHomepageTitle(): ?string
    {
        return $this->homepageTitle;
    }

    public function setHomepageTitle(?string $homepageTitle): static
    {
        $this->homepageTitle = $homepageTitle;

        return $this;
    }

    public function getHomepageDescription(): ?string
    {
        return $this->homepageDescription;
    }

    public function setHomepageDescription(?string $homepageDescription): static
    {
        $this->homepageDescription = $homepageDescription;

        return $this;
    }

    public function getSignCode(): ?string
    {
        return $this->signCode;
    }

    public function setSignCode(?string $signCode): static
    {
        $this->signCode = $signCode;

        return $this;
    }

    public function getExpireAt(): ?\DateTimeImmutable
    {
        return $this->expireAt;
    }

    public function setExpireAt(?\DateTimeImmutable $expireAt): static
    {
        $this->expireAt = $expireAt;

        return $this;
    }
}
