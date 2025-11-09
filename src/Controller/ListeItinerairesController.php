<?php

namespace App\Controller;

use App\Entity\InfoUser;
use App\Repository\InfoUserRepository;

use App\Entity\Itineraires;
use App\Repository\ItinerairesRepository;

use App\Entity\Utilisateur;
use App\Form\ItinerairesType;
use App\Entity\Messagerie\Discussions;
use App\Repository\Messagerie\DiscussionsRepository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;

use Doctrine\Persistence\ManagerRegistry;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Form\SearchType;
use App\Repository\UtilisateurRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ListeItinerairesController extends AbstractController
{
    #[Route('/annonces', name: 'app_liste_annonces',methods: ['GET', 'POST'])]
    public function index(Request $request,EntityManagerInterface $entityManager, ItinerairesRepository $itinerairesRepository, InfoUserRepository $infoUserRepository): Response
    {
        $user = $this->getUser();
        
        // Récupérer les paramètres de recherche
        $depart = $request->query->get('depart', '');
        $niveau = $request->query->get('niveau', '');
        $duree_min = $request->query->get('dureemin', '');
        $duree_max = $request->query->get('dureemax', '');
        $distance = $request->query->get('distance', '');
        $keyword = $request->query->get('keyword', '');
        
        // Convertir en types appropriés
        $duree_min = !empty($duree_min) ? intval($duree_min) : null;
        $duree_max = !empty($duree_max) ? intval($duree_max) : null;
        $distance = !empty($distance) ? intval($distance) : null;
        
        // Si une recherche est effectuée, utiliser les filtres
        if ($request->query->has('search') && $request->query->get('search')) {
            $itineraires = $itinerairesRepository->findByMultipleCriteria(
                !empty($depart) ? $depart : null,
                !empty($niveau) ? $niveau : null,
                $distance,
                $duree_min,
                $duree_max,
                !empty($keyword) ? $keyword : null
            );
        } else {
            // Sinon, afficher tous les itinéraires publiés
            $itineraires = $itinerairesRepository->findBy(['publie' => true]);
        }
        
        // Construire le tableau de détails avec les créateurs
        $details = [];
        foreach($itineraires as $iti){
            $createur = $iti->getCreateur();
            $infoUser = null;
            
            if ($createur) {
                $infoUser = $infoUserRepository->findOneBy(['user' => $createur]);
            }
            
            $details[] = [
                "itineraire" => $iti,
                "createur" => $infoUser
            ];
        }
        
        if($user){
            $user_itineraires = $user->getItineraires()->getValues();
        } else {
            $user_itineraires = null;
        }
        
        $param = [
            'depart' => $depart ?? '',
            'niveau' => $niveau ?? '',
            'duree_min' => $duree_min ?? '',
            'duree_max' => $duree_max ?? '',
            'distance' => $distance ?? '',
            'keyword' => $keyword ?? ''
        ];

        return $this->render('liste_itineraires/index.html.twig', [
            'user' => $user,
            'param' => $param,
            'itineraires' => $details,
            'user_itineraire' => $user_itineraires,
            'controller_name' => 'ListeItinerairesController',
        ]);
    }

    #[Route('/saved/add{id}', name: 'iti_saved_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager,InfoUserRepository $infoUserRepository,Itineraires $itineraire): Response
    {
        $user = $this->getUser();
        $id = $user->getId();
        $infoUser = $infoUserRepository->find($id);

        if ($this->isCsrfTokenValid('add'.$itineraire->getId(), $request->request->get('_token'))) {
            $user->addItineraire($itineraire);
            $entityManager->persist($itineraire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_liste_annonces', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/saved/delete{id}', name: 'iti_saved_delete', methods: ['GET', 'POST'])]
    public function remove(Request $request, EntityManagerInterface $entityManager,InfoUserRepository $infoUserRepository,Itineraires $itineraire): Response
    {
        $user = $this->getUser();
        $id = $user->getId();
        $infoUser = $infoUserRepository->find($id);

        if ($this->isCsrfTokenValid('delete'.$itineraire->getId(), $request->request->get('_token'))) {
            $user->removeItineraire($itineraire);
            $entityManager->persist($itineraire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_liste_annonces', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/contact/{id}', name: 'app_contact_createur', methods: ['GET'])]
    public function contactCreateur(Itineraires $itineraire, EntityManagerInterface $entityManager, DiscussionsRepository $discussionsRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\Utilisateur $utilisateur */
        $utilisateur = $this->getUser();
        $createur = $itineraire->getCreateur();
        
        if (!$createur) {
            $this->addFlash('error', 'Le créateur de cet itinéraire n\'existe pas.');
            return $this->redirectToRoute('app_liste_annonces', [], Response::HTTP_SEE_OTHER);
        }
        
        // Vérifier si une discussion existe déjà entre ces deux utilisateurs avec ce sujet
        $discussionExistante = $discussionsRepository->createQueryBuilder('d')
            ->where('(d.user1 = :user1 AND d.user2 = :user2) OR (d.user1 = :user2 AND d.user2 = :user1)')
            ->andWhere('d.sujet = :sujet')
            ->setParameter('user1', $utilisateur)
            ->setParameter('user2', $createur)
            ->setParameter('sujet', $itineraire->getTitre())
            ->getQuery()
            ->getOneOrNullResult();
        
        if ($discussionExistante) {
            // Si la discussion existe déjà, rediriger vers celle-ci
            return $this->redirectToRoute('app_conversation_show', ['id' => $discussionExistante->getId()], Response::HTTP_SEE_OTHER);
        }
        
        // Créer une nouvelle discussion
        $discussion = new Discussions();
        $discussion->setUser1($utilisateur);
        $discussion->setUser2($createur);
        $discussion->setSujet($itineraire->getTitre());
        
        $entityManager->persist($discussion);
        $entityManager->flush();
        
        return $this->redirectToRoute('app_conversation_show', ['id' => $discussion->getId()], Response::HTTP_SEE_OTHER);
    }
}
