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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\Messagerie\DiscussionType;
use App\Form\Messagerie\MessageType;
use DateTime;

#[Route('/backoffice/messagerie')]
class MessagerieController extends AbstractController
{
    #[Route('/', name: 'app_messagerie')]
    public function index(EntityManagerInterface $entityManager, DiscussionsRepository $discussionsRepository, MessagesRepository $messagesRepository, InfoUserRepository $infoUserRepository): Response
    {
        // check if the user is authenticated first
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        //return the current logged in user
        /** @var \App\Entity\Utilisateur $utilisateur */
        $utilisateur = $this->getUser();
        
        // Chercher InfoUser par l'utilisateur, pas par son propre ID
        $infoUser = $infoUserRepository->findOneBy(['user' => $utilisateur]);
        
        // Si InfoUser n'existe pas, créer un objet avec des valeurs par défaut
        if (!$infoUser) {
            $infoUser = new InfoUser();
            $infoUser->setUserId($utilisateur);
            if (!$infoUser->getMiniature()) {
                $infoUser->setMiniature('/assets/img/thmb-user.png');
            }
        }
        
        // Récupérer toutes les discussions où l'utilisateur est user1 ou user2
        $discussions = $discussionsRepository->findByUser($utilisateur);
        
        return $this->render('backoffice/messagerie/index.html.twig',[
            'user' => $infoUser,
            'discussions' => $discussions,
            'title_controller' => 'Mes messages',
            'btn_breakcrumb' => 'app_messagerie_new'
        ]);
    }

    #[Route('/new', name: 'app_messagerie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, InfoUserRepository $infoUserRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\Utilisateur $utilisateur */
        $utilisateur = $this->getUser();
        
        $infoUser = $infoUserRepository->findOneBy(['user' => $utilisateur]);
        if (!$infoUser) {
            $infoUser = new InfoUser();
            $infoUser->setUserId($utilisateur);
            if (!$infoUser->getMiniature()) {
                $infoUser->setMiniature('/assets/img/thmb-user.png');
            }
        }
        
        $discussion = new Discussions();
        $discussion->setUser1($utilisateur);
        
        $form = $this->createForm(DiscussionType::class, $discussion, [
            'current_user' => $utilisateur
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($discussion);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_conversation_show', ['id' => $discussion->getId()], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('backoffice/messagerie/new.html.twig', [
            'user' => $infoUser,
            'discussion' => $discussion,
            'form' => $form,
            'title_controller' => 'Nouvelle conversation',
            'btn_breakcrumb' => 'app_messagerie'
        ]);
    }

    #[Route('/conversation/{id}', name: 'app_conversation_show', methods: ['GET', 'POST'])]
    public function show(Request $request, EntityManagerInterface $entityManager, DiscussionsRepository $discussionsRepository, int $id, InfoUserRepository $infoUserRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\Utilisateur $utilisateur */
        $utilisateur = $this->getUser();
        
        $infoUser = $infoUserRepository->findOneBy(['user' => $utilisateur]);
        if (!$infoUser) {
            $infoUser = new InfoUser();
            $infoUser->setUserId($utilisateur);
            if (!$infoUser->getMiniature()) {
                $infoUser->setMiniature('/assets/img/thmb-user.png');
            }
        }
        
        $discussion = $discussionsRepository->find($id);
        
        if (!$discussion) {
            throw $this->createNotFoundException('Discussion non trouvée');
        }
        
        // Vérifier que l'utilisateur fait partie de la discussion
        if ($discussion->getUser1() !== $utilisateur && $discussion->getUser2() !== $utilisateur) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette conversation');
        }
        
        // Déterminer le destinataire (l'autre utilisateur)
        $destinataire = ($discussion->getUser1() === $utilisateur) ? $discussion->getUser2() : $discussion->getUser1();
        $destinataireInfo = $infoUserRepository->findOneBy(['user' => $destinataire]);
        
        // Récupérer les messages triés par date et heure
        $messages = $discussion->getMessages()->toArray();
        usort($messages, function($a, $b) {
            $dateA = $a->getDate();
            $dateB = $b->getDate();
            if ($dateA == $dateB) {
                $heureA = $a->getHeure();
                $heureB = $b->getHeure();
                return $heureA <=> $heureB;
            }
            return $dateA <=> $dateB;
        });
        
        // Marquer tous les messages non lus comme lus pour cet utilisateur
        // (uniquement les messages qui ne sont pas envoyés par l'utilisateur)
        $hasUnreadMessages = false;
        foreach ($messages as $message) {
            // Si le message n'a pas été envoyé par l'utilisateur actuel et qu'il n'est pas encore lu
            if ($message->getExpediteur() !== $utilisateur && !$message->isLu()) {
                $message->setLu(true);
                $hasUnreadMessages = true;
            }
        }
        
        // Sauvegarder les changements si des messages ont été marqués comme lus
        if ($hasUnreadMessages) {
            $entityManager->flush();
        }
        
        // Créer un nouveau message
        $message = new Messages();
        $message->setExpediteur($utilisateur);
        $message->setDiscussion($discussion);
        // S'assurer que les dates sont définies
        $message->setDate(new DateTime('now'));
        $message->setHeure(new DateTime('now'));
        
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // S'assurer que le message est ajouté à la discussion
            $discussion->addMessage($message);
            $entityManager->persist($message);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_conversation_show', ['id' => $id], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('backoffice/messagerie/conversation/conversation.html.twig', [
            'user' => $infoUser,
            'destinataire' => $destinataireInfo,
            'discussion' => $discussion,
            'messages' => $messages,
            'form' => $form,
            'title_controller' => $discussion->getSujet(),
            'btn_breakcrumb' => 'app_messagerie'
        ]);
    }

    #[Route('/conversation/{id}/delete', name: 'app_conversation_delete', methods: ['POST'])]
    public function delete(Request $request, Discussions $discussion, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\Utilisateur $utilisateur */
        $utilisateur = $this->getUser();
        
        // Vérifier que l'utilisateur fait partie de la discussion
        if ($discussion->getUser1() !== $utilisateur && $discussion->getUser2() !== $utilisateur) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette conversation');
        }
        
        if ($this->isCsrfTokenValid('delete'.$discussion->getId(), $request->request->get('_token'))) {
            $entityManager->remove($discussion);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('app_messagerie', [], Response::HTTP_SEE_OTHER);
    }
}
