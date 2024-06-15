<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "Le titre est requis")]
    private $title;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(message: "La description est requise")]
    private $description;

    #[ORM\Column(type: "datetime")]
    #[Assert\NotBlank(message: "La date est requise")]
    #[Assert\GreaterThan("now", message: "La date doit être dans le futur")]
    private $date;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "Le nombre de participants est requis")]
    private $maxParticipants;

    #[ORM\Column(type: "boolean")]
    private $isPublic;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'events')]
    private $participants;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $owner;


    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getMaxParticipants(): ?int
    {
        return $this->maxParticipants;
    }

    public function setMaxParticipants(int $maxParticipants): self
    {
        $this->maxParticipants = $maxParticipants;

        return $this;
    }

    public function getIsPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): self
    {
        $this->isPublic = $isPublic;

        return $this;
    }

        /**
     * @return Collection|User[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $user): bool
    {
        if ($this->participants->contains($user)) {
            return false;
        }

        $this->participants[] = $user;
        return true;
    }

    public function removeParticipant(User $user): bool
    {
        if (!$this->participants->contains($user)) {
            return false;
        }

        $this->participants->removeElement($user);
        return true;
    }

    public function isUserRegistered(User $user): bool
    {
        return $this->participants->contains($user);
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

}