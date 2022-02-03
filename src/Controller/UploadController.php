<?php

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
	#[Route('/{id}', name: 'index', methods: ['GET', 'POST'])]
	public function index(ManagerRegistry $doctrine, EntityManagerInterface $entityManager, Request $request, Projets $projet, FileUploader $fileUploader): Response
	{
		$allDocuments = $doctrine->getRepository(Documents::class)->findAll();
		$notSubmittedDocument = $projet->getSubmittedDocuments($allDocuments, false);
		if (empty($notSubmittedDocument))
			return $this->redirectToRoute('projets_index');

		$form = $this->createForm(UploadType::class, [$projet, $notSubmittedDocument]);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			foreach ($notSubmittedDocument as $doc) {
				$brochureFile = $form->get($doc->getId())->getData();
				if ($brochureFile) {
					$brochureFileName = $fileUploader->upload($brochureFile, [
						'projet' => $projet,
						'filename' => $doc->getName()
					]);
					$projet->addDocument($doc);
				}
			}
			$entityManager->persist($projet);
			$entityManager->flush();

			return $this->render('projets/upload/success.html.twig', [
				'projet' => $projet,
				'submitted' => $projet->getSubmittedDocuments($allDocuments),
				'notSubmitted' => $projet->getSubmittedDocuments($allDocuments, false)
			]);
		}

		return $this->render('projets/upload/index.html.twig', [
			'projet' => $projet,
			'form' => $form->createView()
		]);
	}
}
