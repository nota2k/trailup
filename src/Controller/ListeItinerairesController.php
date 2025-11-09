<?php

namespace App\Controller;

use App\Entity\InfoUser;
use App\Repository\InfoUserRepository;

use App\Entity\Itineraires;
use App\Repository\ItinerairesRepository;

use App\Entity\Utilisateur;
use App\Form\ItinerairesType;

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
    public function index(Request $request,EntityManagerInterface $entityManager, ItinerairesRepository $itinerairesRepository): Response
    {
        $user = $this->getUser();
        
        $info_users = $entityManager->getRepository(InfoUser::class);
        $itineraires = $itinerairesRepository->findAll();

        $details = [];

        foreach($itineraires as $iti){
            $id_createur = $iti->getCreateur()->getId();
            
            $info_users->find($id_createur);
            if(!$info_users){
                $info_users = null;
            }

            $details[] = array(
                "itineraire" => $iti,
                "createur" => $info_users->find($id_createur)
            );

        }
        // dd($details);

        if($user){
            $user_itineraires = $user->getItineraires()->getValues();
        } else {
            $user_itineraires = null;
        }

        $request = Request::createFromGlobals();
        $path = $request->getPathInfo();

        $depart = $request->query->get('depart');
        $niveau = $request->query->get('niveau');
        $duree_min = intval($request->query->get('dureemin'));
        $duree_max = intval($request->query->get('dureemax'));
        $distance = intval($request->query->get('distance'));
        $keyword = $request->query->get('keyword');

        $param = [
            'depart' => $depart,
            'niveau' => $niveau,
            'duree_min' => '',
            'duree_max' => $duree_max,
            'distance' => $distance,
            'keyword' => $keyword
        ];


        if (isset($_GET['search'])) {

                if(!empty($depart) && $itineraires || !empty($filtered) ){ 
                    $filtered = $itinerairesRepository->findByDepart($depart);
                    $itineraires = array_intersect_key($filtered, $itineraires );
                    $param['depart'] = $depart;
                } 

                else if(!empty($niveau) && $itineraires || !empty($filtered) ){
                    $filtered = $itinerairesRepository->findByNiveau($niveau);
                    $itineraires = array_intersect_key($filtered, $itineraires );
                    $param['niveau'] = 'selected';
                    
                } 

                else if(!empty($distance) && $itineraires || !empty($filtered) ){
                    $filtered = $itinerairesRepository->findByDistance($distance);
                    $itineraires = array_intersect_key($filtered, $itineraires );
                    $param['distance'] = $distance;
                } 

                else if(!empty($duree_max) && $itineraires || !empty($filtered) ){
                    $duree_max = intval($duree_max);
                    $duree_min = intval($duree_min);
                    $filtered = $itinerairesRepository->findByDuree($duree_min,$duree_max); 
                    $itineraires = array_intersect_key($filtered, $itineraires );
                    $param['duree_max'] = $duree_max ;
                    $param['duree_min'] = $duree_min ;
                }

        }

        if(isset($_GET['reset'])){
            return $this->redirectToRoute('app_liste_annonces', [], Response::HTTP_SEE_OTHER);
        }       

        // dd($itineraires[0]->getUtilisateur()->getValues());

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
}
