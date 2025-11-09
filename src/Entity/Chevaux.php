<?php

namespace App\Entity;

use App\Repository\ChevauxRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChevauxRepository::class)]
class Chevaux
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'chevaux')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $proprietaire = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $race = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $age = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo01 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo02 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo03 = null;

    #[ORM\Column(length: 255)]
    private ?string $sexe = null;

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function getRace(): ?string
    {
        return $this->race;
    }

    public function setRace(string $race): static
    {
        $this->race = $race;

        return $this;
    }

    public function getAge(): ?\DateTimeInterface
    {
        return $this->age;
    }

    public function setAge(?\DateTimeInterface $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getPhoto01(): ?string
    {
        return $this->photo01;
    }

    public function setPhoto01(?string $photo01): static
    {
        $this->photo01 = $photo01;

        return $this;
    }

    public function getPhoto02(): ?string
    {
        return $this->photo02;
    }

    public function setPhoto02(?string $photo02): static
    {
        $this->photo02 = $photo02;

        return $this;
    }

    public function getPhoto03(): ?string
    {
        return $this->photo03;
    }

    public function setPhoto03(?string $photo03): static
    {
        $this->photo03 = $photo03;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): static
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getProprietaire(): ?Utilisateur
    {
        return $this->proprietaire;
    }

    public function setProprietaire(?Utilisateur $id): static
    {
        $this->proprietaire = $id;

        return $this;
    }
}
