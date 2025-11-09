<?php

namespace App\Controller\Backoffice\Messagerie;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;

use App\Entity\InfoUser;
use App\Repository\InfoUserRepository;

use App\Entity\Messagerie\Messages;
use App\Repository\Messagerie\MessagesRepository;

use App\Entity\Messagerie\Discussions;
use App\Repository\Messagerie\DiscussionsRepository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backoffice/messagerie')]
class MessagerieController extends AbstractController
{
    #[Route('/', name: 'app_messagerie')]
    public function index(EntityManagerInterface $entityManager, DiscussionsRepository $discussionsRepository, MessagesRepository $messagesRepository): Response
    {
        // check if the user is authenticated first
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        //return the current logged in user
        /** @var \App\Entity\Utilisateur $utilisateur */
        $utilisateur = $this->getUser();   
        $discussions = $utilisateur->getDiscussions()->getValues();
        // $messages = $discussions[0];
        // dd($utilisateur->getMessages()->getValues());
        return $this->render('backoffice/messagerie/index.html.twig',[
            'user' => $utilisateur,
            'discussions' => $discussions,
            'title_controller' => 'Mes messages',
            'btn_breakcrumb' => 'app_backoffice'
        ]);

    }

    #[Route('/conversation/{id}', name: 'app_conversation_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager, DiscussionsRepository $discussionsRepository,Messages $messages, int $id): Response
    {
        /** @var \App\Entity\Utilisateur $utilisateur */
        $utilisateur = $this->getUser();
        $discussion = $utilisateur->getDiscussions()->getValues();
        $messages = $discussion[0]->getMessages();
        $id = $discussion[0]->getId();
        var_dump(get_debug_type($messages));
        return $this->render('backoffice/messagerie/conversation/conversation.html.twig', [
            'user' => $utilisateur,
            'discussion' => $discussion,
            'messages' => $messages,
            'title_controller' => 'Conversation',
            'btn_breakcrumb' => 'app_itineraires_new'
        ]);
    }

    
}
