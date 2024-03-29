<?php

namespace App\Entity;

use App\Repository\ChildRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChildRepository::class)]
class Child
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $firstname;

    #[ORM\Column(type: 'string', length: 255)]
    private $lastname;

    #[ORM\Column(type: 'datetime_immutable')]
    private $birthAt;

    #[ORM\ManyToMany(targetEntity: Activity::class, mappedBy: 'childrens')]
    private $activities;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'childs')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $levelSki;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $levelSlalom;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $levelSnowboard;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $license = null;

    public function __construct()
    {
        $this->activities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getBirthAt(): ?\DateTimeImmutable
    {
        return $this->birthAt;
    }

    public function setBirthAt(\DateTimeImmutable $birthAt): self
    {
        $this->birthAt = $birthAt;

        return $this;
    }

    /**
     * @return Collection<int, Activity>
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): self
    {
        if (!$this->activities->contains($activity)) {
            $this->activities[] = $activity;
            $activity->addChildren($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->removeElement($activity)) {
            $activity->removeChildren($this);
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFullname(): string
    {
        return ucfirst($this->getFirstname()) . ' ' . ucfirst($this->getLastname());
    }

    public function __toString()
    {
        return ucwords(strtolower($this->getFirstname() . ' ' . $this->getLastname()));
    }

    public function getLevelSki(): ?string
    {
        return $this->levelSki;
    }

    public function setLevelSki(?string $levelSki): self
    {
        $this->levelSki = $levelSki;

        return $this;
    }

    public function getLevelSlalom(): ?string
    {
        return $this->levelSlalom;
    }

    public function setLevelSlalom(?string $levelSlalom): self
    {
        $this->levelSlalom = $levelSlalom;

        return $this;
    }

    public function getLevelSnowboard(): ?string
    {
        return $this->levelSnowboard;
    }

    public function setLevelSnowboard(?string $levelSnowboard): self
    {
        $this->levelSnowboard = $levelSnowboard;

        return $this;
    }

    public function getAge()
    {
        $now = new \DateTime('now');
        $age = $this->getBirthAt();
        $difference = $now->diff($age);

        return $difference->format('%y');
    }

    public function getLicense(): ?string
    {
        return $this->license;
    }

    public function setLicense(?string $license): static
    {
        $this->license = $license;

        return $this;
    }
}
