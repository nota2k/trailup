<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\UserAuthenticator as AppUserAuthenticator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class OAuthController extends AbstractController
{
    #[Route('/connect/google', name: 'app_oauth_google')]
    public function connectGoogle(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect(['openid', 'profile', 'email']);
    }

    #[Route('/connect/google/check', name: 'app_oauth_google_check')]
    public function connectGoogleCheck(
        Request $request,
        ClientRegistry $clientRegistry,
        UtilisateurRepository $utilisateurRepository,
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $userAuthenticator,
        AppUserAuthenticator $authenticator,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // Si l'utilisateur est déjà connecté, rediriger
        if ($this->getUser()) {
            return $this->redirectToRoute('app_backoffice');
        }

        $client = $clientRegistry->getClient('google');
        
        try {
            // Récupérer le token d'accès
            $accessToken = $client->getAccessToken();
            
            // Récupérer les informations de l'utilisateur Google
            /** @var GoogleUser $googleUser */
            $googleUser = $client->fetchUserFromToken($accessToken);
            
            $email = $googleUser->getEmail();
            $googleId = $googleUser->getId();
            $firstName = $googleUser->getFirstName();
            $lastName = $googleUser->getLastName();
            $name = $googleUser->getName();
            
            // Chercher un utilisateur existant par Google ID
            $user = $utilisateurRepository->findOneBy(['googleId' => $googleId]);
            
            // Si l'utilisateur n'existe pas, chercher par email
            if (!$user && $email) {
                $user = $utilisateurRepository->findOneBy(['email' => $email]);
            }
            
            // Si l'utilisateur n'existe toujours pas, créer un nouveau compte
            if (!$user) {
                $user = new Utilisateur();
                $user->setGoogleId($googleId);
                $user->setEmail($email);
                
                // Générer un nom d'utilisateur unique à partir du nom ou de l'email
                $username = $name ?: ($firstName . ' ' . $lastName) ?: explode('@', $email)[0];
                $baseUsername = $username;
                $counter = 1;
                
                // S'assurer que le nom d'utilisateur est unique
                while ($utilisateurRepository->findOneBy(['username' => $username])) {
                    $username = $baseUsername . $counter;
                    $counter++;
                }
                
                $user->setUsername($username);
                
                // Générer un mot de passe aléatoire (même si non utilisé pour OAuth)
                $randomPassword = bin2hex(random_bytes(16));
                $user->setPassword($passwordHasher->hashPassword($user, $randomPassword));
                
                $user->setRoles(['ROLE_USER']);
                
                $entityManager->persist($user);
                $entityManager->flush();
            } else {
                // Si l'utilisateur existe mais n'a pas de Google ID, l'ajouter
                if (!$user->getGoogleId()) {
                    $user->setGoogleId($googleId);
                    if (!$user->getEmail() && $email) {
                        $user->setEmail($email);
                    }
                    $entityManager->flush();
                }
            }
            
            // Connecter automatiquement l'utilisateur
            $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
            
            return $this->redirectToRoute('app_backoffice');
            
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la connexion avec Google : ' . $e->getMessage());
            return $this->redirectToRoute('app_login');
        }
    }
}

