<?php

namespace App\Entity\Messagerie;

use App\Entity\Utilisateur;
use App\Entity\Messagerie\Discussions;

use App\Repository\Messagerie\MessagesRepository;
use App\Repository\Messagerie\DiscussionsRepository;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessagesRepository::class)]
class Messages
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $heure;

    #[ORM\Column]
    private ?bool $lu = null;

    #[ORM\Column]
    private ?bool $prio = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    private ?Discussions $discussion = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    private ?Utilisateur $expediteur = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $body = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = new DateTime('now');

        return $this;
    }

    public function getHeure(): ?\DateTimeInterface
    {
        return $this->heure;
    }

    public function setHeure(\DateTimeInterface $heure): static
    {
        $this->heure = $heure;

        return $this;
    }

    public function isLu(): ?bool
    {
        return $this->lu;
    }

    public function setLu(bool $lu): static
    {
        $this->lu = $lu;

        return $this;
    }

    public function isPrio(): ?bool
    {
        return $this->prio;
    }

    public function setPrio(bool $prio): static
    {
        $this->prio = $prio;

        return $this;
    }

    public function getDiscussion(): ?Discussions
    {
        return $this->discussion;
    }

    public function setDiscussion(?Discussions $discussion): static
    {
        $this->discussion = $discussion;

        return $this;
    }

    public function getExpediteur(): ?Utilisateur
    {
        return $this->expediteur;
    }

    public function setExpediteur(?Utilisateur $expediteur): static
    {
        $this->expediteur = $expediteur;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): static
    {
        $this->body = $body;

        return $this;
    }
}
