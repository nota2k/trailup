<?php

namespace App\Entity;

use App\Entity\Utilisateur;

use App\Repository\InfoUserRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InfoUserRepository::class)]
class InfoUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $region = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $miniature = null;

    public function __construct()           // Pour avoir role_entreprise par defaut // Commenter car fixtures //
    {

        $this->setMiniature('/public/assets/img/thmb-user.png');
    }

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?Utilisateur
    {
        return $this->user;
    }

    public function getUserId(): ?Utilisateur
    {
        return $this->id;
    }

    public function setUserId(Utilisateur $id): self
    {
        $this->user = $id;
        
        return $this;
    }

    // public function __toString() {
    //     return $this->getUser();
    // }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getMiniature(): ?string
    {
        return $this->miniature;
    }

    public function setMiniature(?string $miniature)
    {
        $this->miniature = $miniature;

        return $this;
    }

    public function getMiniaturePath()
    {
        return $this->getMiniature();
    }

    public function setMiniaturePath(?string $miniature)
    {
        $this->miniature = $miniature;
        return $this->getMiniature();
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  Issue|null $issue
     */
    public function transform($issue): string
    {
        if (null === $issue) {
            return '';
        }

        return $issue->getId();
    }
}
