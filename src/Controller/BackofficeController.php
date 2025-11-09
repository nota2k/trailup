<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use App\Entity\Chevaux;
use App\Repository\ChevauxRepository;

use App\Entity\InfoUser;
use App\Repository\InfoUserRepository;

use App\Form\InfoUserType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/backoffice')]
class BackofficeController extends AbstractController
{
    #[Route('/', name: 'app_backoffice')]
    public function index(EntityManagerInterface $entityManager,InfoUserRepository $infoUserRepository): Response
    {
        // check if the user is authenticated first
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $utilisateur = $this->getUser();
        $id = $utilisateur->getId();
        // Chercher InfoUser par l'utilisateur, pas par son propre ID
        $infoUser = $infoUserRepository->findOneBy(['user' => $utilisateur]);
        
        // Si InfoUser n'existe pas, créer un objet avec des valeurs par défaut
        if (!$infoUser) {
            $infoUser = new InfoUser();
            $infoUser->setUserId($utilisateur);
            // Définir une miniature par défaut si elle n'existe pas
            if (!$infoUser->getMiniature()) {
                $infoUser->setMiniature('/assets/img/thmb-user.png');
            }
        }
        
        return $this->render('backoffice/backoffice.html.twig',[
            'user' => $infoUser,
            'title_controller' => 'Mon profil',
            'btn_breakcrumb' => 'app_backoffice'
        ]);
    }

    #[Route('/{id}', name: 'app_profil', methods: ['GET'])]
    public function showByPk( EntityManagerInterface $entityManager, InfoUserRepository $infoUserRepository, UtilisateurRepository $utilisateurRepository, ChevauxRepository $chevauxRepository, int $id): Response
    {
                
        // Récupère les infos liées à l'ID d'Utilisateur
        $utilisateur = $this->getUser();
        $user = $utilisateurRepository->find($id);
        
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
        
        // Chercher InfoUser par l'utilisateur, pas par son propre ID
        $infoUser = $infoUserRepository->findOneBy(['user' => $user]);
        
        // Si InfoUser n'existe pas, créer un objet vide pour éviter les erreurs
        if (!$infoUser) {
            $infoUser = new InfoUser();
            $infoUser->setUserId($user);
        }
        
        // dd($infoUser);
        if($utilisateur){
            $details = $entityManager->getRepository(Utilisateur::class)->findSingleUser(114);  
            $chevaux = $entityManager->getRepository(Chevaux::class)->ownerHorseById(114);         

            if($details == false){
                $empty_details = new InfoUser;
                

                $details = $empty_details;
                $empty_details->setNom('Vide');
                $empty_details->setPrenom('Vide');
                $empty_details->setVille('Vide');
                $empty_details->setRegion('Vide');
                $empty_details->setMiniature('/assets/img/thmb-user.png');

                $empty_chevaux = new Chevaux;
                $chevaux = $empty_chevaux;
                $empty_chevaux->setNom('Vide');
                $empty_chevaux->setRace('Vide');

                // var_dump($chevaux);
            }
            // return $details;
        }
        // var_dump($details);

        return $this->render('backoffice/profil.html.twig', [
            'user' => $infoUser,
            // 'chevaux' => $chevaux,
            'title_controller' => 'Mon profil',
            'btn_breakcrumb' => ''
        ]);
    }

    #[Route('/{id}/edit', name: 'app_profil_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, InfoUserRepository $infoUserRepository, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager, int $id): Response
    {
        $user = $utilisateurRepository->find($id);
        
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
        
        // Chercher InfoUser par l'utilisateur, pas par son propre ID
        $infoUser = $infoUserRepository->findOneBy(['user' => $user]);
        
        // Si InfoUser n'existe pas, le créer
        if (!$infoUser) {
            $infoUser = new InfoUser();
            $infoUser->setUserId($user);
            // Définir une miniature par défaut si elle n'existe pas
            if (!$infoUser->getMiniature()) {
                $infoUser->setMiniature('/assets/img/thmb-user.png');
            }
            $entityManager->persist($infoUser);
            $entityManager->flush();
        }

        $form = $this->createForm(InfoUserType::class, $infoUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_profil', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('info_user/edit.html.twig', [
            'user' => $infoUser,
            'title_controller' => 'Mon profil',
            'btn_breakcrumb' => 'app_backoffice',
            'form' => $form,
        ]);
    }

    #[Route('/register/info', name: 'app_info_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $infoUser = new InfoUser();
        $form = $this->createForm(InfoUserType::class, $infoUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($infoUser);
            $entityManager->flush();

            return $this->redirectToRoute('app_info_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('info_user/new.html.twig', [
            'info_user' => $infoUser,
            'form' => $form,
        ]);
    }


}
