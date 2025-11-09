<?php

namespace App\Controller\Admin;

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

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;

#[Route('/admin')]
class AdminItinerairesController extends AbstractController
{
    #[Route('/', name: 'admin_itineraires',methods: ['GET', 'POST'])]
    public function index(EntityManagerInterface $entityManager, ItinerairesRepository $itinerairesRepository,InfoUserRepository $infoUserRepository): Response
    {
        $user = $this->getUser();
        $id = $user->getId();

        $infoUser = $infoUserRepository->find($id);

        $itineraires = $itinerairesRepository->findAll();
        $iti_count = count($itineraires);

        return $this->render('backoffice/admin/itineraires/index.html.twig', [
            'user' => $infoUser,
            'count' => $iti_count,
            'title_controller' => 'Tous les itinéraires ',
            'itineraires' => $itineraires,
            'btn_breakcrumb' => ''
        ]);
    }

    #[Route('/show/{id}', name: 'admin_itineraires_show', methods: ['GET'])]
    public function show(Itineraires $itineraire, InfoUserRepository $infoUserRepository,int $id): Response
    {
        $user = $this->getUser();
        $id = $user->getId();
        $infoUser = $infoUserRepository->find($id);

        return $this->render('backoffice/admin/itineraires/show.html.twig', [
            'user' => $infoUser,
            'itineraire' => $itineraire,
            'title_controller' => $itineraire->getTitre(),
            'btn_breakcrumb' => 'app_itineraires_new'
        ]);
    }

    #[Route('/delete/{id}', name: 'admin_itineraires_delete', methods: ['POST', 'GET'])]
    public function delete_admin(Request $request, Itineraires $itineraire, EntityManagerInterface $entityManager): Response
    {

        if ($this->isCsrfTokenValid('delete'.$itineraire->getId(), $request->request->get('_token'))) {
            $entityManager->remove($itineraire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_itineraires', [], Response::HTTP_SEE_OTHER);
    }
}
