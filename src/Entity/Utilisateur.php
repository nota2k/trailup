<?php

namespace App\Entity;

use App\Entity\Messagerie\Discussions;
use App\Entity\Messagerie\Messages;
use App\Entity\InfoUser;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]

class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    // #[ORM\JoinColumn(nullable: false)]
    private ?InfoUser $user = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\OneToMany(targetEntity: Chevaux::class, mappedBy: 'id')]
    private Collection $chevaux;

    #[ORM\ManyToMany(targetEntity: Itineraires::class, inversedBy: 'itineraires')]
    private Collection $itineraires;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'user1', targetEntity: Discussions::class)]
    private Collection $discussions;

    #[ORM\OneToMany(mappedBy: 'expediteur', targetEntity: Messages::class)]
    private Collection $messages;

    public function __construct()           // Pour avoir role_entreprise par defaut // Commenter car fixtures //
    {
        $this->chevaux = new ArrayCollection();
        $this->itineraires = new ArrayCollection();
        $this->setRoles(["ROLE_USER"]);
        $this->discussions = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getUserInfo(): ?InfoUser
    {
        return $this->user;
    }

     /**
     * @return Collection<int, chevaux>
     */
    public function getChevaux(): Collection
    {
        return $this->chevaux;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        // $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Question[]
     */
    public function getItineraires(): Collection
    {
        return $this->itineraires;
    }
    public function addItineraire(Itineraires $itineraire): self
    {
        if (!$this->itineraires->contains($itineraire)) {
            $this->itineraires[] = $itineraire;
            $itineraire->addUtilisateur($this);
        }
        return $this;
    }
    public function removeItineraire(Itineraires $itineraire): self
    {
        if ($this->itineraires->removeElement($itineraire)) {
            $itineraire->removeUtilisateur($this);
        }
        return $this;
    }

    /**
     * @return Collection<int, Discussions>
     */
    public function getDiscussions(): Collection
    {
        return $this->discussions;
    }

    public function addDiscussion(Discussions $discussion): static
    {
        if (!$this->discussions->contains($discussion)) {
            $this->discussions->add($discussion);
            $discussion->setUser1($this);
        }

        return $this;
    }

    public function removeDiscussion(Discussions $discussion): static
    {
        if ($this->discussions->removeElement($discussion)) {
            // set the owning side to null (unless already changed)
            if ($discussion->getUser1() === $this) {
                $discussion->setUser1(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Messages>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Messages $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setExpediteur($this);
        }

        return $this;
    }

    public function removeMessage(Messages $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getExpediteur() === $this) {
                $message->setExpediteur(null);
            }
        }

        return $this;
    }

}
