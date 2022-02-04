<?php

/**
 * Auteur : Johan Mickaël
 */

namespace App\Controller;

use App\Entity\Documents;
use App\Entity\Projets;
use App\Form\UploadType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('projets/upload', name: 'upload_')]
class UploadController extends AbstractController
{
	// Uploader les fichiers manquants du clients
	#[Route('/{id}', name: 'index', methods: ['GET', 'POST'])]
	public function index(ManagerRegistry $doctrine, EntityManagerInterface $entityManager, Request $request, Projets $projet, FileUploader $fileUploader): Response
	{
		// Tous les documents requis dans un projet
		$allDocuments = $doctrine->getRepository(Documents::class)->findAll();

		// Tous les documents qui ne sont pas encore validés / uploader par le client
		$notSubmittedDocument = $projet->_getDocuments($allDocuments, false);

		// Géneration du formulaire pour uploader tous les documents manquants / non validés du client
		$form = $this->createForm(UploadType::class, ['projet' => $projet, 'notSubmittedDocuments' => $notSubmittedDocument]);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			// Télechargement des documents manquants reliés au projet du client
			$projet->downloadDocuments($form, $notSubmittedDocument, $fileUploader);

			$entityManager->persist($projet);
			$entityManager->flush();

			// Redirection vers la page de récapitulation des fichiers télechargés
			return $this->render('projets/upload/success.html.twig', [
				// Le projet relié aux documents
				'projet' => $projet,
				// Tous les documents télechargés
				'submitted' => $projet->_getDocuments($allDocuments),
				// Tous les documents non télechargés
				'notSubmitted' => $projet->_getDocuments($allDocuments, false)
			]);
		}

		return $this->render('projets/upload/index.html.twig', [
			'projet' => $projet,
			'form' => $form->createView()
		]);
	}
}