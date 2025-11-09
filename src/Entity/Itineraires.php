<?php

namespace App\Entity;

use App\Entity\Utilisateur;

use App\Repository\ItinerairesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: ItinerairesRepository::class)]
class Itineraires
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: Utilisateur::class, inversedBy: 'itineraires')]
    private Collection $utilisateur;

    #[ORM\Column(length: 150)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $niveau = [];

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $allures = [];

    #[ORM\Column(length: 255)]
    private ?string $depart = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $codePostal = null;

    #[ORM\Column]
    private ?int $distance = null;

    #[ORM\Column]
    private ?int $duree = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $accepte = [];

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $publie = null;

    #[ORM\Column]
    private ?bool $validation = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $createur = null;


    public function __construct()
    {
        $this->utilisateur = new ArrayCollection();
        $this->setValidation(false);
    }

    public function __toString()
    {
        return $this->getNiveau();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Utilisateur>
     */
    public function getUtilisateur(): Collection
    {
        return $this->utilisateur;
    }

    public function addUtilisateur(Utilisateur $utilisateur): self
    {
        if (!$this->utilisateur->contains($utilisateur)) {
            $this->utilisateur->add($utilisateur);
        }
        $this->utilisateur->add($utilisateur);
        $utilisateur->addItineraire($this);

        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): self
    {
        $this->utilisateur->removeElement($utilisateur);

        return $this;
    }

    public function getPublie(): ?bool
    {
        return $this->publie;
    }

    public function setPublie(bool $publie): self
    {
        $this->publie = $publie;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getNiveau(): array
    {
        return $this->niveau;
    }

    public function setNiveau(array $niveau): static
    {

        $this->niveau = $niveau;

        return $this;
        
    }

    public function getAllures():? array
    {
        return $this->allures;
    }

    public function setAllures(?array $allures): static
    {
        $this->allures = $allures;

        return $this;
    }

    public function getDepart(): ?string
    {
        return $this->depart;
    }

    public function setDepart(string $depart): static
    {
        $this->depart = $depart;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(?string $codePostal): static
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getDistance(): ?int
    {
        return $this->distance;
    }

    public function setDistance(int $distance): static
    {
        $this->distance = $distance;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getAccepte(): ?array
    {
        return $this->accepte;
    }

    public function setAccepte(?array $accepte): static
    {
        $this->accepte = $accepte;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isValidation(): ?bool
    {
        return $this->validation;
    }

    public function setValidation(bool $validation): static
    {
        $this->validation = $validation;

        return $this;
    }

    public function getCreateur(): ?Utilisateur
    {
        return $this->createur;
    }

    public function setCreateur(Utilisateur $createur): static
    {
        $this->createur = $createur;

        return $this;
    }

}
