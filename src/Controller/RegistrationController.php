<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\InfoUser;


use App\Form\RegistrationFormType;
use App\Form\InfoUserType;
use App\Repository\InfoUserRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Utilisateur();
        $info_user = new InfoUser();
        $info_user->setUserId($user);
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            
            
            $entityManager->persist($user);
            $entityManager->persist($info_user);
            $entityManager->flush();

            $id = $user->getId();
            // dd($user);

            return $this->redirectToRoute('app_register_info', ['id' => $id]);
            // do anything else you need here, like send an email
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/register/{id}', name: 'app_register_info', methods: ['GET', 'POST'])]
    public function info_register(Request $request, UtilisateurRepository $utilisateurRepository, InfoUserRepository $infoUserRepository, EntityManagerInterface $entityManager, int $id): Response
    {

        $user = $utilisateurRepository->find($id);
        $infoUser = $infoUserRepository->find($id);

        // dd($user);
        $form = $this->createForm(InfoUserType::class, $infoUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($infoUser);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_profil', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('info_user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
