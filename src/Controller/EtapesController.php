<?php

namespace App\Controller;

use App\Entity\Etapes;
use App\Form\EtapesType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/etapes', name:'etapes_')]
class EtapesController extends AbstractController
{
    #[Route('/', name:'index', methods:['GET'])]
    public function index(ManagerRegistry $doctrine): Response
    {
        $etapes = $doctrine->getRepository(Etapes::class)->findAll();

        return $this->render('etapes/index.html.twig', [
            'etapes' => $etapes,
        ]);
    }

    #[Route('/new', name:'new', methods:['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $etape = new Etapes();
        $form = $this->createForm(EtapesType::class, $etape);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($etape);
            $entityManager->flush();

            return $this->redirectToRoute('etapes_index');
        }

        return $this->render('etapes/new.html.twig', [
            'etape' => $etape,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name:'show', methods:['GET'])]
    public function show(Etapes $etape): Response
    {
        return $this->render('etapes/show.html.twig', [
            'etape' => $etape,
        ]);
    }

    #[Route('/{id}/edit', name:'edit', methods:['GET','POST'])]
    public function edit(Request $request, Etapes $etape, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EtapesType::class, $etape);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('etapes_index');
        }

        return $this->render('etapes/edit.html.twig', [
            'etape' => $etape,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name:'delete', methods:['POST', 'DELETE'])]
    public function delete(Request $request, Etapes $etape, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$etape->getId(), $request->request->get('_token'))) {
            $entityManager->remove($etape);
            $entityManager->flush();
        }

        return $this->redirectToRoute('etapes_index');
    }
}
