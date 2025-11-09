<?php

namespace App\Entity\Messagerie;

use App\Entity\Utilisateur;

use App\Repository\Messagerie\DiscussionsRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiscussionsRepository::class)]
class Discussions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'discussions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $user1 = null;

    #[ORM\ManyToOne(inversedBy: 'discussions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $user2 = null;

    #[ORM\Column(length: 255)]
    private ?string $sujet = null;

    #[ORM\OneToMany(mappedBy: 'discussion', targetEntity: Messages::class)]
    private Collection $messages;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser1(): ?Utilisateur
    {
        return $this->user1;
    }

    public function setUser1(?Utilisateur $user1): static
    {   
        $this->user1 = $user1;

        return $this;
    }

    public function getUser2(): ?Utilisateur
    {
        return $this->user2;
    }

    public function setUser2(?Utilisateur $user2): static
    {
        $this->user2 = $user2;

        return $this;
    }

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): static
    {
        $this->sujet = $sujet;

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
            $message->setDiscussion($this);
        }

        return $this;
    }

    public function removeMessage(Messages $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getDiscussion() === $this) {
                $message->setDiscussion(null);
            }
        }

        return $this;
    }
    
    /**
     * Récupère le dernier message trié par date et heure
     */
    public function getLastMessage(): ?Messages
    {
        if ($this->messages->isEmpty()) {
            return null;
        }
        
        $messages = $this->messages->toArray();
        usort($messages, function($a, $b) {
            $dateA = $a->getDate();
            $dateB = $b->getDate();
            
            // Si les dates sont nulles, les mettre à la fin
            if (!$dateA && !$dateB) return 0;
            if (!$dateA) return 1;
            if (!$dateB) return -1;
            
            // Comparer les dates
            $dateCompare = $dateA->getTimestamp() <=> $dateB->getTimestamp();
            if ($dateCompare !== 0) {
                return $dateCompare;
            }
            
            // Si les dates sont égales, comparer les heures
            $heureA = $a->getHeure();
            $heureB = $b->getHeure();
            
            if (!$heureA && !$heureB) return 0;
            if (!$heureA) return 1;
            if (!$heureB) return -1;
            
            return $heureA->getTimestamp() <=> $heureB->getTimestamp();
        });
        
        return end($messages);
    }
    
    /**
     * Compte les messages non lus dans cette discussion pour un utilisateur donné
     * Un message est non lu s'il n'a pas été envoyé par l'utilisateur et que lu = false
     */
    public function countUnreadMessagesForUser(Utilisateur $user): int
    {
        $count = 0;
        foreach ($this->messages as $message) {
            if ($message->getExpediteur() !== $user && !$message->isLu()) {
                $count++;
            }
        }
        return $count;
    }
    
}
