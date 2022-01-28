<?php

// src/Service/FileUploader.php
namespace App\Service;

use App\Entity\Projets;
use Exception;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
	private $targetDirectory;
	private $slugger;

	public function __construct($targetDirectory, SluggerInterface $slugger)
	{
		$this->targetDirectory = $targetDirectory;
		$this->slugger = $slugger;
	}

	public function upload(UploadedFile $file, array $options)
	{
		$originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
		// this is needed to safely include the file name as part of the URL
		$safeFilename = $this->slugger->slug($options['filename']);
		$newFileName = $safeFilename . '.' . $file->guessExtension();

		// Move the file to the directory where brochures are stored
		try {
			$file->move(
				$this->getTargetDirectory() . $options['projet']->getDocumentsPath(),
				$newFileName
			);
		} catch (FileException $e) {
			throw $e;
		}

		// updates the 'filename' property to store the PDF file name
		// instead of its contents
		// $product->setfilename($newFilename);

		return $newFileName;
	}

	public function getTargetDirectory()
	{
		return $this->targetDirectory;
	}
}
