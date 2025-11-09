<?php

namespace App\Controller\Backoffice\Chevaux;

use App\Entity\Chevaux;
use App\Form\ChevauxType;
use App\Repository\ChevauxRepository;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/chevaux')]
class ChevauxController extends AbstractController
{
    #[Route('/', name: 'app_chevaux_list', methods: ['GET'])]
    public function index(ChevauxRepository $chevauxRepository): Response
    {
        return $this->render('chevaux/index.html.twig', [
            'chevaux' => $chevauxRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_chevaux_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $chevaux = new Chevaux();
        $form = $this->createForm(ChevauxType::class, $chevaux);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($chevaux);
            $entityManager->flush();

            return $this->redirectToRoute('app_chevaux_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chevaux/new.html.twig', [
            'chevaux' => $chevaux,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chevaux_show', methods: ['GET'])]
    public function show(Chevaux $chevaux): Response
    {
        return $this->render('chevaux/show.html.twig', [
            'chevaux' => $chevaux,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_chevaux_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Chevaux $chevaux, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChevauxType::class, $chevaux);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_chevaux_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chevaux/edit.html.twig', [
            'chevaux' => $chevaux,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chevaux_delete', methods: ['POST'])]
    public function delete(Request $request, Chevaux $chevaux, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$chevaux->getId(), $request->request->get('_token'))) {
            $entityManager->remove($chevaux);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_chevaux_index', [], Response::HTTP_SEE_OTHER);
    }
}
