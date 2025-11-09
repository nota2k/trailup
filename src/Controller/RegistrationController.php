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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\UserAuthenticator as AppUserAuthenticator;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                // Afficher les erreurs de validation pour le debug
                $errors = [];
                foreach ($form->getErrors(true) as $error) {
                    $errors[] = $error->getMessage();
                }
                // Ajouter un message flash pour afficher les erreurs
                foreach ($errors as $errorMessage) {
                    $this->addFlash('error', $errorMessage);
                }
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // encode the plain password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                // Sauvegarder d'abord l'utilisateur pour obtenir son ID
                $entityManager->persist($user);
                $entityManager->flush();

                $id = $user->getId();

                if (!$id) {
                    throw new \Exception('L\'ID de l\'utilisateur n\'a pas été généré');
                }

                // InfoUser sera créé dans la méthode info_register si nécessaire
                return $this->redirectToRoute('app_register_info', ['id' => $id]);
            } catch (\Exception $e) {
                // En cas d'erreur, ajouter un message d'erreur au formulaire
                $this->addFlash('error', 'Une erreur est survenue lors de l\'inscription : ' . $e->getMessage());
                // Pour le debug, vous pouvez décommenter la ligne suivante :
                // throw $e;
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/register/{id}', name: 'app_register_info', methods: ['GET', 'POST'])]
    public function info_register(
        Request $request, 
        UtilisateurRepository $utilisateurRepository, 
        InfoUserRepository $infoUserRepository, 
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $userAuthenticator,
        AppUserAuthenticator $authenticator,
        int $id
    ): Response
    {
        $user = $utilisateurRepository->find($id);
        
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        // Chercher InfoUser par l'utilisateur
        $infoUser = $infoUserRepository->findOneBy(['user' => $user]);
        
        // Si InfoUser n'existe pas encore, le créer
        if (!$infoUser) {
            $infoUser = new InfoUser();
            $infoUser->setUserId($user);
        }

        $form = $this->createForm(InfoUserType::class, $infoUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($infoUser);
            $entityManager->flush();

            // Connecter automatiquement l'utilisateur après l'inscription complète
            $response = $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
            
            // S'assurer que la redirection va vers le backoffice
            // Si l'authentification a réussi, on redirige vers le backoffice
            if ($response instanceof RedirectResponse) {
                // Vérifier le rôle et rediriger en conséquence
                $roles = $user->getRoles();
                if (in_array('ROLE_ADMIN', $roles)) {
                    return $this->redirectToRoute('admin_itineraires');
                }
                // Par défaut, redirection vers le backoffice
                return $this->redirectToRoute('app_backoffice');
            }
            
            return $response;
        }

        return $this->render('info_user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
