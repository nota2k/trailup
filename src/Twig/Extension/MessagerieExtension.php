<?php

namespace App\Twig\Extension;

use App\Repository\Messagerie\MessagesRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MessagerieExtension extends AbstractExtension
{
    public function __construct(
        private MessagesRepository $messagesRepository,
        private Security $security
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('count_unread_messages', [$this, 'countUnreadMessages']),
        ];
    }

    public function countUnreadMessages(): int
    {
        $user = $this->security->getUser();
        
        if (!$user) {
            return 0;
        }
        
        try {
            return $this->messagesRepository->countUnreadMessages($user);
        } catch (\Exception $e) {
            // En cas d'erreur, retourner 0
            return 0;
        }
    }
}

