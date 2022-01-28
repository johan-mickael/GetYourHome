<?php

namespace App\Controller;

use App\Entity\Documents;
use App\Entity\Projets;
use App\Form\UploadType;
use App\Service\FileUploader;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('projets/upload', name: 'upload_')]
class UploadController extends AbstractController
{
	#[Route('/{id}', name: 'index', methods: ['GET', 'POST'])]
	public function index(ManagerRegistry $doctrine, Request $request, Projets $projet, FileUploader $fileUploader): Response
	{
		$allDocuments = $doctrine->getRepository(Documents::class)->findAll();
		$form = $this->createForm(UploadType::class, [$projet, $allDocuments]);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$brochureFile = $form->get('test')->getData();
			if ($brochureFile) {
				$brochureFileName = $fileUploader->upload($brochureFile, $projet);
				// $product->setBrochureFilename($brochureFileName);
			}
			return $this->redirectToRoute('upload_index', ['id' => $projet->getId()]);
		}

		return $this->render('projets/upload/index.html.twig', [
			'projet' => $projet,
			'form' => $form->createView()
		]);
	}
}
