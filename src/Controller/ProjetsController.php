<?php

namespace App\Controller;

use App\Entity\Documents;
use App\Entity\Projets;
use App\Form\DocumentsProjetType;
use App\Form\EtapesProjetType;
use App\Form\ProjetsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

#[Route('/projets', name: 'projets_')]
class ProjetsController extends AbstractController
{
	public function __construct(Security $security)
	{
		$this->security = $security;
		$this->user = $this->security->getUser();
	}

	#[Route('/', name: 'index', methods: ['GET'])]
	public function index(ManagerRegistry $doctrine): Response
	{
		if ($this->user && $this->user->isAdmin())
			$projets = $doctrine->getRepository(Projets::class)->findAll();
		else {
			$projets = $this->user->getClients()->getProjets();
		}

		$forms = array();
		$allDocumentsAreSubmitted = array();
		$allDocuments = $doctrine->getRepository(Documents::class)->findAll();

		foreach ($projets as $projet) {
			$forms['documents'][] = $this->createForm(DocumentsProjetType::class, $projet)->createView();
			$allDocumentsAreSubmitted[] = $projet->allDocumentsAreSubmitted($allDocuments);
			$forms['etapes'][] = $this->createForm(EtapesProjetType::class, $projet)->createView();
		}

		return $this->render('projets/index.html.twig', [
			'projets' => $projets,
			'forms' => $forms,
			'done' => $allDocumentsAreSubmitted
		]);
	}

	#[Route('/new', name: 'new', methods: ['GET', 'POST'])]
	public function new(Request $request, EntityManagerInterface $entityManager): Response
	{
		$projet = new Projets();
		$form = $this->createForm(ProjetsType::class, $projet);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$entityManager->persist($projet);
			$entityManager->flush();

			return $this->redirectToRoute('projets_index');
		}

		return $this->render('projets/new.html.twig', [
			'projet' => $projet,
			'form' => $form->createView(),
		]);
	}

	#[Route('/{id}', name: 'show', methods: ['GET'])]
	public function show(Projets $projet): Response
	{
		return $this->render('projets/show.html.twig', [
			'projet' => $projet,
		]);
	}

	#[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
	public function edit(Request $request, Projets $projet, EntityManagerInterface $entityManager): Response
	{
		$form = $this->createForm(ProjetsType::class, $projet);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$entityManager->flush();

			return $this->redirectToRoute('projets_index');
		}

		return $this->render('projets/edit.html.twig', [
			'projet' => $projet,
			'form' => $form->createView(),
		]);
	}

	#[Route('/{id}', name: 'delete', methods: ['POST', 'DELETE'])]
	public function delete(Request $request, Projets $projet, EntityManagerInterface $entityManager): Response
	{
		if ($this->isCsrfTokenValid('delete' . $projet->getId(), $request->request->get('_token'))) {
			$entityManager->remove($projet);
			$entityManager->flush();
		}

		return $this->redirectToRoute('projets_index');
	}
}
