<?php

/**
 * Auteur: Johan Mickaël
 */

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
	// Initialisation des attributs de classe
	public function __construct(Security $security)
	{
		$this->security = $security;
		// $this->user est l'utilisateur authentifié sur l'application
		$this->user = $this->security->getUser();
	}

	// Dashboard des projets
	#[Route('/', name: 'index', methods: ['GET'])]
	public function index(ManagerRegistry $doctrine): Response
	{
		if ($this->user && $this->user->isEmployee())
			// Si l'utilisateur n'est pas un client on liste tous les projets des clients
			$projets = $doctrine->getRepository(Projets::class)->findAll();
		else {
			// Si l'utilisateur est un client on liste seulement ses projets
			$projets = $this->user->getClients()->getProjets();
		}

		// $forms désigne la variable pour stocker multiples formulaires qui va génerer l'affichage de l'état du projet du client
		$forms = array();

		// $allDocumentsAreSubmitted liste de booléen pour verifier si le client a déjà uploader ses documents ou pas
		$allDocumentsAreSubmitted = array();

		// $allDocuments : tous les documents nécessaires
		$allDocuments = $doctrine->getRepository(Documents::class)->findAll();

		// Géneration de l'affichage des étapes validés et des documents validés pour chaque projet
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

	// Création d'un nouveau projet
	#[Route('/new', name: 'new', methods: ['GET', 'POST'])]
	public function new(Request $request, EntityManagerInterface $entityManager): Response
	{
		$projet = new Projets();
		$form = $this->createForm(ProjetsType::class, $projet);

		try {
			$form->handleRequest($request); // Traitement et validation des requêtes
		} catch (\Throwable $th) {
			/**
			 * Lève une exception en cas de données no conformes entrées par l'utilisateur
			 * Ex: La date de fin du projet doit être supérieure à la date du début
			 * Renvoi du message d'erreur vers le formulaire d'ajout de projet
			 */
			$this->addFlash('message', $th->getMessage());
			return $this->render('projets/new.html.twig', [
				'projet' => $projet,
				'form' => $form->createView(),
			]);
		}

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

	// Détailler un projet
	#[Route('/{id}', name: 'show', methods: ['GET'])]
	public function show(Projets $projet): Response
	{
		// Géneration de l'affichage pour afficher les documents déjà transmis par le propriétaire du projet
		$form['documents'] = $this->createForm(DocumentsProjetType::class, $projet)->createView();

		// Géneration de l'affichage pour afficher les documents déjà transmis par le propriétaire du projet
		$form['etapes'] = $this->createForm(EtapesProjetType::class, $projet)->createView();

		return $this->render('projets/show.html.twig', compact('projet', 'form'));
	}

	// Modifier un projet
	#[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
	public function edit(Request $request, Projets $projet, EntityManagerInterface $entityManager): Response
	{
		$form = $this->createForm(ProjetsType::class, $projet);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$entityManager->flush();
			return $this->redirectToRoute('projets_edit', ['id' => $projet->getId()]);
		}

		return $this->render('projets/edit.html.twig', [
			'projet' => $projet,
			'form' => $form->createView(),
		]);
	}

	// Supression d'un projet
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
