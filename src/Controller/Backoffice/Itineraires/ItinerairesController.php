<?php

namespace App\Controller\Backoffice\Itineraires;

use App\Entity\Utilisateur;
use App\Entity\InfoUser;
use App\Repository\InfoUserRepository;

use App\Entity\Itineraires;
use App\Form\ItinerairesType;
use App\Repository\ItinerairesRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Serializer\SerializerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/itineraires')]
class ItinerairesController extends AbstractController
{
    #[Route('/', name: 'app_itineraires_liste', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, ItinerairesRepository $itinerairesRepository,InfoUserRepository $infoUserRepository): Response
    {
        $user = $this->getUser();
        $id = $user->getId();
        $infoUser = $infoUserRepository->find($id);
        
        $itineraires = $user->getItineraires()->getValues();

        return $this->render('backoffice/itineraires/index.html.twig', [
            'title_controller' => 'Tous mes itinéraires',
            'user' => $infoUser,
            'itineraires' => $itineraires,
            'btn_breakcrumb' => 'app_itineraires_new'
        ]);
    }

    #[Route('/new', name: 'app_itineraires_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,InfoUserRepository $infoUserRepository): Response
    {
        $user = $this->getUser();
        $id = $user->getId();
        $infoUser = $infoUserRepository->find($id);

        $itineraire = new Itineraires();
        $form = $this->createForm(ItinerairesType::class, $itineraire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $itineraire->addUtilisateur($user);
            $entityManager->persist($itineraire);
            $entityManager->flush();

            return $this->redirectToRoute('app_itineraires_liste', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/itineraires/new.html.twig', [
            'user' => $infoUser,
            'title_controller' => 'Nouvel itinéraire',
            'itineraire' => $itineraire,
            'form' => $form->createView(),
            'btn_breakcrumb' => 'app_itineraires_new'
        ]);
    }

    #[Route('/show/{id}', name: 'app_itineraires_show', methods: ['GET'])]
    public function show(Itineraires $itineraire, InfoUserRepository $infoUserRepository,int $id): Response
    {
        $user = $this->getUser();
        $id = $user->getId();
        $infoUser = $infoUserRepository->find($id);
        // dd($itineraire);

        return $this->render('backoffice/itineraires/show.html.twig', [
            'user' => $infoUser,
            'itineraire' => $itineraire,
            'title_controller' => $itineraire->getTitre(),
            'btn_breakcrumb' => ''
        ]);
    }

    #[Route('/{id}/edit', name: 'app_itineraires_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Itineraires $itineraire, EntityManagerInterface $entityManager,InfoUserRepository $infoUserRepository): Response
    {
        $user = $this->getUser();
        $id = $user->getId();
        $infoUser = $infoUserRepository->find($id);

        $form = $this->createForm(ItinerairesType::class, $itineraire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_itineraires_liste', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/itineraires/edit.html.twig', [
            'user' => $infoUser,
            'itineraire' => $itineraire,
            'form' => $form,
            'title_controller' => $itineraire->getTitre(),
            'btn_breakcrumb' => ''
        ]);
    }

    #[Route('/delete/{id}', name: 'app_itineraires_delete', methods: ['POST', 'GET'])]
    public function delete(Request $request, Itineraires $itineraire, EntityManagerInterface $entityManager): Response
    {

        if ($this->isCsrfTokenValid('delete'.$itineraire->getId(), $request->request->get('_token'))) {
            $entityManager->remove($itineraire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_itineraires_liste', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/published', name: 'app_itineraires_published', methods: ['GET'])]
    public function published(EntityManagerInterface $entityManager, ItinerairesRepository $itinerairesRepository,InfoUserRepository $infoUserRepository): Response
    {
        $user = $this->getUser();

        $id = $user->getId();
        $infoUser = $infoUserRepository->find($id);
        
        $itineraires = $user->getItineraires()->getValues();
        if($itineraires[0]->getPublie() === false){
            $itineraires = null;
        } else{
            $itineraires;
        }

        return $this->render('backoffice/itineraires/index.html.twig', [
            'title_controller' => 'Mes itinéraires',
            'user' => $infoUser,
            'itineraires' => $itineraires,
            'btn_breakcrumb' => 'app_itineraires_new'
        ]);
    }

    #[Route('/saved', name: 'app_itineraires_saved', methods: ['GET'])]
    public function itineraires_saved(Request $request, Itineraires $itineraire, EntityManagerInterface $entityManager): Response
    {

        if ($this->isCsrfTokenValid('delete'.$itineraire->getId(), $request->request->get('_token'))) {
            $entityManager->remove($itineraire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_itineraires_liste', [], Response::HTTP_SEE_OTHER);
    }
}
