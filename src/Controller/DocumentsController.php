<?php
/**
 * Auteur : Johan Mickaël
 */
namespace App\Controller;

use App\Entity\Documents;
use App\Form\DocumentsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/documents', name:'documents_')]
class DocumentsController extends AbstractController
{
	// Liste des documents
    #[Route('/', name:'index', methods:['GET'])]
    public function index(ManagerRegistry $doctrine): Response
    {
        $documents = $doctrine->getRepository(Documents::class)->findAll();
        return $this->render('documents/index.html.twig', [
            'documents' => $documents,
        ]);
    }

	// Ajouter un document
    #[Route('/new', name:'new', methods:['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $document = new Documents();
        $form = $this->createForm(DocumentsType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($document);
            $entityManager->flush();

            return $this->redirectToRoute('documents_index');
        }

        return $this->render('documents/new.html.twig', [
            'document' => $document,
            'form' => $form->createView(),
        ]);
    }

	// Afficher un document
    #[Route('/{id}', name:'show', methods:['GET'])]
    public function show(Documents $document): Response
    {
        return $this->render('documents/show.html.twig', [
            'document' => $document,
        ]);
    }

	// Modifier un document
    #[Route('/{id}/edit', name:'edit', methods:['GET','POST'])]
    public function edit(Request $request, Documents $document, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DocumentsType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('documents_index');
        }

        return $this->render('documents/edit.html.twig', [
            'document' => $document,
            'form' => $form->createView(),
        ]);
    }

	// Supprimer un document
    #[Route('/{id}', name:'delete', methods:['POST', 'DELETE'])]
    public function delete(Request $request, Documents $document, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$document->getId(), $request->request->get('_token'))) {
            $entityManager->remove($document);
            $entityManager->flush();
        }

        return $this->redirectToRoute('documents_index');
    }
}
