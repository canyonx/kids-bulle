<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
class Activity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private $dateAt;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'activities')]
    #[ORM\JoinColumn(nullable: false)]
    private $category;

    #[ORM\ManyToMany(targetEntity: Child::class, inversedBy: 'activities')]
    private $childrens;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'activities')]
    private $teacher;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    public function __construct()
    {
        $this->childrens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateAt(): ?\DateTimeImmutable
    {
        return $this->dateAt;
    }

    public function setDateAt(\DateTimeImmutable $dateAt): self
    {
        $this->dateAt = $dateAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    // /**
    //  * @return Collection<int, Child>
    //  */
    // public function getChildrens(): Collection
    // {
    //     return $this->childrens;
    // }

    /**
     * Returns a sorted collection of children, prioritizing the ones associated with the given user,
     * and sorting all by birth date ascending.
     *
     * @return Collection<int, Child>
     */
    public function getChildrens(?User $user = null): Collection
    {
        $children = $this->childrens->toArray();

        usort($children, function (Child $a, Child $b) use ($user) {
            $aIsUserChild = $user && $a->getUser() === $user;
            $bIsUserChild = $user && $b->getUser() === $user;

            // Prioriser les enfants du user
            if ($aIsUserChild && !$bIsUserChild) {
                return -1;
            } elseif (!$aIsUserChild && $bIsUserChild) {
                return 1;
            }

            // Sinon, trier par birthAt décroissant
            return $b->getBirthAt() <=> $a->getBirthAt();
        });

        return new ArrayCollection($children);
    }

    public function addChildren(Child $children): self
    {
        if (!$this->childrens->contains($children)) {
            $this->childrens[] = $children;
        }

        return $this;
    }

    public function removeChildren(Child $children): self
    {
        $this->childrens->removeElement($children);

        return $this;
    }

    public function getTeacher(): ?User
    {
        return $this->teacher;
    }

    public function setTeacher(?User $teacher): self
    {
        $this->teacher = $teacher;

        return $this;
    }

    public function __toString()
    {
        return ucwords($this->getDateAt()->format('d.m') . ' ' . $this->getCategory()->getName());
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
