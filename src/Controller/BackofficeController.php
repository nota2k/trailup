<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use App\Entity\Chevaux;
use App\Repository\ChevauxRepository;

use App\Entity\InfoUser;
use App\Repository\InfoUserRepository;
use App\Repository\ItinerairesRepository;

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
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/backoffice')]
class BackofficeController extends AbstractController
{
    #[Route('/', name: 'app_backoffice')]
    public function index(
        EntityManagerInterface $entityManager,
        InfoUserRepository $infoUserRepository,
        ItinerairesRepository $itinerairesRepository,
        HttpClientInterface $httpClient
    ): Response {
        // check if the user is authenticated first
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $utilisateur = $this->getUser();
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
        
        // Récupérer les 3 derniers itinéraires
        $derniersItineraires = $itinerairesRepository->findLastThreeItineraires();
        
        // Récupérer la météo si la ville est renseignée
        $meteo = null;
        if ($infoUser && $infoUser->getVille()) {
            $meteo = $this->getWeatherData($httpClient, $infoUser->getVille());
        }
        
        return $this->render('backoffice/index.html.twig',[
            'user' => $infoUser,
            'derniersItineraires' => $derniersItineraires,
            'meteo' => $meteo,
            'title_controller' => 'Tableau de bord',
            'btn_breakcrumb' => 'app_backoffice'
        ]);
    }

    /**
     * Récupère les données météo pour une ville donnée via WeatherAPI
     */
    private function getWeatherData(HttpClientInterface $httpClient, string $ville): ?array
    {
        $apiKey = $_ENV['WEATHERAPI_KEY'] ?? 'fbaf44dbbffc4a40810190513250911';
        
        if (!$apiKey) {
            return null;
        }
        
        try {
            $response = $httpClient->request('GET', 'https://api.weatherapi.com/v1/current.json', [
                'query' => [
                    'key' => $apiKey,
                    'q' => $ville . ',France',
                    'lang' => 'fr',
                    'aqi' => 'no'
                ],
                'timeout' => 5
            ]);
            
            if ($response->getStatusCode() === 200) {
                $data = $response->toArray();
                $current = $data['current'];
                $location = $data['location'];
                
                return [
                    'temperature' => round($current['temp_c']),
                    'description' => ucfirst($current['condition']['text']),
                    'icon' => str_replace('64x64', '128x128', $current['condition']['icon']), // Utiliser une icône plus grande
                    'ville' => $location['name'],
                    'humidite' => $current['humidity'],
                    'vent' => round($current['wind_kph']) // Déjà en km/h
                ];
            }
        } catch (\Exception $e) {
            // En cas d'erreur, retourner null
            return null;
        }
        
        return null;
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
    public function edit(Request $request, InfoUserRepository $infoUserRepository, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager, SluggerInterface $slugger, int $id): Response
    {
        $user = $utilisateurRepository->find($id);
        
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
        
        // Chercher InfoUser par l'utilisateur avec une requête optimisée
        $infoUser = $infoUserRepository->createQueryBuilder('iu')
            ->where('iu.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
        
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
        } else {
            // S'assurer que toutes les propriétés sont chargées
            // Forcer le chargement en accédant aux propriétés
            $infoUser->getNom();
            $infoUser->getPrenom();
            $infoUser->getVille();
            $infoUser->getRegion();
            $infoUser->getMiniature();
        }

        $form = $this->createForm(InfoUserType::class, $infoUser);
        
        // Préremplir le champ email avec la valeur de l'utilisateur
        if ($infoUser->getUser() && $infoUser->getUser()->getEmail()) {
            $form->get('email')->setData($infoUser->getUser()->getEmail());
        }
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder l'email dans l'entité Utilisateur
            $email = $form->get('email')->getData();
            if ($email !== null && $infoUser->getUser()) {
                $infoUser->getUser()->setEmail($email);
            }
            
            /** @var UploadedFile $miniatureFile */
            $miniatureFile = $form->get('miniatureFile')->getData();
            
            // Si un fichier a été uploadé
            if ($miniatureFile) {
                $originalFilename = pathinfo($miniatureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$miniatureFile->guessExtension();
                
                // Déplacer le fichier vers le répertoire de stockage
                try {
                    $uploadDir = $this->getParameter('kernel.project_dir').'/public/uploads/profiles';
                    
                    // Créer le répertoire s'il n'existe pas
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    $miniatureFile->move($uploadDir, $newFilename);
                    
                    // Supprimer l'ancienne image si elle existe et n'est pas l'image par défaut
                    $oldMiniature = $infoUser->getMiniature();
                    if ($oldMiniature && $oldMiniature !== '/assets/img/thmb-user.png' && file_exists($this->getParameter('kernel.project_dir').'/public'.$oldMiniature)) {
                        unlink($this->getParameter('kernel.project_dir').'/public'.$oldMiniature);
                    }
                    
                    // Mettre à jour le chemin de la miniature
                    $infoUser->setMiniature('/uploads/profiles/'.$newFilename);
                } catch (FileException $e) {
                    // Gérer l'erreur si le fichier n'a pas pu être déplacé
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }
            }
            
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
