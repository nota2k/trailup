<?php

namespace App\Controller\Backoffice\Itineraires;

use App\Entity\InfoItineraire;
use App\Form\InfoItineraireType;
use App\Repository\InfoItineraireRepository;

use App\Entity\Utilisateur;
use App\Entity\InfoUser;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/info/itineraire')]
class InfoItineraireController extends AbstractController
{
    #[Route('/', name: 'app_info_itineraire_index', methods: ['GET'])]
    public function index(InfoItineraireRepository $infoItineraireRepository): Response
    {
        return $this->render('backoffice/itineraires/info_itineraire/index.html.twig', [
            'info_itineraires' => $infoItineraireRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_info_itineraire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $infoItineraire = new InfoItineraire();
        $form = $this->createForm(InfoItineraireType::class, $infoItineraire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($infoItineraire);
            $entityManager->flush();

            return $this->redirectToRoute('app_info_itineraire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/itineraires/info_itineraire/new.html.twig', [
            'info_itineraire' => $infoItineraire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_info_itineraire_show', methods: ['GET'])]
    public function show(InfoItineraire $infoItineraire): Response
    {
        return $this->render('backoffice/itineraires/info_itineraire/show.html.twig', [
            'info_itineraire' => $infoItineraire,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_info_itineraire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, InfoItineraire $infoItineraire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InfoItineraireType::class, $infoItineraire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_info_itineraire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/itineraires/info_itineraire/edit.html.twig', [
            'info_itineraire' => $infoItineraire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_info_itineraire_delete', methods: ['POST'])]
    public function delete(Request $request, InfoItineraire $infoItineraire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$infoItineraire->getId(), $request->request->get('_token'))) {
            $entityManager->remove($infoItineraire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_info_itineraire_index', [], Response::HTTP_SEE_OTHER);
    }
}
